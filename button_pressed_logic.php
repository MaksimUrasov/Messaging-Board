<?php

// this file tests the inputs and redirects to the index.php page or to save_to_DB.php.

session_start();
session_destroy(); // this one prevents from starting a new session, so I have to start session again
session_start();


// $first_name = $last_name = $birth = $email = $message = "";
// $first_name_err = $last_name_err = $birth_err = $email_err = $message_err = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
};

      
$first_name = $_SESSION['first_name']= test_input($_POST["first_name"]);
$last_name = $_SESSION['last_name']= test_input($_POST["last_name"]);
$birth = $_SESSION['birth']= test_input($_POST["birth"]);
$email = $_SESSION['email']= test_input($_POST["email"]);
$message = $_SESSION['message']= test_input($_POST["message"]);


function validate_and_save_errors_to_session(){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // echo "post method received" . "<br>";
        // print_r($_POST);
    
        if(!preg_match("/^[a-zA-Z-' ]*$/",$_POST["first_name"])){
            $_SESSION['first_name_err'] = "Name shall contain only letters and whitespaces.";
            // add_css_red("first_name");
        };
    
        if(!preg_match("/^[a-zA-Z-' ]*$/",$_POST["last_name"])){
            $_SESSION['last_name_err'] = "Last name shall contain only letters and whitespaces.";
            // add_css_red("last_name");
        };
    
    
        if($_POST["birth"] > date("Y-m-d")){
            $_SESSION['birth_err'] = " can not be in the future!"; // the beginning of the sentence " *Your date of birth" is already displayed.
            // add_css_red("birth");
        };
    
    
        if(!empty($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
            $_SESSION['email_err'] = "Seems there is an error in the email";
            // add_css_red("email");     
        };
    
        if(strlen($_POST["message"])<3){
            $_SESSION['message_err']=  "Your message shall contain at least 3 characters";
            // add_css_red("message");   
        } else if(strlen($_POST["message"])>500){
            $_SESSION['message_err']=  "Your message shall contain less than 500 characters";  // this is DB limitation I have set for this field.
            // add_css_red("message");
        };
    };
};

validate_and_save_errors_to_session();

function return_or_send_info_to_DB(){
    foreach($_SESSION as $key => $value ){
        if(str_contains($key, "err")) {
            header("Location: index.php");
            exit();
            // echo "<br> $key";
        } 
    }

    global $first_name, $last_name, $birth, $email, $message;
    $name = $first_name." ".$last_name;
    $email = $email ?: "NULL";    
    
    // connect to DB
    require_once "connect_to_db.php";


    // insert row into table
    try {
        $sql = "INSERT INTO $table_name (id, name, birth_date, email, message)
        VALUES (NULL, ?, ?, ?, ?)";
        $result = $pdo->prepare($sql);
        $result->execute(array($name,$birth,$email,$message));
        $_SESSION['DB_updated']="Your message has been saved. Thank you!";

    } catch(PDOException $e) {
        // echo "Error: " . $e->getMessage();  // this shows error on same page, but as long as we redirect, have to save error to Session:
        $_SESSION['DB_error']="Something went wrong, message not saved to DB. <br>" . "Request: ". $sql . "<br>" . $e->getMessage();
    }

    $pdo= null;
    
    header("Location: index.php");
    exit;

    echo "message sent to DB";

}

return_or_send_info_to_DB();
