<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 06.09.2017
 * Time: 14:47
 */

namespace App\Dropbox\Classes;

use App\Dropbox\Classes\Traits\DirectoryTrait;
use App\Dropbox\Classes\Traits\DropboxTrait;
use App\Dropbox\Classes\Traits\ListenerTrait;
use App\Dropbox\Classes\Traits\ZohoTrait;

class Core {
    
	public $directory;
	use DropboxTrait, ListenerTrait, ZohoTrait, DirectoryTrait;

	public function flush(&$array) {
		$array = [];
		return $this;
	}

	public static function debug($comment, $data) {
		echo "<b>$comment</b>";
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		echo "<hr>";
	}

	public static function json_encode(array $data, $options = 0, $dept = 512) {
		if ($data = @json_encode($data, $options, $dept)) {
			return $data;
		} else {
			Throw new \Exception("Error during encoding");
		}
	}
	public static function json_decode($data, $assoc = false, $dept = 512, $options = 0) {
		if ($data = @json_decode($data, $assoc, $dept, $options)) {
			return $data;
		} else {
			Throw new \Exception("Given string is not a json string");
		}
	}
}