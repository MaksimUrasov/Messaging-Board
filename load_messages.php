<?php
$number_of_posts_per_page = 10;
$page_to_show = $_GET["page"] ?: 1;
$calculate_offset = $page_to_show * $number_of_posts_per_page - $number_of_posts_per_page;

// connect to DB
$servername = "localhost";
$username = "viedis_root";
$password = "barinme55ageb0ard";
$dbname = "viedis_messageboard";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn -> connect_error){
    die("Connection failed:" . $conn->connect_error);
}
// echo "connected succesfully". "<br>";


$sql = "SELECT * FROM Posts ORDER BY id DESC LIMIT $number_of_posts_per_page OFFSET $calculate_offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // var_dump($row);
        global $last_downloaded_id;
        $last_downloaded_id = $row["id"];
        $downloaded_name = $row["name"];
        $downloaded_age =  convert_date_to_years_old($row["birth_date"]);
        $downloaded_message  = $row["message"];
       

        echo "<div class='container_for_one_old_message'>
                <div class='name_and_year_container'>
                <p class='old_name'>$downloaded_name,&#160</p> 
                <p class='old_age'>$downloaded_age years.</p> 
                </div>
                <p class='old_message'>$downloaded_message</p> 
                </div>";

    }
} else {
echo "There are no messages yet";
};

$conn->close();

function convert_date_to_years_old($date){
    $dob = new DateTime($date);
    $now = new DateTime();
    $age = $now->diff($dob);
    return $age->format('%y');
}



//now I add pages buttons to the bottom of the message container:


$amount_of_entries = $last_downloaded_id + $number_of_posts_per_page - 1; // e.g. page shows 10 entries and the bottom entry has ID=3. Then the last entry will be 3+10-1=12.
$amount_of_pages = ceil($amount_of_entries / $number_of_posts_per_page);

function create_page_links(){
    global $amount_of_pages;

    echo "</section><div class='pages_container'>";
    
    for ($i = 1; $i <= $amount_of_pages; $i++) {
        echo "<a href='http://message.vienasmedis.lt/index.php?page=$i'>$i</a>";

        
    }
     
    echo "</div>";

};

create_page_links();



