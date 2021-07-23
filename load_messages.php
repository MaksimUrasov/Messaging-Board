<?php
$number_of_posts_per_page = 10;
$page_to_show = 3;
$calculate_offset = $page_to_show * $number_of_posts_per_page - $number_of_posts_per_page;

// $result = $Cal->calculate('5+7'); // 12
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
        //create_data_arrays();
        // var_dump($row);
        $downloaded_name = $row["name"];
        $downloaded_age =  convert_date_to_years_old($row["birth_date"]);
        $downloaded_message  = $row["message"];
       

        echo "<div class='container_for_one_old_message'>
                <div class='name_and_year_container'>
                <p class='old_name'>$downloaded_name, </p> 
                <p class='old_age'>$downloaded_age years.</p> 
                </div>
                <p class='old_message'>$downloaded_message</p> 
                </div>";
                        
         

        //  "id: " . $row["id"]. " - Name: " . $row["name"]. ", " . $row["birth_date"] . ", " . $row["email"] . ", " . $row["message"] .  "<br>";
    }
} else {
echo "There are no messages yet";
};

$conn->close();

function convert_date_to_years_old($date){
    $dob = new DateTime($date);
    $now = new DateTime();
    $age = $now->diff($dob);
    return $age;
}

