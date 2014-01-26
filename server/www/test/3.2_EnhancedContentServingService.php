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

/*	Enhanced Content Serving Service - Test Scripts	*/

// RETRIEVE CONTENT TEST METHOD ('POST' and private content retrieval)
/*
$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$authKeys='2de304f2d71cbee360aa079f6cb26b86be1a2dc1'; // private content's authorize keys

$url='http://127.0.0.1/api/content/retrieve/' . $authKeys . '/';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'publicphrase='.$publicphrase);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br /><br />";
echo "cURL Diagnosis :- <br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);

echo "<br /><br />";


// RETRIEVE CONTENT TEST METHOD ('POST' and public content retrieval)

$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$fileID='21'; // public content ID

$url='http://127.0.0.1/api/content/retrieve/' . $fileID . '/';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'publicphrase='.$publicphrase);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br /><br />";
echo "cURL Diagnosis :- <br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);

echo "<br /><br />";
*/


// RETRIEVE CONTENT TEST METHOD ('GET' and private content retrieval)
/*
$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$authKeys='e52abbdac3568ef96bbab0f74128c3c77621e608'; // private content's authorize keys

$url='http://127.0.0.1/api/content/retrieve/' . $authKeys . '/'. $publicphrase . '/';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

$response = curl_exec($ch);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
echo $response;
echo "<br /><br />";
echo "cURL Diagnosis :- <br />";
echo "http_code : " . $info['http_code'] . "<br />";
echo "content_type : " . $info['content_type'] . "<br />";
echo "<br />";
curl_close($ch);

echo "<br /><br />";


// RETRIEVE CONTENT TEST METHOD ('GET' and public content retrieval)

$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$fileID='17'; // public content ID

$url='http://127.0.0.1/api/content/retrieve/' . $fileID . '/'. $publicphrase . '/';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

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