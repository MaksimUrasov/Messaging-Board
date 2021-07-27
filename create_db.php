<?php

// DB created in serveriai.lt grapical UI, as long as user (!root) has no privileges to create a new table.


//connect to DB
require_once("connect_to_DB.php");

// Create a table
// use exec() because no results are returned
try {
  $sql = "CREATE TABLE $table_name (
    id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name TEXT(30) COLLATE utf8_general_ci NOT NULL,
    birth_date DATE NOT NULL,
    email TEXT(50) COLLATE utf8_general_ci,
    message TEXT(500) COLLATE utf8_general_ci NOT NULL
    )";

  $conn->exec($sql);
  echo "Table $table_name created successfully";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}


$conn = null;

