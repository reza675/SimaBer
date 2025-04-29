<?php

$hostname = "localhost" ;
$username = "root" ;
$password = "" ;
$database = "simaber" ;

$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Gagal terhubung ke database. " . mysqli_connect_error());
}