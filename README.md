# lombard
<?php

function getConnection(){

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lombard";
$conn = new mysqli($servername, $username, $password, $dbname);
return $conn;
}
