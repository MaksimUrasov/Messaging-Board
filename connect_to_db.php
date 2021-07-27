<?php

// this file establishes a connection to DB and sets the necessary table name to createa and work with.

    $servername = "localhost";
    $username = "viedis_root";
    $password = "barinme55ageb0ard";
    $dbname = "viedis_messageboard";
    $table_name = "Posts3";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully. <br>";
    } catch(PDOException $e) {
        echo "Connection to DB failed: " . $e->getMessage();
    }
    