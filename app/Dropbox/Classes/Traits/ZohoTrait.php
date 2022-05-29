<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 06.09.2017
 * Time: 14:55
 */

namespace App\Dropbox\Classes\Traits;

trait ZohoTrait {

	private $zoho_key;
	public $quote;
	public $queue;
	public static $calls = 0;

	public function setZohoKey($zoho_key) {
		$this->zoho_key = $zoho_key;
		return $this;
	}

	public function getZohoKey() {
		return $this->zoho_key;
	}


	public function toDateFormat(string $date, $format) {
		if ($date = \DateTime::createFromFormat('d/m/Y H:i', $date)) {
			return $date->format($format);
		} else {
			return (new \DateTime($date))->format($format);
		}
	}

	public function convertDate(string $string) {
		if (preg_match("/\d{1,2}\/\d{1,2}\/\d{4} \d{1,2}:\d{1,2}:\d{1,2}/", $string, $output_array)) {
			return (new \DateTime(current($output_array)))->format("Y-m-d H:i:s"); //2009-08-04 01:00:00
		} else {
			Throw new \Exception('Wrong date format given in string: ' . $string);
		}
	}

	public function insertQuoteZoho($quote) {
		if (!empty($quote)) {
			$xml = $this->asXML([$quote], 'Quotes');
			return $this->insertRecordsZoho($xml, 'Quotes');
		}
	}

	public function updateRecords($id, $data) {
		if (!empty($quote)) {
			$xml = $this->asXML([$quote], 'Quotes');
			return $this->insertRecordsZoho($xml, 'Quotes');
		}
	}

	public function stripPrice($price) {
		return (float)preg_replace("/[^0-9\.]/", "", $price);
	}

	public function stripZCRM($string) {
		return str_replace('zcrm_', '', $string);
	}

	/**
	 * @param $xml
	 * @param $type
	 * @return bool
	 * @throws Exception
	 * @method Implementation of Zoho Insert Records method
	 */
	public function insertRecordsZoho($xml, $type) 
    {
		if (empty($this->zoho_key)) Throw new Exception('Undefined Zoho Key');
		
		if (empty($xml) || empty($type))
			return false;
		
		$auth_data = array(
		    'authtoken' => $this->zoho_key,
			'scope' => 'crmapi',
			'newFormat' => 1,
			'xmlData' => $xml,
		);
		
		return $this->request("https://www.zohoapis.com/crm/v2/private/$type/insertRecords", false, $auth_data);
	}

	public function updateRecordsZoho($module, $xml, $id = false) 
    {
		if (empty($this->zoho_key)) Throw new Exception('Undefined Zoho Key');
		
		if (empty($xml) || empty($module))
			return false;
		
		$auth_data = [
			'scope' => 'crmapi',
			'newFormat' => 1
		];
		
		if ($id) {
			$auth_data['id'] = $id;
		}
		
		return $this->request($this->getMethodLink('json', $module, 'updateRecords', true, $auth_data), ['xmlData' => $xml]);
	}

	/**
	 * @param $file
	 * @param $type
	 * @param $id
	 * @param bool $stream
	 * @return bool
	 * @throws Exception
	 * @method Implementation of Zoho Upload File method
	 */
	public function uploadFileZoho($file, $type, $id, $stream = false) {
		if (empty($this->zoho_key)) Throw new Exception('Undefined Zoho Key');
		if (empty($file) || empty($file))
			return false;
		$auth_data = array(
		    'authtoken' => $this->zoho_key,
			'scope' => 'crmapi',
			'newFormat' => 1,
			'id' => $id
		);
		if ($stream) {
			$auth_data['content'] = $file;
		} else {
			$auth_data['attachmentUrl'] = $file;
		}
		
		return $this->request("https://www.zohoapis.com/crm/v2/private/$type/uploadFile", false, $auth_data);
	}
	
	public function deleteFileZoho($module, $file_id) {
		return $this->request($this->getMethodLink('json', $module, 'deleteFile', true, [
			'scope' => 'crmapi',
			'id' => $file_id
		]));
	}

	/**
	 * @param string $format
	 * @param string $module
	 * @param string $method
	 * @param array $get_parameters
	 * @param bool $add_auth_token
	 * @return string link
	 */
	public function getMethodLink(string $module, string $method, $add_auth_token = false, array $get_parameters = []) 
    {
		$link = "https://www.zohoapis.com/crm/v2/private/$module/$method";
		
		
		return $link;
	}

	/**
	 * @param $link
	 * @param array $data
	 * @param bool $callback
	 * @return mixed|string
	 */
	public function request($link, $data = [], $callback = false) {
		$response = "";
		try {
			$curl = curl_init();
			if ($data) {
				curl_setopt_array($curl, array(
					CURLOPT_URL => $link,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 120,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_POSTFIELDS => $data,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_HTTPHEADER => array(
						"cache-control: no-cache"
					),
				));
			} else {
				curl_setopt_array($curl, array(
					CURLOPT_URL => $link,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 120,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"cache-control: no-cache"
					),
				));
			}
			
			
			
			$response = curl_exec($curl);
			if (FALSE === $response) {
				throw new Exception(curl_error($curl), curl_errno($curl));
			}
		} catch (Exception $e) {
			trigger_error(sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);
		}
		if ($callback && is_callable($callback)) {
			return call_user_func($callback->bindTo($this), $response);
		} else {
			return $response;
		}
	}

	public function getItemsParameters($iterable, $primary = false) {
		$quotes = [];
		if ($iterable && is_array($iterable)) {
			foreach ($iterable as $item) {
				$quote = [];
				foreach ($item['FL'] as $fl) {
					if (isset($fl['content'])) {
						$quote[$fl['val']] = $fl['content'];
					} elseif (!empty($fl['product'])) {
						if (isset($fl['product']["FL"])) {
							$product_iterable = [$fl['product']];
						} else {
							$product_iterable = $fl['product'];
						}
						$quote['products'] = $this->getItemsParameters($product_iterable, 'Product Id');

					}
				}
				if ($primary && isset($quote[$primary])) {
					$quotes[$quote[$primary]] = $quote;
				} else {
					$quotes[] = $quote;
				}
			}
			return $quotes;
		} else {
			return $iterable;
		}
	}

	public function getZohoRecordById($record_id, $module, $format = "json", $callback) {
		return $this->request("https://www.zohoapis.com/crm/v2/{$module}/getRecordById?authtoken={$this->getZohoKey()}&scope=crmapi&id={$record_id}", $callback);
	}

	/**
	 * @param $response
	 * @return mixed
	 */
	public function simpleXML($response) {
		$response = str_replace('\'', '’', $response);
		$xml_answer = simplexml_load_string($response);
		$xml_answer = json_encode($xml_answer);
		return $this->processResponse($xml_answer);
	}

	/**
	 * @param $json
	 * @param $module
	 * @return array|bool|mixed
	 */
	public function processResponse($json, $module) {
		try {
			$response = static::json_decode($json, true);
			if (isset($response['response'])) {
				$response = $response['response'];
			}
			if (isset($response['nodata'])) {
				return false;
			} else {
				if (isset($response['result'][$module]['row']['FL'])) {
					return [$response['result'][$module]['row']];
				} elseif (isset($response['result'][$module]['row'])) {
					return $response['result'][$module]['row'];
				} else {
					Throw new Exception('Wrong format answer ZOHO', 500);
				}
			}
			return $response;
		} catch (\Throwable $e) {
			return false;
		}
	}

	/**
	 * @param $data
	 * @param $method
	 * @return string (XML string)
	 */
	public function asXML($data, $method) {

        return json_encode([
            'data' => $data
        ]);
        
	    
		$object = '<' . $method . '>';
		if (!empty($data)) {
			$object .= $this->arrayToXML($data, "row");
		}
		$object .= '</' . $method . '>';
		return (simplexml_load_string($object))->asXML();
	}

	public function getPotentialIDsFromQuotes($_quotes) {
		return array_reduce($_quotes, function ($carry, $item) {
			if (is_null($carry)) $carry = [];
			if (isset($item['QUOTEID'], $item['POTENTIALID'])) {
				$carry[$item['POTENTIALID']][$item['QUOTEID']] = $item['QUOTEID'];
			}
			return $carry;
		});
	}

	/**
	 * @param $_quotes
	 * @return mixed
	 */
	public function getPotentialsForQuotes(&$_quotes) {
		$total_potentials = [];
		$potential_ids = $this->getPotentialIDsFromQuotes($_quotes);
		$potential_chunks = array_chunk($potential_ids, 100, true);
		foreach ($potential_chunks as $potential_chunk) {
			$potentials = $this->request($this->getMethodLink('json', 'Potentials', 'getRecordById'), [
				'authtoken' => $this->getZohoKey(),
				'idlist' => implode(';', array_keys($potential_chunk)),
				'scope' => 'crmapi',
				'newFormat' => '1'
			], function ($response) {
				if (empty($response)) {
					$this->debug('Empty Response', $response);
					return false;
				}
				return $this->processResponse($response, 'Potentials');
			});
			$potentials = $this->getItemsParameters($potentials, 'POTENTIALID');
			//linking potentials to quotes
			if (!empty($potentials)) {
				//go over all potentials from answer
				foreach ($potentials as $potential_id => $potential) {
					//if potential exists in potential_ids
					if (!empty($potential_ids[$potential_id])) {
						//get all quote_ids that are related to this potential and go over qotes
						foreach ($potential_ids[$potential_id] as $quote_id) {
							//if quote exists
							if (isset($_quotes[$quote_id])) {
								//linking potential data to this quote
								$_quotes[$quote_id]['potential'] = $potential;
							}
						}
					}
				}
				$total_potentials += $potentials;
			}
		}
		return $total_potentials;
	}

	private function arrayToXML($array, $sub_tree = "row") {
		$response = "";
		$row_number = 1;
		foreach ($array as $row_key => $row) {
			$response .= '<' . $sub_tree . ' no="' . $row_number++ . '">';
			foreach ($row as $fl_key => $fl_value) {
				if (is_array($fl_value)) {
					$response .= '<FL val="Product Details">';
					$response .= $this->arrayToXML($fl_value, $fl_key);
					$response .= '</FL>';
				} else {
					if (!empty($fl_value)) {
						if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fl_value)) {
							$response .= '<FL val="' . $fl_key . '"><![CDATA[' . $fl_value . ']]></FL>';
						} else {
							$response .= '<FL val="' . $fl_key . '">' . $fl_value . '</FL>';
						}
					}
				}
			}
			$response .= "</$sub_tree>";
		}
		return $response;
	}
}