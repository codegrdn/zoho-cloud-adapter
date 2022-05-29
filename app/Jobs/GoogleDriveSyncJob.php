<?php

namespace App\Jobs;

use Google_Service_Drive_DriveFile;
use Google_Service_Drive;
use App\Repositories\GoogleDriveWrapper;
use App\Repositories\ZohoCrmApi;
use Illuminate\Support\Facades\Log;
use zcrmsdk\crm\crud\ZCRMAttachment;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;

class GoogleDriveSyncJob extends Job
{
    private $data;
    private $user;
    private $zohoRecord;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function handle()
    {
        ZohoCrmApi::initialize($this->user);
        $this->zohoRecord = ZCRMRecord::getInstance(
            $this->data['module'],
            $this->data['id']
        );

        foreach ($this->getAttachmentsFromZoho() as $attachment) {
            $filepath = $this->downloadAttachmentFromZoho($attachment);
            $this->uploadAttachmentToGoogleDrive($filepath);

            unlink($filepath);
            $this->zohoRecord->deleteAttachment($attachment->getId());
        }
    }

    protected function getAttachmentsFromZoho(): array
    {
        $attachments = [];

        try {
            $attachments = $this->zohoRecord->getAttachments()->getData();
        } catch (ZCRMException $e) {
            Log::debug($e->getMessage().' No attached files found');
        }

        return $attachments;
    }

    protected function downloadAttachmentFromZoho(ZCRMAttachment $attachment): string
    {
        $file = $this->zohoRecord->downloadAttachment($attachment->getId());
        $filepath = storage_path('files/'.$file->getFileName());

        $fp = fopen($filepath, "w");
        $stream = $file->getFileContent();

        fputs($fp, $stream);
        fclose($fp);

        return $filepath;
    }

    protected function uploadAttachmentToGoogleDrive($filepath): void
    {
        $client = GoogleDriveWrapper::initClient($this->user);
        $service = new Google_Service_Drive($client);

        $targetDir = preg_replace(
            '/(\/+)/',
            '/',
            '/' . $this->data['baseroot'] . '/' . $this->data['directory']
        );

        $dirId = '';
        foreach (explode('/', $targetDir) as $dir) {

            if (empty($dir)) {
                continue;
            }

            /* @see https://developers.google.com/drive/api/v3/reference/query-ref */
            $file = new Google_Service_Drive_DriveFile();
            $list = $service->files->listFiles(array(
                'q' => "name='{$dir}' and trashed=false and mimeType='application/vnd.google-apps.folder'"
            ));

            if (!$list['files']) {
                // create dir
                $file = new Google_Service_Drive_DriveFile();
                $file->setName($dir);
                if ($dirId) {
                    $file->setParents(array($dirId));
                }
                $file->setMimeType('application/vnd.google-apps.folder');
                $newDir = $service->files->create($file);

                $dirId = $newDir->getId();

                $userPermission = new \Google_Service_Drive_Permission(array(
                    'type' => 'anyone',
                    'role' => 'reader'
                ));

                $service->permissions->create(
                    $dirId, $userPermission, array('fields' => 'id')
                );
            } else {
                $dirId = $list['files'][0]->getId();
            }
        }

        // upload file
        $file = new Google_Service_Drive_DriveFile();
        $file->setName(basename($filepath));
        $file->setParents(array($dirId));
        $fileContent = file_get_contents($filepath);
        $service->files->create($file, array(
            'data' => $fileContent
        ));

        // https://drive.google.com/drive/folders/1NuOGNVUVMLceJOnGpce5wRxr4kdOCyzs?usp=sharing
        $this->zohoRecord->setFieldValue('gdriveextension__Drive_Folder_ID',$dirId);
        $this->zohoRecord->setFieldValue('gdriveextension__Drive_URL', "https://drive.google.com/drive/folders/{$dirId}?usp=sharing");
        $this->zohoRecord->update();
    }
}
