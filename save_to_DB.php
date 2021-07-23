<?php

session_start();

// echo "<br><h2>SUCCESS!!!!</h2> <br> We are now sending info to DB, please relax";


$name = $_SESSION["first_name"]." ".$_SESSION["last_name"];
$birth = $_SESSION["birth"];
$email = $_SESSION["email"] ?: NULL;
$message = $_SESSION["message"];
// echo "<br> $name <br> $birth <br> $email <br> $message";


// connect to DB
$servername = "localhost";
$username = "viedis_root";
$password = "barinme55ageb0ard";
$dbname = "viedis_messageboard";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn -> connect_error){
//     die("Connection failed:" . $conn->connect_error);
// }
// // echo "connected succesfully". "<br>";

// // insert row into table
// $sql = "INSERT INTO Posts (id, name, birth_date, email, message)
// VALUES (NULL, '${name}', '${birth}', '${email}', '${message}');";

// if ($conn->query($sql) === TRUE) {
//      // $last_id = $conn->insert_id;
//      // echo "New record created successfully." 
//         $_SESSION['DB_updated']="Your message has been saved. Thank you!";
// } else {
//     $_SESSION["DB_error"] = "DB Error: " . $sql . "<br>" . $conn->error;
//     // echo "DB Error: " . $sql . "<br>" . $conn->error;
// }

// $conn->close();

header("Location: index.php");
exit;