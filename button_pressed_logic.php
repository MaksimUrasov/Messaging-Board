<?php

// this file tests the inputs and redirects to the index.php page or to save_to_DB.php.

session_start();
session_destroy(); // This is necessary to delete old data, from previous "send message". All fresh data will be received via POST method.
session_start();   //destroying the Session from starting a new session, so I have to start session again


// connect to DB
require_once 'manage_db.php';
$table_name = Connect_to_db_singletone_modified::$table_name;
$confirmation_to_client_message_saved = "Your message has been saved. Thank you!"; 

function test_input($data) {
    $data = trim($data); //Strip unnecessary characters (extra space, tab, newline)
    $data = stripslashes($data); //Remove backslashes (\) 
    $data = htmlspecialchars($data); //converts special characters to HTML entities.
    return $data;
};
$first_name = $last_name = $birth = $email = $message = $name = "";// to have global variables scope

//below we check where the data came from- from index.php by POST method or by AJAX POST method:

if (array_key_exists("first_name", $_POST)) { // true means info came from index.php 
    // var_dump($_POST);
    if(validate_and_save_errors_to_session($_POST["first_name"],$_POST["last_name"],$_POST["birth"],$_POST["email"],$_POST["message"])){
        //Function validate_and_save_errors_to_session saves error messages to SESSION and returns true if there are errors. 
        // if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 
                    
        global $first_name, $last_name, $birth, $email, $message;
        $_SESSION['first_name']= $first_name; 
        $_SESSION['last_name']= $last_name;  
        $_SESSION['birth']= $birth;
        $_SESSION['email']= $email;
        $_SESSION['message']= $message;


    } //if there are no errors, wll run send_info_to_DB
        
    $name = $first_name." ".$last_name;
    $email = $email ?: "NULL"; // to save text "NULL" to DB on later stage
    $sending_to_DB_result = send_info_to_DB($name,$birth,$email,$message); // this function not only saves to DB, but as confirmation also returns an array.

    if($sending_to_DB_result[0]){
        //as long as we redirect, have to save messages to Session:
        $_SESSION['DB_updated'] = $confirmation_to_client_message_saved;
    }else{
        $_SESSION['DB_error'] = "Something went wrong, message not saved to DB. <br>" . "Request: ". $sending_to_DB_result[1] . "<br>" . $sending_to_DB_result[2];
    };

    // echo "there are some errors, now page will be redirected to index";
    header("Location: index.php");
    exit();
        
    


}else {
    $data = json_decode(file_get_contents("php://input"));
    // echo "Server has has received POST request from AJAX. Received data: " . $data . "<br>";
    // var_dump($data);
    // var_dump($_POST);
 
    if(validate_and_save_errors_to_session($data->first_name,$data->last_name,$data->birth,$data->email,$data->message)){
        //Function validate_and_save_errors_to_session returns true if there are errors. 
        // if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 
        echo "That is very weird, but seems you have passed the wrong data in input fields. PHP input validator has found some mistakes.
        Try to refresh a page and resubmit a form. <br> Values and Errors: ";
        var_dump($_SESSION);
    };

    $name = $first_name." ".$last_name; 
    $email = $email ?: "NULL"; // to save text "NULL" to DB on later stage
    $sending_to_DB_result = send_info_to_DB($name,$birth,$email,$message);

    if($sending_to_DB_result[0]){
        echo $sending_to_DB_result[0];

    }else{
        echo "Something went wrong, message not saved to DB. <br>" . "Request: ". $sending_to_DB_result[1] . "<br>" . $sending_to_DB_result[2];
    };

    // there is no redirect back to index.php


} 
    
//this piece of code shall be executed any way:


function validate_and_save_errors_to_session($fn, $ln, $b, $e, $m){ // JS makes same validation in browser, but it is better to recheck data on server      
    global $first_name, $last_name, $birth, $email, $message; // I will need them in global scope to save in DB on send_info_to_DB()

    $first_name = test_input($fn); 
    $last_name = test_input($ln);  
    $birth = test_input($b); // we save birth date to DB, exact age of customer will be calculated on loading the message.
    $email = test_input($e);
    $message = test_input($m);
    $there_is_an_error= false;

    if(!preg_match("/^[a-zA-Z-' ]*$/",$first_name)){
        $_SESSION['first_name_err'] = "shall contain only letters and whitespaces.";
        $there_is_an_error= true;
    };

    if(!preg_match("/^[a-zA-Z-' ]*$/",$last_name)){
        $_SESSION['last_name_err'] = "shall contain only letters and whitespaces.";
        $there_is_an_error= true;
    };


    if($birth > date("Y-m-d")){
        $_SESSION['birth_err'] = " can not be in the future!"; // the beginning of the sentence " *Your date of birth" is already displayed.
        $there_is_an_error= true;
    };


    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['email_err'] = ": Seems there is a typing error.";  
        $there_is_an_error= true;
    };

    if(strlen($message)<3){
        $_SESSION['message_err']=  "shall contain at least 3 characters";
        $there_is_an_error= true;
    } else if(strlen($message)>500){
        $_SESSION['message_err']=  "shall contain less than 500 characters";  // this is DB limitation I have set for this field.
        $there_is_an_error= true;
    };
    
    return $there_is_an_error;
};

    


function send_info_to_DB($name,$birth,$email,$message){ // here we check if there are error messages saved in SESSION and return to index.php or save info to DB and return.
    
    // insert row into DB table
    try {
        global $table_name;

        $sql = "INSERT INTO $table_name (id, name, birth_date, email, message)
        VALUES (NULL, ?, ?, ?, ?)";

        $connection_object = new Connections_to_db;
        $connection_object->db_insert($sql,$name,$birth,$email,$message);
        
        $success = true; 
        return array($success);

    } catch(PDOException $e) {
        $success = false; 
    }
    return array($success, $sql, $e->getMessage());
    
    //echo "message sent to DB";

}
