<?php

//set all necessary variables:
$number_of_posts_per_page = 10;

function set_variable_page_to_show() {
    global $page_to_show;
    if (array_key_exists("page",$_GET)) {
        $page_to_show = intval($_GET["page"]);
    }else{
        $page_to_show = 1;
    }
};
set_variable_page_to_show();

$calculated_offset = $page_to_show * $number_of_posts_per_page - $number_of_posts_per_page;
$amount_of_entries = null;



//set a class:
class Old_message
{
    public function __construct($name, $birth_date, $message) {
        $this->downloaded_name = $name;
        $this->downloaded_age =  $this->convert_date_to_years_old($birth_date);
        $this->downloaded_message = $message;  
    }

    public function genereate_an_html(){
        echo "<div class='container_for_one_old_message'>
        <div class='name_and_year_container'>
        <p class='old_name'>" . $this->downloaded_name . ",&#160</p> 
        <p class='old_age'>" . $this->downloaded_age . " years.</p> 
        </div>
        <p class='old_message'>" . $this->downloaded_message . "</p> 
        </div>";
    }

    public function convert_date_to_years_old($b_date){
        $dob = new DateTime($b_date);
        $now = new DateTime();
        $age = $now->diff($dob);
        return $age->format('%y');
    }

};





// connect to DB
$servername = "localhost";
$username = "viedis_root";
$password = "barinme55ageb0ard";
$dbname = "viedis_messageboard";



try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection to DB failed: " . $e->getMessage();
}


// download old messages using PDO
try {

    $stmt_one = $conn->prepare("SELECT * FROM Posts ORDER BY id DESC LIMIT $number_of_posts_per_page OFFSET $calculated_offset");
    $stmt_one->execute();
  
    foreach($stmt_one->fetchAll() as $k=>$v) {
        var_dump($v);
        echo "<br>";
        // $old_message = new Old_message($v["name"],$v["birth_date"],$v["message"]);
        // // $old_message->set_variables($v["name"],$v["birth_date"],$v["message"]);  // I better use __construct for to pass arguments
        // $old_message->genereate_an_html();
    }
} catch(PDOException $e) {
    echo "Error of downloading data from DB: " . $e->getMessage();
}



// download number of entries in DB table

try {
    $amount_of_entries = $conn->query("SELECT count(*) FROM Posts")->fetchColumn();

} catch(PDOException $e) {
    echo "Error of downloading data from DB: " . $e->getMessage();
}

// finish the connection
$conn = null;





//now I add pages buttons to the bottom of the message container:


function create_page_links(){
    global $amount_of_pages, $amount_of_entries, $number_of_posts_per_page;
    $amount_of_pages = ceil($amount_of_entries / $number_of_posts_per_page);

    echo "</section><div class='pages_container'>";
    
    for ($i = 1; $i <= $amount_of_pages; $i++) {
        echo "<a href='http://message.vienasmedis.lt/index.php?page=$i,tag=#messages'>$i</a>"; 
    }

    echo "</div>";

};

create_page_links();



