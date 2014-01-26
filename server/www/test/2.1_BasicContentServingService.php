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

/*	Basic Content Serving Service - Test Scripts	*/

/*	WARNING :	DEPRECATED AFTER IMPLEMANTATION of 3.2 Enhanced Content Serving Service */
/*				However it doesn't break this scripts functionality 					*/

// INSERT CONTENT TEST METHOD
/*
$url='http://127.0.0.1/api/content/insert/';

// Maximum content length allowed: 16MB
//$filePath='C:\foo.sample';
//$fp=fopen($filePath, 'r');
//$content=fread($fp, filesize($filePath));
//$content=addslashes($content);
//fclose($fp);
// OR
$content='Hello World, Foo';

$privatephrase='b0ef03e1d35c3185e0fa04671d23dc22a740e55a';
$filename='foo.sample';
$permission='public'; // 'public' or 'private'


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase.'&filename='.$filename.'&permission='.$permission.'&content='.$content);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br /><br />";
echo "cURL Diagnosis :- <br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);
*/

// RETRIEVE CONTENT TEST METHOD ('POST' and public content retrieval)
/*
$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$fileID='22';

$url='http://127.0.0.1/api/content/retrieve/' . $fileID;


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'publicphrase='.$publicphrase);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br /><br />";
echo "cURL Diagnosis :- <br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);
*/
	
?>

