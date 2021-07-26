<?php

// DB created in serveriai.lt grapical UI, as long as user (!root) has no privileges to create a new table.
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

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection to DB failed: " . $e->getMessage();
}

// Create a table


// use exec() because no results are returned
try {
  $sql = "CREATE TABLE Posts (
    id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name TEXT(30) COLLATE utf8_general_ci NOT NULL,
    birth_date DATE NOT NULL,
    email TEXT(50) COLLATE utf8_general_ci,
    message TEXT(500) COLLATE utf8_general_ci NOT NULL
    )";

  $conn->exec($sql);
  echo "Table MyGuests created successfully";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}


$conn = null;

