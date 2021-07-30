<?php
// below code is necessary only once, to create a new DB, by simply running this file.

// DB created in serveriai.lt grapical UI, as long as user (!root) has no privileges to create a new table.

require_once 'manage_db.php';

$table_name = "A new awesome name for a table";

try {
  $sql = "CREATE TABLE $table_name (
      id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
      name TEXT(30) COLLATE utf8_general_ci NOT NULL,
      birth_date DATE NOT NULL,
      email TEXT(50) COLLATE utf8_general_ci,
      message TEXT(500) COLLATE utf8_general_ci NOT NULL
      )";
      
  $result = Connections_to_db::db_create($sql);
  echo "Table $table_name created successfully";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}