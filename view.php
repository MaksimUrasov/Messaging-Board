<?php

session_start();

require_once 'manage_db.php'; // Connect_to_db_singletone_modified is needed here

class View {

    function __construct(){
        $this->number_of_posts_per_page = 10;
        $this->table_name = Connect_to_db_singletone_modified::$table_name; // this variable has to be in Model
        $this->page_to_show = (array_key_exists("page",$_GET)) ?  intval($_GET["page"]) : 1 ;
        $this->calculated_offset = $this->page_to_show * $this->number_of_posts_per_page - $this->number_of_posts_per_page;
        $this->amount_of_entries = $this->download_amount_of_old_messages();
        $this->amount_of_pages = ceil($this->amount_of_entries / $this->number_of_posts_per_page);
    }


       public function load_html(){
        require_once "header.php"; 

        require_once "html_body.php";
        echo "</html>";

        $this->apply_additional_css();

    }
    

    public function get_session_value($key){ //this function is needed for each form input value.
        if(array_key_exists($key,$_SESSION)){
            echo $_SESSION[$key];
        } else{
            echo "";
        }
    }

    

    
    public function show_server_message_ok(){
        if(array_key_exists("DB_updated",$_SESSION)){
            echo $_SESSION["DB_updated"];   
        }

    }      
    public function show_server_message_err(){
        if(array_key_exists("DB_error",$_SESSION)){
            echo $_SESSION["DB_error"];  
        }

    }   
        
    
    
    public function  apply_additional_css(){

        foreach ($_SESSION as $key => $value) {
            if (str_contains($key, "_err") && isset($value) ) { //ther is error message
                $this->add_css_error($key);
            } elseif(str_contains($key, "_err")) { // no errors
                $key_without_err = str_replace("_err", "",$key);

                $this->add_css_complete($key_without_err);
            } // do nothing with the input values in Session, we need only to change appearance according to error messages in session.
        }
    }
        
    public function add_css_error($field){
        echo "<style type='text/css'>
        .$field {
            color: red;
            font-size: 100%; 
        }   
        </style>"; // font size under the submit fields originally is small, so have to increase it to 100%, means to normal
    }

    public function add_css_complete($field){
        echo "<style type='text/css'>
        .$field {
            pointer-events:none;
            border-color:green;
        }   
        </style>"; //disabled="disabled" can not be used here, as it prevents form data to be sent on later stage.
                    // in JS I have used "disabled"= true, because info is being sent not via POST;
    }
    



    public function download_old_messages(){
        // global $table_name, $number_of_posts_per_page, $calculated_offset;
        
        try {

            $sql = "SELECT * FROM $this->table_name ORDER BY id DESC LIMIT $this->number_of_posts_per_page OFFSET $this->calculated_offset";

            $connection_object = new Connections_to_db;
            $result = $connection_object->db_select($sql);

            foreach($result as $k=>$v) {
                $old_message = new Old_message($v["email"],$v["name"],$v["birth_date"],$v["message"]);
                // $old_message->set_variables($v["name"],$v["birth_date"],$v["message"]);  // I better use __construct for to pass arguments
                            
            }
        } catch(PDOException $e) {
            echo "Error of downloading messages from DB: " . $e->getMessage();
        }
    }

    public function download_amount_of_old_messages(){ // download number of entries in DB table
        // global $table_name;
        try {
            $sql = "SELECT count(*) FROM $this->table_name";
            $connection_object = new Connections_to_db;
            $result = $connection_object->db_select($sql);
            $number_of_entries = $result->fetchColumn();
            return $number_of_entries;
        } catch(PDOException $e) {
            echo "Error of downloading amount of messages from DB: " . $e->getMessage();
        }  
        
    }  
    
    public function create_page_links(){
        
        for ($i = 1; $i <= $this->amount_of_pages; $i++) {

            if($i == $this->page_to_show) { // if we are on a page one, there shall be no link to itself.
                echo "<p>$i</p>"; 
            }else {
                // page number is sent to $page_to_show variable via GET method, all index.php is reloaded:
                echo "<p><a href='http://message.vienasmedis.lt/index.php?page=$i,tag=#messages'>$i</a></p>"; 
            }                                                                                                    
        }

    }

}



//a class for one old message: (am not sure how to include it into View object)
class Old_message {

    public function __construct($email, $name, $birth_date, $message) {
        $this->generated_email = ($email !== "NULL") ? $email : "" ;
        $this->generated_name = $this->generate_link_to_email($this->generated_email,$name);
        $this->generated_age =  $this->convert_date_to_years_old($birth_date);
        $this->generated_message = ucfirst($message);  // have added ucfirst built in function to capitalize the first letter, for better appearance.
        $this->genereate_an_html();
    }

    public function generate_link_to_email($email,$name){
        if ($email){
            return "<a href='mailto:$email'>$name</a>";
        } else {
            return $name;
        }
    }

    public function convert_date_to_years_old($b_date){
        $dob = new DateTime($b_date);
        $now = new DateTime();
        $age = $now->diff($dob);
        return $age->format('%y');
    }

    public function genereate_an_html(){
        echo "<div class='container_for_one_old_message'>
        <div class='name_and_year_container'>
            <p class='old_name'>" . $this->generated_name . ",</p>  
            <p class='old_age'>" . $this->generated_age . " years.</p> 
        </div>
        <p class='old_message'>" . $this->generated_message . "</p> 
        </div>"; 
    }


};

