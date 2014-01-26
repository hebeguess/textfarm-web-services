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

/*	Authorize Keys Service - Test Scripts	*/

// AUTHKEYS GENERATION TEST METHOD
/*
$privatephrase='74135691b87b37sbd676e2718313656f081164f98';
$fileID='22';

$url='http://127.0.0.1/api/authkeys/create/' . $fileID;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase);
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
	
// UPDATE AUTHKEYS STATE TEST METHOD
/*
$privatephrase='74135691b87b37bd676e2718313656f081164f98';
$authkeys='a521bf40bac6952fe7f61571769470dd24ad8dce';
$authstate='activate'; // 'activate' or 'deactivate'
	
$url='http://127.0.0.1/api/authkeys/'.$authstate.'/'.$authkeys.'/';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
	
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);
*/
	
?>

