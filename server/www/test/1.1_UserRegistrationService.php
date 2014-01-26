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

/*	User Registration Service - Test Scripts	*/

/*
$url = '$url = 'http://127.0.0.1/api/register/';';

$username='test30';
$password='test30';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password.'');
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
	
<!--
<html>
	<head>
	<title>User Registration</title>
	</head>
	<body>
	<div>
	<br /><br />	
    <form action="<?= $url; ?>" name="form1" method="post">
	Username : <input type="text" name="username" value="<?= $username; ?>" /><br />
	Password : <input type="text" name="password" value="<?= $password; ?>" /><br />
	<input type="submit" value="Create New User" /><br />
    </form>
	</div>
	</body>
</html>
-->


