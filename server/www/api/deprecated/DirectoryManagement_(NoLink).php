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

/*	Directory Management Service	*/

// Class Directory Management

class DirectoryManagement {
	private $query = array();
	private $userID;

	public function __construct()
	{
		global $requestParser;
		$this->query = $requestParser->getRequestVars();
		
		// Check if privatePassphrase is presented on request
		if(isset($this->query['privatephrase']) and trim($this->query['privatephrase']) != '')
		{
			// Check if privatePasshrase is correct, userID returned on positive feedback
			include 'Passphrase.php';
			$this->userID = Passphrase::verifyPrivatePhrase(trim($this->query['privatephrase']));
			
			// IF privatePassphrase valid
			if ($this->userID != '') {			
				$this->moveContent();
			}
		}
		else
		{
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}
	}

	private function moveContent()
	{
		global $restPath;
		$fileID = substr($restPath, 5);
		
		// Evaluate fileID as integer
		if(is_numeric($fileID) > 0) {
			$dbCon = mysql_connect('localhost','root','');	
			
			if ($dbCon)
			{
				mysql_select_db("textfarm", $dbCon);

				// Retrieve userID using fileID to verify target content's ownership
				$dbString = "SELECT userID FROM file WHERE fileID='".$fileID."' AND userID IS NOT NULL";
				$result = mysql_query($dbString);

				if (mysql_errno($dbCon) == '0' and mysql_affected_rows($dbCon) == 1) {
					$row = mysql_fetch_row($result)[0];

					// Verifying if user has target content ownership
					if ($row == $this->userID) {						
						// Update fileName if parameter received
						if(isset($this->query['fileName']) and trim($this->query['fileName']) != '')
						{
							// Check if filename & file extension is properly specify.
							$path = pathinfo(trim($this->query['fileName']));
							
							// Update fileName & fileExtension using given parameter
							if (isset($path['filename']) and isset($path['extension']) and ($path['filename'] != '' and $path['extension']) != '')
							{								
								$_dbString = "UPDATE file SET fileName='" . $path['filename'] . "', fileExt='" . $path['extension'] . "' WHERE fileID='" . $fileID . "'";
								mysql_query($_dbString);
								
								if (mysql_errno($dbCon) != '0' and mysql_affected_rows($dbCon) == 0) {
									mysql_close($dbCon);
									TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
								}
							}
							else
							{
								TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
							}
						}
						
						// Update filePermission if parameter received
						if(isset($this->query['permission']) and (trim($this->query['permission']) == 'public' or trim($this->query['permission']) == 'private'))
						{
							(trim($this->query['permission']) == 'public') ? $permission = '1' : $permission = '0';
							$_dbString = "UPDATE file SET filePermission='" . $permission . "' WHERE fileID='" . $fileID . "'";
							mysql_query($_dbString);
							
							if (mysql_errno($dbCon) != '0' and mysql_affected_rows($dbCon) == 0) {
								mysql_close($dbCon);
								TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
							}
						}
						else
						{
							TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
						}
						
						mysql_close($dbCon);
						TextFarm::sendResponse(200, '200 OK'); // OK
					}
					else
					{
						TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
					}
				}
				else
				{
					TextFarm::sendResponse(404, '403 Forbidden'); // Not Found
				}
			}
			else
			{
				die('Could not connect: ' . mysql_error());
			}
		}
	}

	public static function generateDirList($phrase)
	{
		// Check if privatePassphrase is presented on request
		if(isset($phrase) and trim($phrase) != '')
		{	
			// Check if privatePasshrase is correct, userID returned on positive feedback
			include 'Passphrase.php';
			$userID = Passphrase::verifyPrivatePhrase(trim($phrase));

			if ($userID != '') {
				$dbCon = mysql_connect('localhost','root','');	
				
				// Generating directory listing in XML format
				$xml = "<? version=\"1.0\" encoding=\"utf-8\" ?>\n<textfarm>";
				
				if ($dbCon)
				{					
					mysql_select_db("textfarm", $dbCon);

					// Appending <user> data into XML					
					// Retrieve user information through userID				
					$dbString = "SELECT privatePassphrase, publicPassphrase, regDate FROM user WHERE userID='".$userID."'";
					$result = mysql_query($dbString);

					if (mysql_errno($dbCon) == '0' and mysql_num_rows($result) > 0) {
						$row = mysql_fetch_row($result);
						
						$xml .= "\n\t<user userID=\"".$userID."\" registerDate=\"".$row[2]."\">";
						$xml .= "\n\t\t<privatePassphrase>".$row[0]."</privatePassphrase>";
						if ($row[1] != '') { // IF publicPassphrase existed
							$xml .= "\n\t\t<publicPassphrase>".$row[1]."</publicPassphrase>";
						}
						else // IF publicPassphrase not exist
						{
							$xml .= "\n\t\t<publicPassphrase />";
						}	
						$xml .= "\n\t</user>";						
					}
					
					// Appending <directory> data into XML					
					// Retrieve user's stored contents information through userID
					$_dbString = "SELECT fileID, fileName, fileExt, filePermission, OCTET_LENGTH(filePath) FROM file WHERE userID='".$userID."'";
					$_result = mysql_query($_dbString);					
					
					if (mysql_errno($dbCon) == '0' and mysql_num_rows($_result) > 0) { // IF user has contents stored
						$row = mysql_fetch_row($result);
						$xml .= "\n\t<directory>";

						while($_row = mysql_fetch_array($_result))
						{
							if ($_row[3] == '1') { // filePermission = 'public'
								$xml .= "\n\t\t<content fileID=\"".$_row[0]."\" fileName=\"".$_row[1].".".$_row[2]."\" contentSize=\"".$_row[4]."\" permission=\"public\" />";
							}
							else // filePermission = 'private'
							{
								// Retrieve authKeys information using fileID
								$__dbString = "SELECT authKeys, authState FROM authKey WHERE fileID='".$_row[0]."'";
								$__result = mysql_query($__dbString);
								
								if (mysql_errno($dbCon) == '0' and mysql_num_rows($__result) > 0) { // IF content have any authKeys assignment
									$xml .= "\n\t\t<content fileID=\"".$_row[0]."\" fileName=\"".$_row[1].".".$_row[2]."\" contentSize=\"".$_row[4]."\" permission=\"private\">";
									while($__row = mysql_fetch_array($__result)) // if no file
									{
										($__row[1] == 0) ? $authState = 'active' : $authState = 'inactive';											
										$xml .= "\n\t\t\t<authorize authKeys=\"".$__row[0]."\" status=\"".$authState."\" />";
										
									}
									$xml .= "\n\t\t</content>";
								}								
								else // IF content do not has authKeys assign
								{
									$xml .= "\n\t\t<content fileID=\"".$_row[0]."\" fileName=\"".$_row[1].".".$_row[2]."\" contentSize=\"".$_row[4]."\" permission=\"private\" />";
								}							  
							}							  
						}
						$xml .= "\n\t</directory>";
					}
					else // IF user does not has any stored content
					{
						$xml .= "\n\t<directory />";		
					}
					
					$xml .= "\n</textfarm>";
					mysql_close($dbCon);
					return $xml;
				}
				else
				{
					die('Could not connect: ' . mysql_error());
				}				
			}
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}
		else
		{
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}
	}
}

?>