<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once 'vendor/autoload.php';
//should delete file after processing ?
define('DELETE_FILE', true);

use app\Core;

$app = (new Core())->setAccessToken('1rh702VOxJAAAAAAAAAAEp3COMY3P5KyGN4GjPtEXSlzYjJpfAwi9jWa8wXCmIA9')
//$app = (new Core())->setAccessToken('_qD7bKlTQ8AAAAAAAAAAEz0gKIxyYPQlurZeKyFhoARcuxmKl6g3gApB0HXxMpGx')
	->setClientKey('mbfew28n7vffi5o')
	->setClientSecret('x79mnr03rn2lc3f')
    //->setClientKey('eezjpyurptgcfyv')
    //->setClientSecret('23fukol3esu7bnp')

    ->setDirectoryOnServer(dirname(__FILE__) . '/files/')
	//->setZohoKey('811e0af4e080c0137558eb192f054051') //old sys
    ->setZohoKey('745f187de32cc1c2027ff0e52849f528')
	->init();


$app->addListener([
	['parameter' => 'module', 'required' => true],
	['parameter' => 'id', 'required' => true],
	['parameter' => 'directory', 'required' => false, 'default' => null],
    ['parameter' => 'baseroot', 'required' => false, 'default' => null],
], function ($parameter) {
    
	/** @var Core $this */
	if ($this->request($this->getMethodLink('json', $parameter['module'], 'getRecordById', true, [
		'scope' => 'crmapi',
		'id' => $parameter['id'],
		'newFormat' => 1
	]), [], function ($response) use ($parameter) {
        print_r($parameter);
		$module = $parameter['module'];
		$id = $parameter['id'];
		/** @var Core $this */
		if ($moduleItems = $this->getItemsParameters($this->processResponse($response, $module))) {
			Core::$calls += count($moduleItems);
			$moduleItem = current($moduleItems);
			$moduleId = current($moduleItem);
			if (!empty($parameter['directory'])) {
				$directory_name = $parameter['directory'];
			} elseif (!empty($moduleItem['DropBox Folder Path'])) {
				$directory_name = $moduleItem['DropBox Folder Path'];
			} else {
				$directory_name = "{$moduleItem['First Name']} {$moduleItem['Last Name']} {$moduleId}";
			}
            if( !empty($parameter['baseroot']))$directory_name=$parameter['baseroot'].$directory_name;			

			//getting related attachments
			$attachments = $this->getItemsParameters($this->processResponse($this->request($this->getMethodLink('json', 'Attachments', 'getRelatedRecords', true, [
				'scope' => 'crmapi',
				'parentModule' => $module,
				'id' => current($moduleItem),
				'newFormat' => 1
			])), 'Attachments'));
			//if attachments exists go further
			if (!empty($attachments) && is_array($attachments)) {
				//get directory name
				//if folder not exists on server - creating folder
				if (!($fileDATA = $this->isDropboxEntityExists($directory_name))) {
					//if created success - updating zoho records (guess we can do it in bulk after but if script will die - we will lose some of data)
					if ($dbxFolder = $this->createFolder($directory_name)) {
						//create shared link
						if (($sharedLink = $this->_dbx->createSharedLinkWithOptions($this->formatPath($directory_name))) && !empty($sharedLink->getDataProperty('url'))) {
							//update zoho record
							$result = $this->updateRecordsZoho($module, $this->asXML([[
								'Dropbox Folder ID' => $dbxFolder->id,
								'DropBox Folder Path' => $directory_name,
								'Dropbox Folder Web Link' => $sharedLink->getDataProperty('url')
							]], $module), $moduleId);
							$this::debug('Zoho update ' . $module, $this::json_decode($result, true));
						}
					}
				} else {
					//$this->deleteFolder($directory_name); //For debug purpose
					$this::debug('Folder exists on dropbox already: ', $directory_name);
				}
				foreach ($attachments as $attachment) {
					if (($file_stream = $this->request($this->getMethodLink('json', $module, 'downloadFile', true, [
							'scope' => 'crmapi',
							'parentModule' => $module,
							'id' => $attachment['id'],
							'newFormat' => 1
						]))) && !empty($file_stream)) {
						//downloading file to server
						$filepath = $this->putFile($directory_name, $attachment['File Name'], $file_stream);
						//if file not exists on server - uploading file
						if (!$this->isDropboxEntityExists($this->implodePathFile($directory_name, $attachment['File Name']))) {
							$dbxFile = $this->createDropboxFile($filepath);
							$dbxUploadedFile = $this->uploadFile($dbxFile, $this->implodePathFile($directory_name, $attachment['File Name']));
							$this::debug('File uploaded to dropbox: ', (array)$dbxUploadedFile);
						} else {
							$this::debug('File exists on dropbox already: ', $this->implodePathFile($directory_name, $attachment['File Name']));
						}
					}
					unset($dbxFile);
					$this->deleteFile($filepath);
					if (DELETE_FILE) {
						$result = $this->deleteFileZoho($module, $attachment['id']);
						$this::debug('Zoho file deleted: ', $result);
					}
				}
				$this->removeDirectory($this->getServerDirectory($directory_name));
			}
		} else {
			return false;
		}
	})) ;
})->listen();

Core::debug('Total: ', Core::$calls);



