<?php

$host = 'db';
$user = 'devuser';
$pass = 'devpass';

$databaseName = 'devdb';

$con = mysqli_connect($host,$user,$pass,$databaseName) or error($PN.'10');

?>