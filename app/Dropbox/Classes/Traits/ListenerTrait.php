<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 06.09.2017
 * Time: 14:55
 */

namespace App\Dropbox\Classes\Traits;

trait ListenerTrait 
{
	public $callbacks = [];
	public $index_callback = [];

	public function addListener($param, $callback, $callback_on_error = false) 
    {
		if (is_null($param)) 
		{
			if (is_callable($callback)) 
			{
				$this->index_callback = $callback;
			}
		} else {
		    
			if (is_callable($callback))
			{
                $callback_name = @md5(is_array($param) ? implode(',', $param) : $param);
                
                $this->callbacks[$callback_name] = [
                    "parameter" => $param,
                    "callback" => $callback,
                    "on_error" => $callback_on_error
                ];   
                
			}
		}
		return $this;
	}

	//https://apps.customize.co.il/transactnow/dropbox/?id=${New Corporate Apps.New Corporate App Id}&baseroot=/Flexagon Team Folder/Flexagon CRM Document Automation/New Applications/&module=Leads
	
	public function listen() 
    {
		if (empty($_GET) && $this->index_callback && is_callable($this->index_callback)) 
		{
			call_user_func($this->index_callback->bindTo($this));
		}
		else 
		{
			foreach ($this->callbacks as $key => $data) 
			{
				$callback = $this->callbacks[$key]['callback'];
				$callback_error = $this->callbacks[$key]['on_error'];
			
				if (is_array($data['parameter']))
				{
					$parameters = [];
					foreach ($data['parameter'] as $parameter) 
					{
						@$parameters[$parameter['parameter']] = $_GET[$parameter['parameter']] ?? null;
					}
					if (is_callable($callback)) {
						call_user_func($callback->bindTo($this), $parameters);
					}
				} else {
					if (isset($_GET[$data['parameter']]) || isset($_POST[$data['parameter']])) {
						$parameter = isset($_GET[$data['parameter']]) ? $_GET[$data['parameter']] : $_POST[$data['parameter']];
						if (is_callable($callback)) {
							call_user_func($callback->bindTo($this), $parameter);
						}
					} else {
						if ($callback_error && is_callable($callback_error)) {
							call_user_func($callback_error->bindTo($this), $data['parameter']);
						}
					}
				}
			}
		}
	}
}