<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DashboardDB";

// Create connection
$conn = new mysqli($servername, $username, $password);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// $sql_db = "CREATE DATABASE IF NOT EXISTS $dbname";
// if ($conn->query($sql_db) === TRUE) {
//     // echo "Database 'DashboardDB' checked/created successfully. <br>";
// } else {
//     // This die should generally not happen if the initial connection worked.
//     exit("Error checking/creating database: " . $conn->error);
// }
// $conn->close();


$conn = new mysqli($servername, $username, $password, $dbname);
// Re-check connection to the specific database
if ($conn->connect_error) {
    exit("Connection to $dbname failed: " . $conn->connect_error);
} else {
    // echo "Connected to database '$dbname' successfully. <br>";
}

// ---------------------------------------------------
// $sql_table = "CREATE TABLE IF NOT EXISTS tbl_users (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     firstname VARCHAR(30) NOT NULL,
//     lastname VARCHAR(30) NOT NULL,
//     email VARCHAR(50) UNIQUE NOT NULL,
//     designation VARCHAR(50),
//     password VARCHAR(255) NOT NULL,
//     phone_number VARCHAR(15),
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )";

// if ($conn->query($sql_table) === TRUE) {
//     // echo "Table 'users' checked/created successfully. <br>";
// } else {
//     die("Error creating table: " . $conn->error);
// }

// Note: The connection ($conn) remains open for use in signup.php
