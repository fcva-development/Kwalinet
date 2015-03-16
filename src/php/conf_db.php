<?php
//Settings
$host = 'rdbms.strato.de';
$user = 'U2056795';
$pass = '84Px5a!@';

$db = 'DB2056795';

//Connection
$con = mysqli_connect($host, $user, $pass, $db); //connect to database
$mysqli = new mysqli($host, $user, $pass, $db); //Create object for object orientated mysqli useage
?>