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

/*	Directory Management Service - Test Scripts	*/

// GENERATE DIRECTORY LIST TEST METHOD
/*
$url='http://127.0.0.1/api/directory/list/';

$privatephrase='74135691b87b37bd676e2718313656f081164f98';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase);
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

// MOVE CONTENT TEST METHOD
/*
$privatephrase='74135691b87b37bd676e2718313656f081164f98';
$fileID='15';
$fileName='super.nfo'; // name your desire new filename
$permission='public'; // 'public' or 'private'

$url='http://127.0.0.1/api/directory/move/'.$fileID.'/';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'privatephrase='.$privatephrase.'&fileName='.$fileName.'&permission='.$permission);
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

