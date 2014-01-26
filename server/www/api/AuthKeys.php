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

/*	Authorize Keys Service	*/

// Class Authorize Keys

class AuthKeys {

	public function __construct()
	{
		global $requestParser;
		$query = $requestParser->getRequestVars();

		// Check if privatePassphrase is presented on request
		if(isset($query['privatephrase']) and trim($query['privatephrase']) != '')
		{	
			// Check if privatePasshrase is correct, userID returned on positive feedback
			include 'Passphrase.php';
			$userID = Passphrase::verifyPrivatePhrase(trim($query['privatephrase']));
			
			// IF privatePassphrase invalid
			if ($userID == '') {
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}
		}
		else
		{
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}	
	}
	
	public function generateAuthKeys()
	{
		global $restPath;
		$fileID = substr($restPath, 7);
		
		// Evaluate fileID as integer
		if(is_numeric($fileID) > 0) {
			$dbCon = mysql_connect('localhost','root','');	
			
			if ($dbCon)
			{
				mysql_select_db("textfarm", $dbCon);
				
				// Check if targeted content has filePermission = '0' [private] , else denied to create authorize keys
				$dbString = "SELECT filePermission FROM file WHERE fileID='".$fileID."'";
				$result = mysql_query($dbString);

				if (mysql_errno($dbCon) == '0' and mysql_num_rows($result) > 0) {
					$permission = mysql_fetch_row($result)[0];
					
					if ($permission == 0) {				
						// Create and Insert authKeys into dababase
						$authKeys = sha1(round(microtime(true) * 1000) . 'authkeys');
						$dbString = "INSERT INTO authKey (fileID, authKeys, authState) VALUES ('".$fileID."','".$authKeys."','0')";
						
						$result = mysql_query($dbString);

						if (mysql_errno($dbCon) == '0') {
							mysql_close($dbCon);
							// Reply new authKeys for targeted content
							TextFarm::sendResponse(200, $authKeys, 'text/plain; charset=utf-8'); // OK
						}					
					}
					else
					{
						mysql_close($dbCon);
						TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
					}
				}
				else
				{
					mysql_close($dbCon);
					TextFarm::sendResponse(204, '204 No Content'); // No Content
				}
				mysql_close($dbCon);
				TextFarm::sendResponse(403, '403 Forbidden');
			}
			else
			{
				die('Could not connect: ' . mysql_error());
			}
		}
	}
	
	public function modifyAuthKeys()
	{
		global $restPath;
		$authKeys = '';
		$authState = '';
		
		// Setting parameters based on request path
		if (strncasecmp($restPath, 'activate/', 9) == 0)
		{
			$authKeys = substr($restPath, 9);
			$authState = '0';
		}
		else if (strncasecmp($restPath, 'deactivate/', 11) == 0)
		{
			$authKeys = substr($restPath, 11);
			$authState = '1';
		}

		// Check if authKeys & authState parameters properly set
		if (trim($authKeys) != '' and $authState != '')
		{
			$dbCon = mysql_connect('localhost','root','');	
			
			if ($dbCon)
			{
				mysql_select_db("textfarm", $dbCon);
				
				// Update authState using given authKeys
				$dbString = "UPDATE authkey SET authState='" . $authState . "' WHERE authKeys='" . $authKeys . "'";
				$result = mysql_query($dbString);
				
				// Check on mysql feedback to verify wether authState toggle successfull or not
				if (mysql_errno($dbCon) == '0' and mysql_affected_rows($dbCon) == '1') {					
					mysql_close($dbCon);
					TextFarm::sendResponse(200, '200 OK'); // OK
				}
				else
				{
					mysql_close($dbCon);
					TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
				}									
			}
			else
			{
				die('Could not connect: ' . mysql_error());
			}
		}
		else
		{
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}
	}

	public static function verifyAuthKeys($keys)
	{
		$fileID = '';
		
		// Check if authKeys field is null
		if(trim($keys) != '')
		{			
			$dbCon = mysql_connect('localhost','root','');	
			if ($dbCon)
			{
				mysql_select_db("textfarm", $dbCon);
				
				// Retrieve fileID & authState using authKeys				
				$dbString = "SELECT fileID, authState FROM authkey WHERE authKeys='".trim($keys)."'";
				$result = mysql_query($dbString);
				
				if (mysql_errno($dbCon) == '0' and mysql_num_rows($result) > 0) {
					$row = mysql_fetch_row($result);

					// Check if targeted content has authState = '0' [active] , set fileID = '0' to indicate
					if ($row[1] == 0) {
						$fileID = $row[0];					
					}
					else
					{
						$fileID = '0';
					}
				}
				mysql_close($dbCon);
			}
			else
			{
				die('Could not connect: ' . mysql_error());
			}
			return $fileID;	
		}
	}
}

?>