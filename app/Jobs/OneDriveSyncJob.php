<?php

namespace App\Jobs;

use App\TokenStore\TokenCache;
use App\Repositories\ZohoCrmApi;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Graph;
use zcrmsdk\crm\crud\ZCRMAttachment;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;

class OneDriveSyncJob extends Job
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
        $this->zohoRecord = ZCRMRecord::getInstance($this->data['module'], $this->data['id']);

        // upload files to OneDrive
        $attachments = $this->getAttachmentsFromZoho();
        foreach ($attachments as $attachment) {
            $filepath = $this->downloadAttachmentFromZoho($attachment);
            $this->uploadAttachmentToOneDrive($filepath);

            unlink($filepath);
            $this->zohoRecord->deleteAttachment($attachment->getId());
        }

        // create folder on OneDrive
        $this->setFieldsOnZoho();
    }

    protected function getAttachmentsFromZoho(): array
    {
        $attachments = [];

        try {
            $attachments = $this->zohoRecord->getAttachments()->getData();
        } catch (ZCRMException $e) {
            Log::warning($e->getMessage().' No attached files found');
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

        Log::info("OneDrive :: File '{$file->getFileName()}' was downloaded from Zoho.");

        return $filepath;
    }

    protected function uploadAttachmentToOneDrive($filepath)
    {
        $graph = new Graph();
        $tokenCache = new TokenCache();
        $graph->setAccessToken($tokenCache->getAccessToken($this->user));

        $pathInfo = pathinfo($filepath);
        $onedrivePathname = preg_replace('/(\/+)/', '/', "/Apps/Zoho CRM/{$this->data['directory']}/{$pathInfo['basename']}");

        // upload a file to OneDrive
        $graphUploadedResponse = $graph
            ->createRequest('PUT', "/me/drive/root:$onedrivePathname:/content")
            ->upload($filepath);

        Log::info("OneDrive :: File '{$pathInfo['basename']}' was uploaded to OneDrive.");
    }

    protected function setFieldsOnZoho()
    {
        $graph = new Graph();
        $tokenCache = new TokenCache();
        $graph->setAccessToken($tokenCache->getAccessToken($this->user));

        // get a folder meta data
        $onedriveFolder =   preg_replace('/(\/+)/', '/', "/Apps/Zoho CRM/{$this->data['directory']}");
        try {
	    $folderMeta = $graph->createRequest('GET', "/me/drive/root:{$onedriveFolder}")->execute()->getBody();
	} catch(\Exception $e) {
            // do nothing as the target folder doesn't exist
            return;
	}

        $this->zohoRecord->setFieldValue('onedriveextension__OneDrive_Folder_ID', $folderMeta['id']);
        $this->zohoRecord->update();
    }
}
