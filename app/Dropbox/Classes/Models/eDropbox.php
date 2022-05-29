<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 17.11.2017
 * Time: 15:09
 */
namespace App\Dropbox\Classes\Models;

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\Models\ModelInterface;

class eDropbox extends Dropbox
{
	public function createSharedLinkWithOptions($path, $settings = null) {
		//Path cannot be null
		if (is_null($path)) {
			throw new DropboxClientException("Path cannot be null.");
		}
		$parameters = [
			"path" => $path
		];
		if (!empty($settings)) {
			$parameters["settings"] = $settings;
		}
		//Get Temporary Link
		$response = $this->postToAPI('/sharing/create_shared_link_with_settings', $parameters);

		//Make and Return the Model
		return $this->makeModelFromResponse($response);
	}

	public function createSharedLink($path) {
		//Path cannot be null
		if (is_null($path)) {
			throw new DropboxClientException("Path cannot be null.");
		}
		$parameters = [
			"path" => $path
		];
		//Get Temporary Link
		$response = $this->postToAPI('/sharing/create_shared_link_with_settings', $parameters);

		//Make and Return the Model
		return $this->makeModelFromResponse($response);
	}

    /**
     * @see https://www.dropbox.com/developers/documentation/http/documentation#sharing-list_shared_links
     * @param $path
     * @return ModelInterface
     * @throws DropboxClientException
     */
    public function listSharedLinks($path): ModelInterface
    {
        $response = $this->postToAPI('/sharing/list_shared_links', [
            "path"   => $path,
            "direct_only" => true,
        ]);

        return $this->makeModelFromResponse($response);
    }
}
