<?php

// this file tests the inputs and redirects to the index.php page or to save_to_DB.php.

session_start();
session_destroy(); // This is necessary to delete old data, from previous "send message". All fresh data will be received via POST method.
session_start();   //destroying the Session from starting a new session, so I have to start session again

// print_r($_SESSION);
// echo "<br>";
// print_r($_POST);

// connect to DB
require_once 'manage_db.php';
$table_name = Connect_to_db_singletone_modified::$table_name;
$information_to_client_message_saved = "Your message has been saved. Thank you!"; 
$information_to_client_message_not_saved ="Saving to DB has failed";
$first_name = $last_name = $birth = $email = $message = "";

function test_input($data) {
    $data = trim($data); //Strip unnecessary characters (extra space, tab, newline)
    $data = stripslashes($data); //Remove backslashes (\) 
    $data = htmlspecialchars($data); //converts special characters to HTML entities.
    return $data;
};
$first_name = $last_name = $birth = $email = $message = $name = "";// to have global variables scope

function prepare_data_for_DB($fn, $ln, $b, $e, $m){
    $name = $fn . " " . $ln;
    $birth = $b;
    $email = $e ?: "NULL"; // to save text "NULL" to DB on later stage
    $message = $m;
    return array($name, $birth, $email, $message);
};

//below we check where the data came from- from index.php by POST method or by AJAX POST method:
function check_who_triggered_me(){
    if (array_key_exists("first_name", $_POST)) { // true means info came from index.php 
        
        // echo "have received POST data from index.php. <br>";
        // var_dump($_POST);

        //Function validate_input saves error messages to SESSION and returns true if there are errors + sanitized values in array+ errors. 
        $validation_result = validate_input($_POST["first_name"],$_POST["last_name"],$_POST["birth"],$_POST["email"],$_POST["message"]);
        // echo "Have validated the result: ";
        // print_r($validation_result);
        // echo "<br>";

        if($validation_result[0]){// if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 
            
            // $validation_result = validate_input($_POST["first_name"],$_POST["last_name"],$_POST["birth"],$_POST["email"],$_POST["message"]);
            // var_dump($validation_result);
            // global $first_name, $last_name, $birth, $email, $message;
            

            $_SESSION['first_name']= $validation_result[1][0]; 
            $_SESSION['last_name']= $validation_result[1][1];  
            $_SESSION['birth']= $validation_result[1][2];
            $_SESSION['email']= $validation_result[1][3];
            $_SESSION['message']= $validation_result[1][4];

            $_SESSION['first_name_err'] = $validation_result[2][0]; // Session will contain empty keys if there are no errors
            $_SESSION['last_name_err'] = $validation_result[2][1];
            $_SESSION['birth_err'] = $validation_result[2][2];
            $_SESSION['email_err'] = $validation_result[2][3];
            $_SESSION['message_err'] = $validation_result[2][4];

            // echo "There are errors, have saved errors and POST values to Session:";
            // var_dump($_SESSION);
            // echo "<br>";
            
    
        } else {//if there are no errors, wll run send_info_to_DB
            // global $first_name, $last_name, $birth, $email, $message;  
            
            // I can not prepare data before, f.e. on data validation step, because if the data contains error, it has to be returned for correction in the same state, not prepared for DB.
            $prepared_data_array = prepare_data_for_DB($validation_result[1][0],$validation_result[1][1],$validation_result[1][2],$validation_result[1][3],$validation_result[1][4]);
            $sending_to_DB_result = send_info_to_DB($prepared_data_array); // this function not only saves to DB, but as confirmation also returns an array.

            if($sending_to_DB_result[0]){ 
                //as long as we redirect, have to save messages to Session:
                $_SESSION['DB_updated'] = $sending_to_DB_result[1];
                // echo $_SESSION['DB_updated'];

            }else{
                $_SESSION['DB_error'] = $sending_to_DB_result[1] . "<br>" . "Request: ". $sending_to_DB_result[2] . "<br>" . $sending_to_DB_result[3];
                // echo $_SESSION['DB_error'];
            };
        }
    
        header("Location: index.php");
        exit();
            
        
    
    
    }elseif ( json_decode(file_get_contents("php://input")) ) { //  info was sent to me by AJAX in JSON format, so lets use it!
    
        $data_from_JSON = json_decode(file_get_contents("php://input"));
    
        // echo "Have received POST request from AJAX. Received data_from_JSON: ";
        // var_dump($data_from_JSON);
        // echo "<br>";
        $validation_result = validate_input($data_from_JSON->first_name,$data_from_JSON->last_name,$data_from_JSON->birth,$data_from_JSON->email,$data_from_JSON->message);
        // echo "validation_result is here:";
        // print_r($validation_result);

        if($validation_result[0]){
            //Function validate_input returns true if there are errors. 
            // if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 

            echo "That is very weird, but seems you have passed the wrong data in input fields. PHP input validator has found some mistakes.
            Try to refresh a page and resubmit a form. <br> Values and Errors: ";
            // here it is possible to JSONencode all POST values and Error messages to send them back to 
            // global $first_name, $last_name, $birth, $email, $message;
            
            echo json_encode($validation_result[1],$validation_result[2]);


            // var_dump($_SESSION);
        } else {
            
            $prepared_data_array = prepare_data_for_DB($validation_result[1][0],$validation_result[1][1],$validation_result[1][2],$validation_result[1][3],$validation_result[1][4]);
            $sending_to_DB_result = send_info_to_DB($prepared_data_array); // this function not only saves to DB, but as confirmation also returns an array.

            echo json_encode($sending_to_DB_result);

            // if($sending_to_DB_result[0]){
            //     // printed string will be seen by JS function saveToDB(), and displayed in Console Log
            //     //print_r($sending_to_DB_result) ; // here comes an array with 1 or 0 and error message from function send_info_to_DB()
            //     echo json_encode($sending_to_DB_result);
            // }else{
            //     echo "Something went wrong, message may be not saved to DB. <br>" . "Request: ". $sending_to_DB_result[1] . "<br>" . $sending_to_DB_result[2];
            // };
        };
    
       
    
        
    

    
        // there is no redirect back to index.php
    
    
    } 
}

check_who_triggered_me();
    
//this piece of code shall be executed any way:


function validate_input($fn, $ln, $b, $e, $m){ // JS makes same validation in browser, but it is better to recheck data on server      
    // global $first_name, $last_name, $birth, $email, $message; // I will need them in global scope so save in DB on send_info_to_DB() // I go away from global variables, they mess up everything

    $first_name = test_input($fn); 
    $last_name = test_input($ln);  
    $birth = test_input($b); // we save birth date to DB, exact age of customer will be calculated on loading the message.
    $email = test_input($e);
    $message = test_input($m);
    $there_is_an_error= false;

    $errors = array();
    $validated_messages = array($first_name, $last_name, $birth, $email, $message);
    

    if(!preg_match("/^[a-zA-Z-' ]*$/",$first_name)){
        $first_name_err =  "shall contain only letters and whitespaces."; 
        array_push($errors,$first_name_err);
        $there_is_an_error= true;
    };

    if(!preg_match("/^[a-zA-Z-' ]*$/",$last_name)){
        $last_name_err = "shall contain only letters and whitespaces.";
        array_push($errors,$last_name_err);
        $there_is_an_error= true;
    };


    if($birth > date("Y-m-d")){
        $birth_err = " can not be in the future!"; // the beginning of the sentence " *Your date of birth" is already displayed.
        array_push($errors,$birth_err);
        $there_is_an_error= true;
    };


    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_err = ": Seems there is a typing error.";  
        array_push($errors,$email_err);
        $there_is_an_error= true;
    };

    if(strlen($message)<3){
        $message_err =  "shall contain at least 3 characters";
        array_push($errors,$message_err);
        $there_is_an_error= true;
    } else if(strlen($message)>500){
        $message_err =  "shall contain less than 500 characters";  // this is DB limitation I have set for this field.
        array_push($errors,$message_err);
        $there_is_an_error= true;
    };


    
    return array($there_is_an_error, $validated_messages, $errors);
};

    


function send_info_to_DB($data_array){ // here we check if there are error messages saved in SESSION and return to index.php or save info to DB and return.
    
    global $information_to_client_message_saved, $information_to_client_message_not_saved;
    $name = $data_array[0];
    $birth = $data_array[1];
    $email = $data_array[2];
    $message = $data_array[3];
    // insert row into DB table
    try {
        global $table_name;

        $sql = "INSERT INTO $table_name (id, name, birth_date, email, message)
        VALUES (NULL, ?, ?, ?, ?)";

        // Connections_to_db::db_insert($sql,$name,$birth,$email,$message);

        $connection_object = new Connections_to_db;
        $connection_object->db_insert($sql,$name,$birth,$email,$message);
        
        $success = true; 
        return array($success, $information_to_client_message_saved); // this array will be visible in console log and JS will look for phraze "message saved to DB".

    } catch(PDOException $e) {
        $success = false; 
        return array($success, $information_to_client_message_not_saved,  $sql, $e->getMessage());// this array will be visible in console log
    }
    
    
    //echo "message sent to DB";

}



// the last thing that has to be done when all page elements are loaded - close connection to DB.
// Connections_to_db::db_close();
