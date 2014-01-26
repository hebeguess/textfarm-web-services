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

/*	TextFarm API Main Handle	*/

include 'TextFarm.php';

//Process the request
$requestParser = TextFarm::processRequest(); 

// Obtain the path and turn into array
if(isset($_SERVER['PATH_INFO'])) {
	$requestPath = explode('/', trim($_SERVER['PATH_INFO'],'/'), 2);
}
else
{
	$dbConn = mysql_connect('localhost', 'root', '');
	if (!$dbConn) {
		die('Could not connect: ' . mysql_error());
	}
	
	$diag = '<pre><br />&nbsp;\'&gt; =-=-=-=-=- [TEXTFARM API DIANGNOSTICS PAGE] -=-=-=-=-='.
	'<br />&nbsp;\'= &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'.
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ='.
	'<br />&nbsp;\'= &nbsp;&nbsp;X-Powered-By :&nbsp;'.phpversion().' &nbsp; &nbsp; &nbsp'.
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;='.
	'<br />&nbsp;\'= &nbsp;&nbsp;Server &nbsp; &nbsp; &nbsp; :&nbsp;'.apache_get_version().
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ='.
	'<br />&nbsp;\'= &nbsp;&nbsp;MySQL Server :&nbsp;'.mysql_get_server_info().
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ='.
	'<br />&nbsp;\'= &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '.
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp='.
	'<br /> \'=-=-=-=-=-=-=-=-=-=-=-= [ STATUS ] =-=-=-=-=-=-=-=-=-=-='.
	'<br />&nbsp;\'= &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '.
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;='.
	'<br /> \'=  TextFarm API is currently Online. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ='.
	'<br /> \'=  Proceed with proper API request. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp  &nbsp; &nbsp; ='.
	'<br />&nbsp;\'= &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '.
	'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;='.
	'<br /> \'&lt; =-=-=-=-=-=-=-=-=-=-= [TEXTFARM] =-=-=-=-=-=-=-=-=-=-=</pre>';
	
	mysql_close($dbConn);
	TextFarm::sendResponse(200, $diag); // Forbidden :: REQUEST API ROOT ENDPOINT :: SHOW DIANGNOSTICS PAGE INSTEAD
}

// Allocating url path
$apiPath = array_shift($requestPath);
$restPath = array_shift($requestPath);

//echo "PATH_INFO == ";
//print_r ($requestPath);
//echo "<br /><br />";
//echo "_REQUEST == ";
//print_r ($_REQUEST);
//echo "<br />";

//echo "$ apiPath >> " . $apiPath;
//echo "&nbsp; &nbsp; &nbsp;"; 
//echo "$ restPath >> "; print_r ($restPath);
//echo "<br />"; echo "<br />";

//echo "TEST getMethod : ". $requestParser->getMethod() . "<br />";	
//echo "TEST getData : ". $requestParser->getData() . "<br />";	
//echo "TEST getHttpAccept : ". $requestParser->getHttpAccept() . "<br />";	
//echo "<br />";

switch($apiPath) {
case 'register':
	{
		// Check if using correct call method = 'POST'
		if($requestParser->getMethod() == 'POST') {
			include 'UserRegister.php';
			$registerObj = new UserRegister();
		}
		break;
	}
case 'passphrase':
	{
		// Check if using correct call method = 'POST'
		if($requestParser->getMethod() == 'POST') {
			include 'Passphrase.php';			
			
			if($restPath == 'public') {
				// Generate/Refresh publicPassphrase
				$passphrase = new Passphrase();	
				$publicphrase = $passphrase->generatePublicPhrase();
				TextFarm::sendResponse(200, $publicphrase, 'text/plain; charset=utf-8'); // OK
			}
			else if ($restPath == 'private')
			{
				// Refresh privatePassphrase
				$passphrase = new Passphrase();				
				$privatephrase = $passphrase->generatePrivatePhrase();
				TextFarm::sendResponse(200, $privatephrase, 'text/plain; charset=utf-8'); // OK
			}
			else
			{
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}			
		}	
		break;
	}
case 'content':
	{
		include 'ContentServing.php';		
		break;
	}
case 'authkeys':
	{
		// Check if using correct call method = 'POST'
		if($requestParser->getMethod() == 'POST') {
			include 'AuthKeys.php';		
			
			// Check if request for Create or Update AuthKeys
			if(strncasecmp($restPath, 'create/', 7) == 0) {	
				$authkeysObj = new AuthKeys();	
				$authkeysObj->generateAuthKeys();
			}
			else if(strncasecmp($restPath, 'activate/', 9) == 0 or strncasecmp($restPath, 'deactivate/', 11) == 0)
			{
				$authkeysObj = new AuthKeys();	
				$authkeysObj->modifyAuthKeys();			
			}
			else
			{
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}
		}		
		break;
	}
case 'directory':
	{
		// Check if using correct call method = 'POST'
		if($requestParser->getMethod() == 'POST') {
			include 'DirectoryManagement.php';

			// Check if privatePhrase presented
			if(isset($requestParser->getRequestVars()['privatephrase'])) {
				$phrase = trim($requestParser->getRequestVars()['privatephrase']);	
				
				// Check if request for List or Move Content Directory
				if($restPath == 'list') {		
					$list = DirectoryManagement::generateDirList($phrase);
					TextFarm::sendResponse(206, $list, 'text/xml');					
				}
				else if(strncasecmp($restPath, 'move/', 5) == 0)
				{
					$directoryObj = new DirectoryManagement();	
				}
				else
				{
					TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
				}				
			}
			else
			{
				TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
			}			
		}		
		break;
	}
default:
	{
		TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden
	}
}

TextFarm::sendResponse(403, '403 Forbidden'); // Forbidden

?>