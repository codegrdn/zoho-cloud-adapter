<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 06.09.2017
 * Time: 14:55
 */

namespace App\Dropbox\Classes\Traits;

use GuzzleHttp\Exception\ClientException;
use Kunnu\Dropbox\DropboxApp;
use App\Dropbox\Classes\Models\eDropbox;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
/** @property eDropbox $_dbx  */

trait DropboxTrait 
{
	private $access_token;
	private $client_key;
	private $client_secret;
	public $_dbx;
	private $app;


	public function setAccessToken($token) {
		$this->access_token = $token;
		return $this;
	}

	public function setClientKey($token) {
		$this->client_key = $token;
		return $this;
	}

	public function setClientSecret($token) {
		$this->client_secret = $token;
		return $this;
	}

	public function init() {
		if ($this->access_token) {
			$this->app = new DropboxApp($this->client_key, $this->client_secret, $this->access_token);
		} else {
			$this->app = new DropboxApp($this->client_key, $this->client_secret);
		}
		$this->_dbx = new eDropbox($this->app);
		return $this;
	}

	public function uploadFile($dbFile, $filename, $options = ["autorename" => true]) {
		return $this->_dbx->simpleUpload($dbFile, $this->formatPath($filename), $options);
	}

	public function uploadFileChunked($dbFile, $filename, $fileSize = 400000000, $options = ["authorename" => true]) {
		return $this->_dbx->uploadChunked($dbFile, $this->formatPath($filename), $fileSize, $fileSize / 4, $options);
	}

	public function isDropboxFileExists($directory, $filename) {
		$searchResults = $this->_dbx->search($directory, $filename, ['start' => 0, 'max_results' => 1]);
		return $searchResults;
	}

	public function createDropboxFile($file) {
		if (is_file($this->formatPath($file))) {
			return new DropboxFile($this->formatPath($file));
		} else {
			Throw new \Exception('File not exists');
		}
	}

	public function formatPath($path) {
		if ($path[0] != '/') {
			return '/' . $path;
		} else {
			return $path;
		}
	}

	public function getFileData($file, $options = ["include_media_info" => true, "include_deleted" => false]) {
		return $this->_dbx->getMetadata($this->formatPath($file), $options);
	}

	public function isDropboxEntityExists($file) {
		try {
			return $this->getFileData($file);
		} catch (ClientException $e) {
			return false;
		} catch (DropboxClientException $b) {
			return false;
		}
	}

	public function createFolder($folder) {
		return $this->_dbx->createFolder($this->formatPath($folder));
	}

	public function deleteFolder($folder) {
		return $this->_dbx->delete($this->formatPath($folder));
	}

	public function getTemporaryLink($file) {
		return $this->_dbx->getTemporaryLink($this->formatPath($file));
	}
}