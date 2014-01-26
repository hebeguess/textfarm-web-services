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

/*	Text Manipulation Service	*/

// Code Snippets
error_reporting(0);

//Check if nameSpace & nodeName parameters set
if ((isset($this->query['nameSpace']) and isset($this->query['nodeName'])) and (trim($this->query['nameSpace']) != '' and trim($this->query['nodeName']) != '')) {

	try {		
		$action = trim($this->query['action']);
		global $xmlData;
		$xmlData = $content;		

		if ($action == 'add') {
			$textCrafter = new TextManipulation();	
			$textCrafter->addContent();			

			// Check if request for update or retrieve
			if ($restPath == 'insert' or (strncasecmp($restPath, 'insert/', 7) == 0 and count(explode("/",$restPath,2)) == 2)) {
				$textCrafter->saveContent();
				TextFarm::sendResponse(200, '200 OK'); // OK
			}
			else
			{
				$xmlData = $textCrafter->getContent();
			}			
		}
		else if ($action == 'remove') {		
			//Check if attribute & value parameters set
			if ((isset($this->query['attribute']) and isset($this->query['value'])) and	(trim($this->query['attribute']) != '' and trim($this->query['value']) != '')) {				
				$textCrafter = new TextManipulation();
				$textCrafter->removeContent();

				// Check if request for update or retrieve
				if ($restPath == 'insert' or (strncasecmp($restPath, 'insert/', 7) == 0 and count(explode("/",$restPath,2)) == 2)) {
					$xmlData = $textCrafter->saveContent();
					TextFarm::sendResponse(200, '200 OK'); // OK
				}
				else
				{
					$xmlData = $textCrafter->getContent();
				}				
			}			
		}		
		$content = $xmlData;
	}
	catch (Exception $ex) {
		// Do Nothing		
	}	
}


// Text Manipulation Class

class TextManipulation {
	private $query = array();
	private $simpleXml;
	private $xmlStr;
	
	public function __construct()
	{
		global $requestParser;
		$this->query = $requestParser->getRequestVars();

		// Check if the content is xml parsable
		global $xmlData;
		$this->simpleXml = new SimpleXMLElement($xmlData);
	}
	
	public function addContent()
	{
		// IF XML Root Node not match
		if ($this->simpleXml->getName() != trim($this->query['nameSpace'])) {
			throw new Exception("Root Node Miss-match.");
		}

		// Adding new node
		$node = $this->simpleXml->addChild(trim($this->query['nodeName']));
		
		// Adding new attributes if presented
		if ((isset($this->query['attribute']) and isset($this->query['value'])) and	(trim($this->query['attribute']) != '' and trim($this->query['value']) != '')) {
			$node->addAttribute(trim($this->query['attribute']), trim($this->query['value']));
		}	

		// Reindent XML
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->simpleXml->asXML());

		$this->xmlStr = $dom->saveXML();
	}
	
	public function removeContent()
	{
		// IF XML Root Node not match
		if ($this->simpleXml->getName() != trim($this->query['nameSpace'])) {
			throw new Exception("Root Node Miss-match.");
		}

		// Removing node with a matching attribute value
		for ($i=0; $i<sizeof($this->simpleXml->{trim($this->query['nodeName'])}); $i++) {					
			if ($this->simpleXml->{trim($this->query['nodeName'])}[$i][trim($this->query['attribute'])] == trim($this->query['value'])) {
				unset($this->simpleXml->{trim($this->query['nodeName'])}[$i]);
				break;
			}
		}
		
		// Reindent XML
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->simpleXml->asXML());
		
		$this->xmlStr = $dom->saveXML();
	}
	
	public function saveContent()
	{
		global $restPath;
		$fileID = substr($restPath,7);
		
		$dbCon = mysql_connect('localhost','root','');	
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Update content back into dababase
			$this->xmlStr = mysql_real_escape_string($this->xmlStr);
			$dbString = "UPDATE textfarm.file SET filePath='$this->xmlStr' WHERE file.fileID='$fileID';";
			$result = mysql_query($dbString);

			if (mysql_errno($dbCon) != '0' and mysql_affected_rows($dbCon) != '1') {
				mysql_close($dbCon);
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}
		}
	}
	
	public function getContent()
	{
		return $this->xmlStr;
	}	
}

?>