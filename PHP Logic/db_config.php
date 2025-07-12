<?php

// DATABSE CONNECTION 
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'skillquest';

// ESTABLISHING CONNECTION
$conn = mysqli_connect($host, $user, $password, $database);

// CONNECTION FAILED
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
