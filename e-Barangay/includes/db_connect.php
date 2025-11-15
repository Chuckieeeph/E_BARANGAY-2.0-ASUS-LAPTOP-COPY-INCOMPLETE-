<?php
// config/database.php
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP default
$dbname = "e_barangay";

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}
$con->set_charset("utf8mb4");
?>
