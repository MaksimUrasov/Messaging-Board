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
require_once "pre_button_logic.php";
?>

        <main class="content">
            <h1 class="welcome">Welcome!</h1>
            <p class="welcome">Here you can leave a message for our team</p>
            
            <form  method="post"  action="/button_pressed_logic.php" onsubmit="JSActions()" >  
                
                <input type="text" name="first_name" value="<?php check_session_keys("first_name")?>" placeholder="First name" class="input first_name"  required>
                <div class="error_message first_name_err">* <?php check_session_keys("first_name_err")?></div><br>

                <input type="text" name="last_name" value="<?php check_session_keys("last_name")?>" placeholder="Last name" class="input last_name" required>
                <div class="error_message last_name_err">* <?php check_session_keys("last_name_err")?></div><br>

                <input type="date" name="birth" value="<?php check_session_keys("birth")?>" placeholder="Your date of birth" class="input birth" required>
                <div class="error_message birth_err">*Your date of birth <?php check_session_keys("birth_err")?></div><br>

                <input type="email" name="email" value="<?php check_session_keys("email")?>" placeholder="Your email" class="input email">
                <div class="error_message email_err"><?php check_session_keys("email_err")?></div><br>

                <textarea name="message"  placeholder="Please enter your message here" class="input message" required><?php check_session_keys("message")?></textarea >
                <div class="error_message message_err">* <?php check_session_keys("message_err")?></div><br>

                <div class="mandatory_notice">*These fields are mandatory</div><br>
                <button type="submit">Send your message</button>

            </form>

            <div class='server_success_message'><?php echo $server_message_ok; ?></div>
            <div class='server_error_message'><?php echo $server_message_err; ?></div>
            

            <button onclick="JSActions()" >test JS</button> 
            <!-- <button onclick="window.localStorage.clear();location.reload();">clear all fields</button>  -->

            <section class="container_for_old_messages">
                <p class="message_container_name">Message history</p>
                <?php
                require_once "load_messages.php";
                ?>
                <!-- <div class='container_for_one_old_message'>
                    <div class="name_and_year_container">
                        <p class="old_name">Darius Kaimynas</p> 
                        <p class="old_age">31 years</p> 
                    </div>
                    <p class="old_message">Laba diena!</p> 
                </div>

                <div class='container_for_one_old_message'>
                    <div class="name_and_year_container">
                        <p class="old_name">Darius Kaimynas</p> 
                        <p class="old_age">32 years</p> 
                    </div>
                    <p class="old_message">Laba diena!</p> 
                </div>

                <div class='container_for_one_old_message'>
                    <div class="name_and_year_container">
                        <p class="old_name">Darius Kaimynas</p> 
                        <p class="old_age">33 years</p> 
                    </div>
                    <p class="old_message">Laba diena!</p> 
                </div> -->

            <!-- </section>  this section is in the load_messages.php -->

        </main>
    </body>
</html>

<!-- 


All form fields should be protected against  SQL  intections. ? Parameterized Statements?

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