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

/*	Argument Parsing Service 	*/

// Arguments Parsing Class

class ArgumentsParsing {
	private $query = array();
	private $MIME;
	private $Encoding;
	private $fileName;
	private $method;
	
	public function __construct()
	{
		global $requestParser;
		$this->query = $requestParser->getRequestVars();
		
		$this->setMIME();
		$this->setEncoding();
		$this->setFilename();
	}

	private function setMIME()
	{
		$mimeTypes = array(
		"text/css"=>"text/css",
		"text/csv"=>"text/csv",
		"text/css"=>"text/css",
		"text/csv"=>"text/csv",
		"text/html"=>"text/html",
		"text/plain"=>"text/plain",
		"text/vcard"=>"text/vcard",
		"text/xml"=>"text/xml",
		"image/svg+xml"=>"image/svg+xml",
		"application/atom+xml"=>"application/atom+xml",
		"application/ecmascript"=>"application/ecmascript",
		"application/json"=>"application/json",
		"application/javascript"=>"application/javascript",
		"application/octet-stream"=>"application/octet-stream",
		"application/rss+xml"=>"application/rss+xml",
		"application/soap+xml"=>"application/soap+xml",
		"application/xhtml+xml"=>"application/xhtml+xml",
		"application/xml-dtd"=>"application/xml-dtd",
		"application/zip"=>"application/zip",
		"application/gzip"=>"application/gzip"
		);		
		
		// Check if MIME Type specified in postfields, else fallback default
		if (isset($this->query['mime']) and trim($this->query['mime']) != '') { 
			$this->MIME = strtolower($this->query['mime']);

			if (isset($mimeTypes[$this->MIME])) {
				$this->MIME = $mimeTypes[$this->MIME];
			}
			else
			{
				$this->MIME = 'text/html; charset=utf-8';
			}			
		}
		else // fallback to default MIME reponse
		{
			$this->MIME = 'text/html; charset=utf-8';
		}	
	}
	
	private function setEncoding()
	{	
		$supportedEncodings = array(
		"UTF32"=>"UTF-32",
		"UTF32BE"=>"UTF-32BE",
		"UTF32LE"=>"UTF-32LE",
		"UTF16"=>"UTF-16",
		"UTF16BE"=>"UTF-16BE",
		"UTF16LE"=>"UTF-16LE",
		"UTF7"=>"UTF-7",
		"UTF8"=>"UTF-8",
		"ASCII"=>"ASCII",
		"EUCJP"=>"EUC-JP",
		"JIS"=>"JIS",
		"ISO88591"=>"ISO-8859-1",
		"EUCCN"=>"EUC-CN",
		"EUCTW"=>"EUC-TW",
		"BIG5"=>"BIG-5"
		);
		
		// Check if Encoding specified in postfields, else fallback default
		if (isset($this->query['encoding']) and trim($this->query['encoding']) != '') { 
			$this->Encoding = strtoupper($this->query['encoding']);
			
			if (isset($supportedEncodings[$this->Encoding])) {
				$this->Encoding = $supportedEncodings[$this->Encoding];
			}
			else
			{
				$this->Encoding = $supportedEncodings['UTF8'];
			}	
		}
		else // fallback to default encoding
		{
			$this->Encoding = $supportedEncodings['UTF8'];
		}
	}
	
	private function setFilename()
	{
		// Check if fileName & fileExt specified in postfields, else fallback default
		if ((isset($this->query['fileName']) and isset($this->query['fileExt'])) and (trim($this->query['fileName']) != '' and trim($this->query['fileExt']) != '')) {			
			$path = pathinfo(trim($this->query['fileName']).".".trim($this->query['fileExt']));			
			if (isset($path['filename']) and isset($path['extension'])) {
				$this->fileName = $path['basename'];
			}			
		}
		
		if ($this->fileName == '')
		{
			global $fileName;
			$this->fileName = $fileName;
		}
		
		if (isset($this->query['attachment']) and trim($this->query['attachment']) == 'false') {	
			$this->method = 'inline';
		}
		else
		{
			$this->method = 'attachment';
		}
		
	}

	public function getMIME()
	{
		return $this->MIME;
	}
	
	public function getEncoding()
	{
		return $this->Encoding;
	}
	
	public function getFilename()
	{
		header('Content-Disposition: '.$this->method.'; filename="'.$this->fileName.'"');
		return $this->fileName;
	}
	
}

?>