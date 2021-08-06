<?php

session_start();

// 1) option one: the piece of code that is needed when a webpage is loading for the first time, no errors or previous inputs

function get_session_value($key){ //this function is needed for each form input value.
    if(array_key_exists($key,$_SESSION)){
        echo $_SESSION[$key];
    } else{
        echo "";
        //print_r($_SESSION);
    }
};


// 2) option two: code below is necessary when the website is loaded not the first time: form has been submitted, but may contain errors.


$server_message_ok = "";
$server_message_err = "";

Dealing_with_form_errors::show_server_messages();
Dealing_with_form_errors::apply_additional_css();

class Dealing_with_form_errors {

    static public function show_server_messages(){
        if(array_key_exists("DB_updated",$_SESSION)){
            // echo "<br>" . $_SESSION["DB_error"];   
            global $server_message_ok;
            $server_message_ok = $_SESSION["DB_updated"]; 
            session_destroy(); // this is necessary to empty the session and allow user to enter a new message.
            session_start(); 
        }
        if(array_key_exists("DB_error",$_SESSION)){
            echo "<br>" . $_SESSION["DB_error"];
            global $server_message_err;
            $server_message_err = $_SESSION["DB_error"];    
            session_destroy(); // this is necessary to empty the session and allow user to enter a new message. //even there is an error?
            session_start();  
        }
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

}



// 3) Below is the code that has to be done either way - load all messages, including the new message if it has been saved

require_once 'manage_db.php'; // this has to be done before the "$table_name = Connect_to_db_singletone_modified::$table_name;" to access that class

//1)
$table_name = Connect_to_db_singletone_modified::$table_name; 
$number_of_posts_per_page = 10;
$page_to_show = (array_key_exists("page",$_GET)) ?  intval($_GET["page"]) : 1 ;
$calculated_offset = $page_to_show * $number_of_posts_per_page - $number_of_posts_per_page;
//2) 
$amount_of_entries = Loading_messages::download_amount_of_old_messages();
$amount_of_pages = ceil($amount_of_entries / $number_of_posts_per_page); 
// echo "amount_of_pages = $amount_of_pages";


class Loading_messages { // this class is called from index.php    // download old messages using PDO

    public static function download_old_messages(){
        global $table_name, $number_of_posts_per_page, $calculated_offset;
        
        try {

            $sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT $number_of_posts_per_page OFFSET $calculated_offset";

            $connection_object = new Connections_to_db;
            $result = $connection_object->db_select($sql);

            foreach($result as $k=>$v) {
                $old_message = new Old_message($v["email"],$v["name"],$v["birth_date"],$v["message"]);
                // $old_message->set_variables($v["name"],$v["birth_date"],$v["message"]);  // I better use __construct for to pass arguments
                $old_message->genereate_an_html();
            }
        } catch(PDOException $e) {
            echo "Error of downloading messages from DB: " . $e->getMessage();
        }
    }
    

    
    public static function download_amount_of_old_messages(){ // download number of entries in DB table
        global $table_name;
        try {
            $sql = "SELECT count(*) FROM $table_name";
            $connection_object = new Connections_to_db;
            $result = $connection_object->db_select($sql);
            $amount_of_entries = $result->fetchColumn();
            return $amount_of_entries;
        } catch(PDOException $e) {
            echo "Error of downloading amount of messages from DB: " . $e->getMessage();
        }  
        
    }  
};





//a class for one old message:
class Old_message {

    public function __construct($email, $name, $birth_date, $message) {
        $this->generated_email = ($email !== "NULL") ? $email : "" ;
        $this->generated_name = $this->generate_link_to_email($this->generated_email,$name);
        $this->generated_age =  $this->convert_date_to_years_old($birth_date);
        $this->generated_message = ucfirst($message);  // have added ucfirst built in function to capitalize the first letter, for better appearance.
        
    }

    public function generate_link_to_email($email,$name){
        if ($email){
            return "<a href='mailto:$email'>$name</a>";
        } else {
            return $name;
        }
    }

    public function genereate_an_html(){
        echo "<div class='container_for_one_old_message'>
        <div class='name_and_year_container'>
            <p class='old_name'>" . $this->generated_name . ",</p>  
            <p class='old_age'>" . $this->generated_age . " years.</p> 
        </div>
        <p class='old_message'>" . $this->generated_message . "</p> 
        </div>"; // &#160 represents a space character.
    }

    public function convert_date_to_years_old($b_date){
        $dob = new DateTime($b_date);
        $now = new DateTime();
        $age = $now->diff($dob);
        return $age->format('%y');
    }

};


class Create_links_to_pages { // this class is called from index.php // this class adds pages buttons to the bottom of the message container: (class for one function???)
    
    public static function create_page_links($nr_of_pages, $page_to_show){

        for ($i = 1; $i <= $nr_of_pages; $i++) {

            if($i == $page_to_show) { // if we are on a page one, there shall be no link to itself.
                echo "<p>$i</p>"; 
            }else {
                // page number is sent to $page_to_show variable via GET method, all index.php is reloaded:
                echo "<p><a href='http://message.vienasmedis.lt/index.php?page=$i,tag=#messages'>$i</a></p>"; 
            }                                                                                                    
        }

    }

};


// the last thing that has to be done when all page elements are loaded - close connection to DB.
// Connections_to_db::db_close();


// for ($i = 1; $i <= $amount_of_pages; $i++) {
//     echo "<a href='http://message.vienasmedis.lt/index.php?page=$i,tag=#messages'>$i</a>"; 
// };