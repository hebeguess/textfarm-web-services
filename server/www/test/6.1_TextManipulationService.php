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

/*	Text Manipulation Service - Test Scripts	*/

// RETRIEVE CONTENT TEST METHOD ('POST' with parameters to modify XML content)
/*
$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$authKeys='2de304f2d71cbee360aa079f6cb26b86be1a2dc1'; // IF retrieving private content
$fileID='21'; // IF retrieving public content
$mime='text/xml';

$url='http://127.0.0.1/api/content/retrieve/'.$fileID.'/'; // Substitutable using $authKeys

// Adding a new node into XML on retrieval content,
// actual content stored on server preserves.
$action='add'; // [REQUIRED], Invoke API endpoint to perform Simple XML Manipulation
$nameSpace='Books'; // [REQUIRED], XML root node name
$nodeName='book'; // [REQUIRED], Node name to be append
$attribute='title'; // [OPTIONAL], Attribute name of $nodeName
$value='harry potter ep4'; // [OPTIONAL], Attribute value of $nodeName

// Removing specific node from XML on retrieval content,
// actual content stored on server preserves.
//$action='remove'; // [REQUIRED], Invoke API endpoint to perform Simple XML Manipulation
//$nameSpace='Books'; // [REQUIRED], XML root node name
//$nodeName='book'; // [REQUIRED], Node to be remove if attribute matched
//$attribute='title'; // [REQUIRED], attribute name
//$value='harry potter ep2'; // [REQUIRED], attribute value

	
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'publicphrase='.$publicphrase.'&mime='.$mime.'&action='.$action.'&nameSpace='.$nameSpace.'&nodeName='.$nodeName.'&attribute='.$attribute.'&value='.$value);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

// Force browser to render xml as text for better visualisation
header('Content-type: ' . 'text/plain'); 

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "\n\n";
echo "cURL Diagnosis :- \n";
echo "http_code : " . $info['http_code'] . "\n";
echo "content_type : " . $info['content_type'] . "\n";
echo "\n";
curl_close($ch);
*/

// INSERT CONTENT TEST METHOD ('POST' with parameters to modify XML content)
// DESCRIPTIONS : targeted xml file is already stored,
//                this method is used to add/remove xml node and store back to database
/*
$privatephrase='74135691b87b37bd676e2718313656f081164f98';
$fileID='21';
$action='remove'; // 'add' or 'remove'
	
$url='http://127.0.0.1/api/content/insert/'.$fileID.'/';
	
// Adding a new node into XML on target content, change saved back to database.
$action='add'; // [REQUIRED], Invoke API endpoint to perform Simple XML Manipulation
$nameSpace='Books'; // [REQUIRED], XML root node name
$nodeName='book'; // [REQUIRED], Node name to be append
$attribute='title'; // [OPTIONAL], Attribute name of $nodeName
$value='harry potter ep4'; // [OPTIONAL], Attribute value of $nodeName

// Removing specific node from XML on target content, change saved back to database.
//$action='remove'; // [REQUIRED], Invoke API endpoint to perform Simple XML Manipulation
//$nameSpace='Books'; // [REQUIRED], XML root node name
//$nodeName='book'; // [REQUIRED], Node to be remove if attribute matched
//$attribute='title'; // [REQUIRED], attribute name
//$value='harry potter ep2'; // [REQUIRED], attribute value


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase.'&action='.$action.'&nameSpace='.$nameSpace.'&nodeName='.$nodeName.'&attribute='.$attribute.'&value='.$value);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "\n\n";
echo "cURL Diagnosis :- \n";
echo "http_code : " . $info['http_code'] . "\n";
echo "content_type : " . $info['content_type'] . "\n";
echo "\n";
curl_close($ch);
*/

?>