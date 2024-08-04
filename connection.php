<?php
$servername = "localhost";
$username = "serefiye_galaapps";
$password = ".Unc7JMZGWy6GzJ";
$dbname = "lokantaOtomasyon";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}