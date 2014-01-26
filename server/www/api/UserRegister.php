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

/*	User Registration Service	*/

class UserRegister {
	private $query = array();

	public function __construct()
	{	
		global $requestParser;
		$this->query = $requestParser->getRequestVars();
		
		// Check username and password fields
		if( ((isset($this->query['username']) and isset($this->query['password'])))	and ((trim($this->query['username'] != '') and trim($this->query['password']) != '')) )
		{	
			// Check username avalability
			if ($this->isAvailable() == 't')
			{
				// Create new user
				$privatephrase = $this->createUser();
				TextFarm::sendResponse(200, $privatephrase, 'text/plain; charset=utf-8'); // OK
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
	
	private function isAvailable()
	{	
		// Check if userID is still available. available = 'f'; not available = 't';
		$dbCon = mysql_connect('localhost','root','');
		$bool = 'f';
		
		if ($dbCon)
		{
			mysql_select_db("textfarm", $dbCon);
			
			// Retrieve corresponding userID from database
			$dbString = "SELECT userID FROM User WHERE userID='".$this->query['username']."'";
			$result = mysql_query($dbString);

			if (mysql_num_rows($result) == 0) {			
				$bool = 't'; // userID is avaibility
			}	
			mysql_close($dbCon);	
			return $bool; // Reply userID avaibility status
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}
	
	private function createUser()  
	{
		// Create a new User on database
		$dbCon = mysql_connect('localhost','root','');
		
		if ($dbCon)
		{			
			mysql_select_db("textfarm", $dbCon);			
			date_default_timezone_set('Asia/Kuala_Lumpur');
			
			// Insert new User entry into database
			$privatephrase = sha1(round(microtime(true) * 1000) . 'private');			
			$dbString = "INSERT INTO User (userID, userPass, regDate, isActive, privatePassphrase, publicPassphrase)
					VALUES ('".trim($this->query['username'])."', '".trim($this->query['password'])."', '".date('Y-m-d')."','0', '".$privatephrase."', '')";			
			mysql_query($dbString);
			
			if (mysql_errno($dbCon) == '0') {		
				mysql_close($dbCon);
				return $privatephrase; // Reply privatePassphrase on successfull
			}
			else
			{
				TextFarm::sendResponse(204, '204 No Content'); // No Content
			}
		}
		else
		{
			die('Could not connect: ' . mysql_error());
		}
	}	
}

?>