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

/*	Argument Parsing Service - Test Scripts	*/

// RETRIEVE CONTENT TEST METHOD ('POST' with optional parameters to tweaks HTTP responeses)
/*
$publicphrase='9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684';
$fileID='16';

$attachment='false'; // default: attachment; filename=database_filename.sample
//	Supported Content-Disposition Return Types : -
// true (attachment)
// false (inline)
	
$mime='text/plain'; // default: 'text/html; charset=utf-8'
//	Supported MIME Return Types :-
// text/css
// text/csv
// text/html
// text/plain
// text/vcard
// text/xml
// image/svg+xml
// application/atom+xml
// application/ecmascript
// application/json
// application/javascript
// application/octet-stream
// application/rss+xml
// application/soap+xml
// application/xhtml+xml
// application/xml-dtd
// application/zip
// application/gzip

$encoding='UTF8'; // default: treating content as UTF8
//	Supported Encoding Auto Conversion Types :-
// UTF32
// UTF32BE
// UTF32LE
// UTF16
// UTF16BE
// UTF16LE
// UTF7
// UTF8
// ASCII
// EUCJP
// JIS
// ISO88591
// EUCCN
// EUCTW

$fileName='test'; // you desire file name
$fileExt='txt'; // you desire file extension

$url='http://127.0.0.1/api/content/retrieve/' . $fileID . '/';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'publicphrase='.$publicphrase.'&mime='.$mime.'&encoding='.$encoding.'&fileName='.$fileName.'&fileExt='.$fileExt.'&attachment='.$attachment);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_HEADER, 1);

// Force browser to render as text for better visualisation
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

?>