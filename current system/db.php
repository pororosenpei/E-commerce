<?php
$servername = "localhost"; // Your server name or IP address
$username = "root";        // Database username
$password = "";            // Database password
$dbname = "ecwm";          // Database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
