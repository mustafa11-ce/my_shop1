<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "my_shop";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>