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

/*	Passphrase Generation Service	*/

// Passphrase Class

class Passphrase {
	private $query = array();
	
	public function __construct()
	{
		global $requestParser;
		$this->query = $requestParser->getRequestVars();
	}

	public function generatePublicPhrase()
	{		
		$dbCon = mysql_connect('localhost','root','');	
		$userID = '';
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Check privatePassphrase and retrieve corresponding userID
			if(isset($this->query['privatephrase']) and trim($this->query['privatephrase']) != '')
			{	
				// Retrieve privatephrase for verification
				$dbString = "SELECT userID FROM User WHERE privatePassphrase='".$this->query['privatephrase']."'";
				$result = mysql_query($dbString);
				
				if (mysql_num_rows($result) > 0) {	
					$userID = mysql_fetch_row($result)[0];
					$phrase = sha1(round(microtime(true) * 1000) . 'public');
					
					// Generate new public passphrase
					$dbString = "UPDATE User SET publicPassphrase='" . $phrase . "' WHERE privatePassphrase='" . $this->query['privatephrase'] . "'";
					mysql_query($dbString);
					
					if (mysql_errno($dbCon) == '0') {
						mysql_close($dbCon);
						return $phrase; // Reply new public passphrase
					}					
					mysql_close($dbCon);
					TextFarm::sendResponse();					
				}
				else
				{
					TextFarm::sendResponse(204, '204 No Content'); // No Content
				}				
			}
			else
			{
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}
	
	public function generatePrivatePhrase()
	{
		$dbCon = mysql_connect('localhost','root','');	
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// IF using username and password to update privatePassphrase
			// Check username and password fields
			if((isset($this->query['username']) and isset($this->query['password'])) and (trim($this->query['username']) != '' and trim($this->query['password']) != ''))
			{						
				// Retrieve userPass from corresponding userID
				$dbString = "SELECT userPass FROM User WHERE userID='".$this->query['username']."'";
				$result = mysql_query($dbString);
				
				if (mysql_num_rows($result) > 0) {			
					$userPass = mysql_fetch_row($result)[0];					
					
					// Verify userPass with received password field
					if ($userPass == $this->query['password']) {
						
						// Generate new private passphrase
						$phrase = sha1(round(microtime(true) * 1000) . 'private');
						$dbString = "UPDATE User SET privatePassphrase='" . $phrase . "' WHERE userID='" . $this->query['username'] . "'";
						mysql_query($dbString);
						
						if (mysql_errno($dbCon) == '0') {
							mysql_close($dbCon);
							return $phrase; // Reply new private passphrase
						}							
					}
					else
					{
						TextFarm::sendResponse(204, '204 No Content'); // No Content
					}
				}
			}
			// IF using existing privatePassphrase to request new privatePassphrase
			// Check privatePassphrase field
			else if(isset($this->query['privatephrase']) and trim($this->query['privatephrase']) != '')
			{	
				// Retrieve corresponding userID if privatePassphrase is correct; else userID=null
				$userID = Passphrase::verifyPrivatePhrase($this->query['privatephrase']);
				
				if ($userID != '') {
					$dbCon = mysql_connect('localhost','root','');	
					
					if ($dbCon)	{
						mysql_select_db("textfarm", $dbCon);
						
						// Generate new private passphrase
						$phrase = sha1(round(microtime(true) * 1000) . 'private');
						$dbString = "UPDATE User SET privatePassphrase='" . $phrase . "' WHERE userID='" . $userID . "'";
						mysql_query($dbString);						
						
						if (mysql_errno($dbCon) == '0') {
							mysql_close($dbCon);
							return $phrase; // Reply new private passphrase
						}
					}
				}
				else
				{
					TextFarm::sendResponse(204, '204 No Content'); // No Content
				}				
			}
			else
			{
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}
			mysql_close($dbCon);
			TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}

	public static function verifyPublicPhrase($phrase)
	{
		// Check if public $phrase existed in database and return userID if $phrase is correct
		$dbCon = mysql_connect('localhost','root','');	
		$userID = '';
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Check if $phrase field is valid
			if(isset($phrase) and $phrase != '') {
				// Retrieve corresponding userID if publicPassphrase is correct; else userID=null
				$dbString = "SELECT userID FROM User WHERE publicPassphrase='" . $phrase . "'";
				
				$result = mysql_query($dbString);
				
				if (mysql_num_rows($result) > 0) {	
					$userID = mysql_fetch_row($result)[0]; // Set userID
				}		
			}
			mysql_close($dbCon);
			return $userID; // Reply userID		 
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}
	
	public static function verifyPrivatePhrase($phrase)
	{
		// Check if private $phrase existed in database and return userID if $phrase is correct
		$dbCon = mysql_connect('localhost','root','');	
		$userID = '';
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Check if $phrase field is valid
			if(isset($phrase) and $phrase != '') {
				// Retrieve corresponding userID if privatePassphrase is correct; else userID=null
				$dbString = "SELECT userID FROM User WHERE privatePassphrase='" . $phrase . "'";	
				$result = mysql_query($dbString);
				
				if (mysql_num_rows($result) > 0) {	
					$userID = mysql_fetch_row($result)[0]; // Set userID
				}		
			}
			mysql_close($dbCon);
			return $userID; // Reply userID
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}
}

?>