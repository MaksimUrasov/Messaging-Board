<?php

// DB created in serveriai.lt UI, as long as user (!root) has no privileges to create a new table.
//connect

$servername = "localhost";
$username = "viedis_root";
$password = "barinme55ageb0ard";
$dbname = "viedis_messageboard";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn -> connect_error){
    die("Connection failed:" . $conn->connect_error);
}
echo "connected succesfully". "<br>";

// Create a table

$sql = "CREATE TABLE Posts (
    id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name TEXT(30) COLLATE utf8_general_ci NOT NULL,
    birth_date DATE NOT NULL,
    email TEXT(50) COLLATE utf8_general_ci,
    message TEXT(500) COLLATE utf8_general_ci NOT NULL
    )";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully";
  } else {
    echo "Error creating table: " . $conn->error;
  }
  
  $conn->close();

