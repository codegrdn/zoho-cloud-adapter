<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 16.11.2017
 * Time: 13:50
 */

namespace App\Dropbox\Classes\Traits;


trait DirectoryTrait 
{
	public $working_directory;
	public function setDirectoryOnServer($directory) {
		$this->working_directory = $directory;
		return $this;
	}
	public function getDirectoryOnServer($directory) {
		$this->working_directory = $directory;
	}
	public function getServerDirectory($path) {
		if (empty($this->working_directory)) Throw new \Exception('Set working_directory first!');
		if ($this->working_directory[strlen($this->working_directory) - 1] != '/' && $path[0] != '/') {
			return $this->working_directory . '/' . $path;
		} else {
			return $this->working_directory . $path;
		}
	}
	public function putFile($path, $filename, $source) {
		if ($target_directory = $this->getServerDirectory($path)) {
			if (!is_dir($target_directory)) $this->createDirectoryPath($target_directory);
			$filepath = $this->implodePathFile($target_directory, $filename);
			if (!file_exists($filepath)) {
				file_put_contents($filepath, $source);
				return $filepath;
			} else {
				//Throw new \Exception('File ' . $filename . ' already exists on server!');
				$this::debug('File ' . $filename . ' already exists on server!', $filepath);
				return $filepath;
			}
		}
	}
	public function implodePathFile($path, $file) {
		return $path . '/' . $file;
	}
	public function createDirectoryPath($path, $rights = 0755) {
		if ($path = array_filter(explode('/', $path), function($e){
			return !empty($e);
		})) {
			foreach ($path as $directory) {
				$carry_path = !isset($carry_path) ? "/$directory" : $carry_path . "/$directory";
				if (!is_dir($carry_path)) {
					mkdir($carry_path, $rights);
				}
			}
		} else {
			Throw new \Exception("Wrong path given!");
		}
		return $carry_path;
	}
	public function deleteFile($path) {
		if (is_file($path)) {
			unlink($path);
		}
	}
	public function removeDirectory($path) {
		if (is_dir($path)) {
			rmdir($path);
		}
	}
}