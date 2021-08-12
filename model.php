<?php



// connect to DB
require_once 'manage_db.php';

class Model {

    public function __construct(){
        $this->table_name = Connect_to_db_singletone_modified::$table_name;
        $this->information_to_client_message_saved = "Your message has been saved. Thank you!"; 
        $this->information_to_client_message_not_saved ="Saving to DB has failed";
        // $first_name = $last_name = $birth = $email = $message = "";  have to declare them
    }

    public function proceed_the_data($indexOne_ajaxZero){

        session_start();
        session_destroy(); // This is necessary to delete old data, from previous "send message". All fresh data will be received via POST method.
        session_start();   //destroying the Session from starting a new session, so I have to start session again
 

        if ($indexOne_ajaxZero) {// true means info came from index.php, then we have to take POST data:
            $validation_result = $this->validate_input($_POST["first_name"],$_POST["last_name"],$_POST["birth"],$_POST["email"],$_POST["message"]);

            if($validation_result[0]){// if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 
                $this->save_info_to_session($validation_result);
          
            } else {//if there are no errors, wll run send_info_to_DB
                
                // I can not prepare data before, f.e. on data validation step, because if the data contains error, it has to be returned for correction in the same state, not prepared for DB.
                $prepared_data_array = $this->prepare_data_for_DB($validation_result[1][0],$validation_result[1][1],$validation_result[1][2],$validation_result[1][3],$validation_result[1][4]);
                $sending_to_DB_result = $this->send_info_to_DB($prepared_data_array); // this function not only saves to DB, but as confirmation also returns an array.
                var_dump( $sending_to_DB_result);

                if($sending_to_DB_result[0]){ 
                    //as long as we redirect, have to save messages to Session:
                    $_SESSION['DB_updated'] = $sending_to_DB_result[1];
                    echo $_SESSION['DB_updated'];
                }else{
                    $_SESSION['DB_error'] = $sending_to_DB_result[1] . "<br>" . "Request: ". $sending_to_DB_result[2] . "<br>" . $sending_to_DB_result[3];
                    echo $_SESSION['DB_error'];
                    
                };
            }
          
            header("Location: index.php");
            exit();

        } else {//  info was sent to me by AJAX in JSON format, so lets use it!
            $data_from_JSON = json_decode(file_get_contents("php://input"));
            $validation_result = $this->validate_input($data_from_JSON->first_name,$data_from_JSON->last_name,$data_from_JSON->birth,$data_from_JSON->email,$data_from_JSON->message);
      
            if($validation_result[0]){
              echo "That is very weird, but seems you have passed the wrong data in input fields. PHP input validator has found some mistakes.
              Try to refresh a page and resubmit a form. <br> Values and Errors: ";
              echo json_encode($validation_result[1],$validation_result[2]);

              // var_dump($_SESSION);
            } else {
                
              $prepared_data_array = $this->prepare_data_for_DB($validation_result[1][0],$validation_result[1][1],$validation_result[1][2],$validation_result[1][3],$validation_result[1][4]);
              $sending_to_DB_result = $this->send_info_to_DB($prepared_data_array); // this function not only saves to DB, but as confirmation also returns an array.
      
              echo json_encode($sending_to_DB_result);
            };
            // there is no need to redirect back to index.php
        }
    }
    
    


    public function validate_input($fn, $ln, $b, $e, $m){ // JS makes same validation in browser, but it is better to recheck data on server      
  
        $first_name = $this->test_input($fn); 
        $last_name = $this->test_input($ln);  
        $birth = $this->test_input($b); // we save birth date to DB, exact age of customer will be calculated on loading the message.
        $email = $this->test_input($e);
        $message = $this->test_input($m);
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
    }

    


    public function test_input($data) {
        $data = trim($data); //Strip unnecessary characters (extra space, tab, newline)
        $data = stripslashes($data); //Remove backslashes (\) 
        $data = htmlspecialchars($data); //converts special characters to HTML entities.
        return $data;
    }

    public function save_info_to_session($validation_result){
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
    }


    public function prepare_data_for_DB($fn, $ln, $b, $e, $m){
        $name = $fn . " " . $ln;
        $birth = $b;
        $email = $e ?: "NULL"; // to save text "NULL" to DB on later stage
        $message = $m;
        return array($name, $birth, $email, $message);
    }

    public function send_info_to_DB($data_array){
    
        
        $name = $data_array[0];
        $birth = $data_array[1];
        $email = $data_array[2];
        $message = $data_array[3];
        // insert row into DB table
        try {
               
            $sql = "INSERT INTO $this->table_name (id, name, birth_date, email, message)
            VALUES (NULL, ?, ?, ?, ?)";
    
            // Connections_to_db::db_insert($sql,$name,$birth,$email,$message);
    
            $connection_object = new Connections_to_db;
            $connection_object->db_insert($sql,$name,$birth,$email,$message);
            
            $success = true; 
            return array($success, $this->information_to_client_message_saved); // this array will be visible in console log and JS will look for phraze "message saved to DB".
    
        } catch(PDOException $e) {
            $success = false; 
            return array($success, $this->information_to_client_message_not_saved,  $sql, $e->getMessage());// this array will be visible in console log
        }
        
        //echo "message sent to DB";
    }
    
};