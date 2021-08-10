<?php

class View {

    function __construct(){
        
    }


    

    // 1) option one: the piece of code that is needed when a webpage is loading for the first time, no errors or previous inputs

    public function get_session_value($key){ //this function is needed for each form input value.
        if(array_key_exists($key,$_SESSION)){
            echo $_SESSION[$key];
        } else{
            echo "";
            //print_r($_SESSION);
        }
    }

    // $view->apply_additional_css();
    
    // class Dealing_with_form_errors {
    
        public function show_server_messages(){
            // $server_message_ok = "";
            // $server_message_err = "";
            if(array_key_exists("DB_updated",$_SESSION)){
                // echo "<br>" . $_SESSION["DB_error"];   
                // global $server_message_ok;
                $server_message_ok = $_SESSION["DB_updated"]; 
                echo  $server_message_ok;
                echo "<div id='server_success_message'><?php echo {$_SESSION["DB_updated"]}; ?></div>";
            }
            if(array_key_exists("DB_error",$_SESSION)){
                // echo "<br>" . $_SESSION["DB_error"];
                // global $server_message_err;
                // $server_message_err = $_SESSION[""DB_error];    
                echo "<div id='server_error_message'><?php echo {$_SESSION["DB_error"]}; ?></div>";
            }
            // session_destroy(); // this is necessary to empty the session and allow user to enter a new message. //even there is an error?
            // session_start(); 

             
            
        }
    
        static public function apply_additional_css(){
            if(count($_SESSION)>0){
                // print_r($_SESSION); // session contains keys like "name" and "name_err" and values with text. 
                foreach($_SESSION as $key => $value ){
                    if (in_array($key."_err", array_keys($_SESSION)) ) { // checks if there is eg "name" and "name_err" in Session object
                        // if current key (like "name") has his error message "name_err", then there is no need to mark "name" green.
                        // do nothing to that $key, will have to add error css to "name_err" on her iteration later.
                    } else {
                        if(str_contains($key, "_err") ){
                            self::add_css_error($key);    // error messages change their css 
                        } else {
                            self::add_css_complete($key);  // if ther is no error, message is not displayed, but field changes its appearance.
                        };
        
                    }
                }
            }
        } 
        
        static public function add_css_error($field){
            echo "<style type='text/css'>
            .$field {
                color: red;
                font-size: 100%; 
            }   
            </style>"; // font size under the submit fields originally is small, so have to increase it to 100%, means to normal
        }
    
        static public function add_css_complete($field){
            echo "<style type='text/css'>
            .$field {
                pointer-events:none;
                border-color:green;
            }   
            </style>"; //disabled="disabled" can not be used here, as it prevents form data to be sent on later stage.
                        // in JS I have used readOnly = true;
        }
    
    // }
    








}


