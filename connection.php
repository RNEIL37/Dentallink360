<?php
$host = "localhost";
$user = "root";       // default for XAMPP
$pass = "";           // default empty password
$db   = "dentallink";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
