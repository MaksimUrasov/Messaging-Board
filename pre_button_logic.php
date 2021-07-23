<?php

session_start();
// print_r($_SESSION);
// $first_name = $last_name = $birth = $email = $message = "";
// $first_name_err = $last_name_err = $birth_err = $email_err = $message_err = "";

$server_message_ok = "";
$server_message_err = "";

function clear_session_and_show_server_messages(){  //this function is called in a div under the button.
    if(array_key_exists("DB_updated",$_SESSION)){
        // echo "<br>" . $_SESSION["DB_error"];   
        global $server_message_ok;
        $server_message_ok = $_SESSION["DB_updated"]; 
        session_destroy();
        session_start(); 
        // echo "have deleted the session. Message saved: " . $server_message_ok;
           
    }
    if(array_key_exists("DB_error",$_SESSION)){
        echo "<br>" . $_SESSION["DB_error"];
        global $server_message_err;
        $server_message_err = $_SESSION["DB_error"];
        session_destroy();
        session_start(); 
        // echo "have deleted the session. There is an error: " . $server_message_err;         
    }
    
}

clear_session_and_show_server_messages();








function check_session_keys($key){ //this function is needed for each form input value.
    if(array_key_exists($key,$_SESSION)){
        echo $_SESSION[$key];
    } else{
        echo "";
    }
};

function apply_additional_css(){
    if(count($_SESSION)>0){
        // print_r($_SESSION);
        foreach($_SESSION as $key => $value ){
            if (in_array($key."_err", array_keys($_SESSION)) ) { // checks if there is eg "name" and "name_err" in Session object
                // if current key (like "name") has his error message "name_err", then there is no need to mark "name" green.
                // do nothing to that $key, will have to add error css to _err later
            } else {
                if(str_contains($key, "_err") ){
                    add_css_error($key);
                } else {
                    add_css_complete($key);
                };

            }
        }
    }
} 


apply_additional_css();

// str_contains(string $haystack, string $needle):


function add_css_error($field){
    echo "<style type='text/css'>
    .$field {
        color: red;
        font-size: 100%;
    }   
    </style>";
};

function add_css_complete($field){
    echo "<style type='text/css'>
    .$field {
        pointer-events:none; 
        border-color:green;
    }   
    </style>";
};

