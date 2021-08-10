<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging board</title>
    <link rel="stylesheet" href="/styles.css">
    <script src="/script.js"></script>
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
            
            <form  method="post"  action="/button_pressed_logic.php" onsubmit="JSActions()" >  
                
                <input type="text" name="first_name" value="<?php get_session_value("first_name")?>" placeholder="First name" class="input first_name"  required>
                <div class="error_message first_name_err">* <?php get_session_value("first_name_err")?></div><br>

                <input type="text" name="last_name" value="<?php get_session_value("last_name")?>" placeholder="Last name" class="input last_name" required>
                <div class="error_message last_name_err">* <?php get_session_value("last_name_err")?></div><br>

                <input type="date" name="birth" value="<?php get_session_value("birth")?>" placeholder="Your date of birth" class="input birth" required>
                <div class="error_message birth_err">*Your date of birth <?php get_session_value("birth_err")?></div><br>

                <input type="email" name="email" value="<?php get_session_value("email")?>" placeholder="Your email" class="input email">
                <div class="error_message email_err"><?php get_session_value("email_err")?></div><br>

                <textarea name="message"  placeholder="Please enter your message here" class="input message" required><?php get_session_value("message")?></textarea >
                <div class="error_message message_err">* <?php get_session_value("message_err")?></div><br>

                <div class="mandatory_notice">*These fields are mandatory</div><br>
                <button type="submit">Send your message</button>

            </form>

            <div class='server_success_message'><?php echo $server_message_ok; ?></div>
            <div class='server_error_message'><?php echo $server_message_err; ?></div>
            

            <!-- <button onclick="JSActions()" >test JS</button>  -->
            
            <section class="container_for_old_messages">
                <p class="message_container_name" id="messages" >Message history</p>
                <?php
                Loading_messages::download_old_messages();
                Loading_messages::download_amount_of_old_messages();
                ?>
                <!-- <div class='container_for_one_old_message'>
                    <div class="name_and_year_container">
                        <p class="old_name">Darius Kaimynas</p> 
                        <p class="old_age">31 years</p> 
                    </div>
                    <p class="old_message">Laba diena!</p> 
                </div>


            </section>  the whole section is generated in the load_messages.php -->
            </section>
            <div class='pages_container'>
            <?php
                Create_links_to_pages::create_page_links($amount_of_entries, $number_of_posts_per_page); 
            ?>
            </div>


        </main>
    </body>
</html>

<!-- 



Siuo atveju kalbama apie OOP, reiketu susikurti klase, per kuria bendrausi su duomenu baze. Gali pasinaudoti constructorium uzmegsti 
rysi su duomenu baze, ar sukurti metoda tam skirta.
Taip pat gali pasidometi apie design patterns, duomenu bazei daznai naudojamas Singleton pattern, ir sis pattern'as leidzia tureti tik
 viena originalu rysi su duomenu baze.
Database klases tikslas butu uzmegsti rysi su duomenu baze, ir taip pat tureti klases metodus su kuriais atlieka select, insert, update, 
delete sql uzklausas. Kad sukurus sios klases objekta, butu lengva dirbti su DB.


If JavaScript is enabled:
After pressing the submit button, all fields have become inactive (not editable), and instead loader diagram should appear.
In case of success, the most recent message should be placed on top using JavaScript The oldest message should be removed from the screen. All form 
fields should be activated again.
In case any errors are detected, fields must be marked and fields should be activated so that user can edit their input.
In both cases, the loader should disappear and the button should appiear instead.

If JavaScript is turned off:
Pressing the button should reload the page. 


All messages should be paginated. Number of posts per page should be defined in constants.

If an e-mail was provided, the name and last name should become a link. (By clicking on the full name, an e-mail is triggered.)

Year must be calculated from the current date and the date of birth entered.


Bonus points:
Design Patterns are employed (Singleton, Factory, etc.)
The code should be clean, neat, with comments in English.

result:
Full source code and the database structure should be sent back to us as a proof of work done.  -->



<!-- 
Duomenų bazė:	viedis_messageboard
Serveris (host):	localhost
Naudotojo vardas:	viedis_root
Slaptažodis:	barinme55ageb0ard -->