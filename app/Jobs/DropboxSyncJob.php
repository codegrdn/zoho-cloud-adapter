<?php


namespace App\Jobs;

use App\Repositories\ZohoCrmApi;
use Mockery\CountValidator\Exception;
use zcrmsdk\crm\crud\ZCRMRecord;
use App\Repositories\EmailNotification;
use App\Dropbox\Classes\Core;
use zcrmsdk\crm\exception\ZCRMException;


class DropboxSyncJob extends Job
{
    private $data;
    private $user;
    public $tries = 1;
    public $timeout = 120;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        $data = $this->data['data'];
        $userSettings = json_decode($user->settings, true);

        ZohoCrmApi::initialize($user);

        $app = (new Core())
            ->setAccessToken($user->storage_token)
            ->init();

        $listeners = [
            ['parameter' => 'module', 'required' => true],
            ['parameter' => 'id', 'required' => true],
            ['parameter' => 'directory', 'required' => false, 'default' => null],
            ['parameter' => 'baseroot', 'required' => false, 'default' => null],
        ];

        $app->addListener($listeners, function ($parameter) {
            $module = (!empty($parameter['module'])) ? $parameter['module'] : "Leads";
            $record = ZCRMRecord::getInstance($module, $parameter['id']);

            try {
                $attachments = $record->getAttachments()->getData();
            } catch (ZCRMException $e) {
                throw new \Exception("DROPBOX : Lead {$parameter['id']} has no attachments. {$e->getMessage()}");
            }

            if (!empty($parameter['directory'])) {
                $directory_name = $parameter['directory'];
            } elseif (!empty($moduleItem['DropBox Folder Path'])) {
                $directory_name = $moduleItem['DropBox Folder Path'];
            } else {
                $directory_name = $parameter['id'];
            }

            if (!empty($parameter['baseroot'])) {
                $directory_name = $parameter['baseroot'].'/'.$directory_name;
            }

            try {
                if (!$dbxFolder = $this->isDropboxEntityExists($directory_name)) {
                    $dbxFolder = $this->createFolder($directory_name);
                }

                $sharedLinks = $this->_dbx->listSharedLinks($this->formatPath($directory_name))->getDataProperty('links');
                $dbxSharedLink = empty($sharedLinks)
                    ? $this->_dbx->createSharedLinkWithOptions($this->formatPath($directory_name))->getDataProperty('url')
                    : $sharedLinks[0]['url'];
            } catch (\Exception $e) {
                throw new Exception("DROPBOX : Fail to create {$directory_name} on Dropbox. {$e->getMessage()}");
            }

            try {
                $record->setFieldValue('dropboxextension__Dropbox_Folder_ID', $dbxFolder->id);
                $record->setFieldValue('DropBox_Folder_Path', $directory_name);
                $record->setFieldValue('Dropbox_Folder_Web_Link', $dbxSharedLink);

                $record->update();
            } catch (\Exception $e) {
                throw new Exception("DROPBOX : Fail to update dropbox field on Zoho. {$e->getMessage()}");
            }

            foreach ($attachments as $attachment) {
                $fileResponseIns = $record->downloadAttachment($attachment->getId());
                $filepath = storage_path('files/'.$fileResponseIns->getFileName());

                $fp = fopen($filepath, "w");
                $stream = $fileResponseIns->getFileContent();
                fputs($fp, $stream);
                fclose($fp);

                if ( !$this->isDropboxEntityExists($this->implodePathFile($directory_name, $fileResponseIns->getFileName())) ) {
                    $dbxFile = $this->createDropboxFile($filepath);
                    $this->uploadFile($dbxFile, $this->implodePathFile($directory_name, $fileResponseIns->getFileName()));
                }

                $record->deleteAttachment($attachment->getId());
                unlink($filepath);
            }

        })->listen();
    }
}
