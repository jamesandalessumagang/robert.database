<?php
$servername = "localhost"; // Or your server address
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "robert"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
