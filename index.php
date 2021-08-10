<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging board</title>
    <link rel="stylesheet" href="/styles.css">
    
        <!-- below is to delete GEThttp://localhost:3000/favicon.ico  404 ERROR -->
    <link rel="icon" type="image/ico" href="/img/1.ico" >
</head>
<body>

<?php
require_once "functions.php";
?>

        <main class="content">
            <h1 class="welcome">Welcome!</h1>
            <p class="welcome">Here you can leave a message for our team</p>
            <br>
            <noscript>                     
                <br> 
                <p>Sorry, your browser does not support JavaScript! That means that this messaging board will not work as fast as it could.</p>
                <br>
            </noscript>
            
            <form  method="post" action="/button_pressed_logic.php" onsubmit="Actions()" >  
                
                <input type="text" class="input first_name" name="first_name" value="<?php get_session_value("first_name")?>" placeholder="First name"   required>
                <div class="error_message first_name_err">* First Name <?php get_session_value("first_name_err")?></div><br>

                <input type="text" class="input last_name" name="last_name" value="<?php get_session_value("last_name")?>" placeholder="Last name"  required>
                <div class="error_message last_name_err">* Last name <?php get_session_value("last_name_err")?></div><br>

                <input type="date" class="input birth" name="birth" value="<?php get_session_value("birth")?>" placeholder="Your date of birth"  required>
                <div class="error_message birth_err">* Your date of birth <?php get_session_value("birth_err")?></div><br>

                <input type="email" class="input email" name="email" value="<?php get_session_value("email")?>" placeholder="Your email" >
                <div class="error_message email_err">  E-mail<?php get_session_value("email_err")?></div><br>

                <textarea name="message" class="input message" placeholder="Please enter your message here"  required><?php get_session_value("message")?></textarea >
                <div class="error_message message_err">* Message <?php get_session_value("message_err")?></div><br>

                <div class="mandatory_notice">*These fields are mandatory</div><br>
                
                <div id="button_created_by_JS"></div>

                <button class="button" type="submit">Send your message</button>

                <div class="lds-facebook"><div></div><div></div><div></div></div> <!-- copied from https://loading.io/css/ -->

            </form>

            <div id='server_success_message'><?php echo $server_message_ok; ?></div>
            <div id='server_error_message'><?php echo $server_message_err; ?></div>
            
            <!-- <button onclick="JSActions()" >test JS</button>  -->
            
            <section id="container_for_old_messages">
                <p id="name_for_message_container" >Message history</p>
                <?php
                //3)
                Loading_messages::download_old_messages();
                ?>
                <!-- <div class='container_for_one_old_message'>  // this is the example message
                    <div class="name_and_year_container">
                        <p class="old_name">Darius Kaimynas</p> 
                        <p class="old_age">31 years</p> 
                    </div>
                    <p class="old_message">Laba diena!</p> 
                </div> -->

            </section>
            <div class='page_links_container'>
                <?php
                    Create_links_to_pages::create_page_links($amount_of_pages, $page_to_show); 
                ?>
            </div>
            
        </main>
    </body>
    <script src="/script.js"></script> 
</html>



<!-- 
// I chose JSON because it is faster and better than XML: https://www.w3schools.com/js/js_json_xml.asp


mvc pattern
JS OOP

Bonus points:
Design Patterns are employed (Singleton, Factory, etc.)
-->



