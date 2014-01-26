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

/*	Content Serving Service	*/

/* NOTES

	IMPLEMENTATION LEVEL - Iterative Cycle No. 5
	YES,	Basic Content Serving Functionalities
	YES,	Enhanced Content Serving Functionalities
	YES,	Arguments Parsing Support
	NO,		Text Manipulation Support

*/

// Check if request for insert or retrieve files
if($restPath == 'insert') {	
	// Check if using correct call method = 'POST'
	if($requestParser->getMethod() == 'POST') {
		
		// Check if privatePassphrase is presented on request
		if(isset($requestParser->getRequestVars()['privatephrase']) and trim($requestParser->getRequestVars()['privatephrase']) != '') {
			// Insert new content
			$content = new ContentServing();
			$content->insertContent(trim($requestParser->getRequestVars()['privatephrase']));
		}
		else
		{
			TextFarm::sendResponse(403); // Forbidden
		}
		TextFarm::sendResponse(403); // Forbidden
	}
}
else if (strncasecmp($restPath, 'retrieve/', 9) == 0)
{	
	// Check if using 'GET' or 'POST' method to retrieve content
	if($requestParser->getMethod() == 'GET') {
		
		// Check if publicPassphrase is appended in request url
		if (count(explode("/",$restPath,3)) == 3) {
			$publicphrase = explode("/",$restPath,3)[2];		
			
			// Retrieve stored content
			$content = new ContentServing();	
			$fileID = $content->retrieveContent($publicphrase);
		}
	}
	else if($requestParser->getMethod() == 'POST') {
		
		// Check if publicPassphrase is presented on request
		if(isset($requestParser->getRequestVars()['publicphrase']) and trim($requestParser->getRequestVars()['publicphrase']) != '') {
			// Retrieve stored content
			$content = new ContentServing();	
			$fileID = $content->retrieveContent(trim($requestParser->getRequestVars()['publicphrase']));
		}
	}	
	TextFarm::sendResponse(403); // Forbidden
}
else
{
	TextFarm::sendResponse(403); // Forbidden
}


// Content Serving Class

class ContentServing {
	private $query = array();

	public function __construct()
	{
		global $requestParser;
		$this->query = $requestParser->getRequestVars();
	}

	public function insertContent($phrase)
	{
		// Check if filename & content & permission parameters set
		if ((isset($this->query['filename']) and isset($this->query['content']) and isset($this->query['permission'])) and
				(trim($this->query['filename']) != '' and trim($this->query['content']) != '' and trim($this->query['permission']) != ''))
		{
			//Check if privatePasshrase is correct, userID returned on positive feedback
			include 'Passphrase.php';
			$userID = Passphrase::verifyPrivatePhrase($phrase);
			
			// Check if permission parameter is properly set, else fallback to 'private'
			$permission = strtolower(trim($this->query['permission']));
			($permission == 'public') ? $permission = '1' : $permission = '0';

			// Check if filename & file extension is properly set, else fallback to 'file.txt'
			$path = pathinfo($this->query['filename']);
			(isset($path['filename'])) ? $fileName = $path['filename'] : $fileName = 'file';
			(isset($path['extension'])) ? $fileExt = $path['extension'] : $fileExt = 'txt';
			
			if ($userID != '') {
				$dbCon = mysql_connect('localhost','root','');	
				
				if ($dbCon)
				{
					mysql_select_db("textfarm", $dbCon);

					// Insert content into dababase
					$dbString = "INSERT INTO file (userID, fileName, filePath, fileExt, filePermission) VALUES ('".$userID."','".$fileName."','".$this->query['content']."','".$fileExt."','".$permission."')";
					$result = mysql_query($dbString);
					
					if (mysql_errno($dbCon) == '0') {
						$fileID = mysql_insert_id($dbCon);
						mysql_close($dbCon);
						// Reply content's fileID
						TextFarm::sendResponse(200, $fileID, 'text/plain; charset=utf-8'); // OK
					}					
					mysql_close($dbCon);
					TextFarm::sendResponse();
				}
				else
				{
					die('Could not connect: ' . mysql_error());
				}
			}			
		}
		TextFarm::sendResponse(403); // Forbidden
	}

	public function retrieveContent($phrase)
	{
		global $restPath;
		$fileID = ''; $authKeys = ''; $phrase = '';

		// Check if authKeys and publicPassphrase is presented on request
		// Allocating parameters into right variables
		if (count(explode("/",$restPath,3)) == 3) {
			$path = explode("/",$restPath,3);	
			
			if (strlen($path[1]) == 40) { // authKeys is appened on url
				$authKeys  = $path[1];
				include 'AuthKeys.php';
				$fileID = AuthKeys::verifyAuthKeys($authKeys);
			}
			else if(is_numeric($path) > 0) { // Evaluate check on fileID as integer
				$fileID = $path[1];  // fileID is appened on url
			}			
			$phrase = $path[2]; // publicPassphrase is appened on url			
		}
		else if(count(explode("/",$restPath,3)) == 2) {
			$path = substr($restPath,9);
			
			if (strlen($path) == 40) { // authKeys is appened on url
				$authKeys  = $path;	
				include 'AuthKeys.php';
				$fileID = AuthKeys::verifyAuthKeys($authKeys);
			}
			else if(is_numeric($path) > 0) { // Evaluate check on fileID as integer
				$fileID = $path;     // fileID is appened on url
			}
			
			if (isset($this->query['publicphrase']) and trim($this->query['publicphrase']) != '') { // publicPassphrase is in postfields
				$phrase = trim($this->query['publicphrase']);
			}
		}

		// IF fileID not found (no content)
		if ($fileID == '')
		{
			TextFarm::sendResponse(404); // Not Found
		}		
		
		// IF publicPassphrase not found
		if ($phrase == '') 
		{
			TextFarm::sendResponse(403); // Forbidden
		}	

		// Check if publicPasshrase is correct, userID returned on positive feedback
		include 'Passphrase.php';
		$userID = Passphrase::verifyPublicPhrase($phrase);
		
		// IF incorrect ownership
		if ($userID == '')
		{
			TextFarm::sendResponse(403); // Forbidden
		}		

		$dbCon = mysql_connect('localhost','root','');	
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Retrieve requested content from database
			$dbString = "SELECT filePath, filePermission, fileName, fileExt FROM file WHERE fileID='".$fileID."'";
			$result = mysql_query($dbString);
			
			if (mysql_errno($dbCon) == '0' and mysql_num_rows($result) > 0) {
				$row = mysql_fetch_row($result);
				
				// IF content has private flag, and has no authKeys received
				if ($row[1] == '0' and $authKeys == '') 
				{
					TextFarm::sendResponse(403); // Forbidden
				}
				else 
				{							
					$content = $row[0];					
					mysql_close($dbCon);
					
					global $fileName;
					$fileName = $row[2].".".$row[3]; // Set default fileName
				
					// Call Arguments Parsing Service for custom responses
					include 'ArgumentsParsing.php';
					$argsParse = new ArgumentsParsing();
				
					$mime = $argsParse->getMIME();
					$encoding = $argsParse->getEncoding();
					$fileName = $argsParse->getFilename();	
					
					// Encode content to target encoding
					// SKIP if target encoding is also UTF-8
					if($encoding != 'UTF-8') {
						$content = mb_convert_encoding($content, $encoding, 'UTF-8');
					}
					TextFarm::sendResponse(200, $content, $mime); // OK
				}
			}
			mysql_close($dbCon);
			TextFarm::sendResponse(404); // Not Found		
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}	
	}
}

?>