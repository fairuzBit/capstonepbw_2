<?php
date_default_timezone_set('Asia/Jakarta');

$servername = "mysql";
$username = "root";
$password = "root";
$db = "webdailymyjurnal";

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successful<hr>";
?>
