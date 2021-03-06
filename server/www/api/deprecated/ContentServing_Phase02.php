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

	IMPLEMENTATION LEVEL - Iterative Cycle No. 2
	YES,	Basic Content Serving Functionalities
	NO,		Enhanced Content Serving Functionalities
	NO,		Arguments Parsing Support
	NO,		Text Manipulation Support

*/

// Check if request for Insert or Retrieve files
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
		
		// Check if publicPassphrase is appended on request url
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
		$fileID = substr($restPath,9);

		//Check if publicPasshrase is correct, userID returned on positive feedback
		include 'Passphrase.php';
		$userID = Passphrase::verifyPublicPhrase($phrase);
		
		if ($userID != '') {
			// Evaluate fileID as integer
			if(is_numeric($fileID) > 0) {
				
				$dbCon = mysql_connect('localhost','root','');	
				if ($dbCon)
				{
					mysql_select_db("textfarm", $dbCon);
					
					// Retrieve requested content from database
					$dbString = "SELECT filePath FROM File WHERE fileID='".$fileID."'";
					$result = mysql_query($dbString);

					if (mysql_errno($dbCon) == '0' and mysql_num_rows($result) > 0) {
						$content = mysql_fetch_row($result)[0];					
						mysql_close($dbCon);	
						TextFarm::sendResponse(200, $content); // OK
					}
					mysql_close($dbCon);
					TextFarm::sendResponse(404); // Not Found		
				}
				else
				{
					die('Could not connect: ' . mysql_error());
				}				
			}
			else
			{
				TextFarm::sendResponse(404); // Not Found
			}		
		}
		else
		{
			TextFarm::sendResponse(403); // Forbidden
		}	
	}
}

?>