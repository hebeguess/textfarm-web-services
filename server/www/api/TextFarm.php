<?php

/*

    TextFarm Web Services
    Copyright (C) 2014 Goh Liang Chi

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

    email: gohliangchi@gmail.com

*/


/*  ==  TextFarm Web Services  ==*/

class TextFarm { 
	
	public static function processRequest()
	{
		$reqMethod = strtoupper($_SERVER['REQUEST_METHOD']);
		$return_obj = new RestRequest();

		$queryData = array();

		switch ($reqMethod)
		{
		case 'GET':
			{
				$queryData = $_GET;
				break;
			}
		case 'POST':
			{
				$queryData = $_POST;
				break;
			}
		case 'PUT':
			{
				$queryData = $_PUT;
				break;
			}
		}

		$return_obj->setMethod($reqMethod);
		$return_obj->setRequestVars($queryData);

		if(isset($queryData['data']))
		{
			$return_obj->setData(trim($queryData['data']));
		}
		return $return_obj; 
	}
	
	public static function sendResponse($status = 403, $body = '', $content_type = 'text/html; charset=utf-8')
	{		
		// Set Response HTTP status
		$status_header = 'HTTP/1.1 ' . TextFarm::getStatusCodeMessage($status);
		header($status_header, 'true', $status);
		
		// Set Response Content type
		header('Content-type: ' . $content_type);
		
		// Set Response Content length
		header('Content-Length: ' . strlen($body));
		
		// Reply content body if available
		if($body != '') {
			echo $body;
		}
		
		exit;
	}

	public static function getStatusCodeMessage($status)
	{
		// TextFarm API Responses Status Codes Array
		$codes = Array(
		100 => 'Continue',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		204 => 'No Content',
		206 => 'Partial Content',
		403 => 'Forbidden',
		404 => 'Not Found',
		);
		return (isset($codes[$status])) ? strval($status) . ' ' . $codes[$status] : strval($status) . ' ' . '$codes[403]';
	}

}


// Rest Wrapper Class

class RestRequest
{ 
	private $request_vars;
	private $data;
	private $http_accept;
	private $method;

	public function __construct()
	{		
		$this->request_vars = array();
		$this->data = '';
		$this->http_accept = (strpos($_SERVER['HTTP_ACCEPT'], 'text/html')) ? $_SERVER['HTTP_ACCEPT'] : 'text/html';
		$this->method = 'GET';
	}

	public function setData($data)
	{
		$this->data = $data;
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function setRequestVars($request_vars)
	{
		$this->request_vars = $request_vars;
	}

	public function getData()
	{
		return $this->data;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getHttpAccept()
	{
		return $this->http_accept;
	}

	public function getRequestVars()
	{
		return $this->request_vars;
	} 

}

?>