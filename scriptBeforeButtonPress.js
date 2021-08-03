// 1) actions that are needed BEFORE the button is pressed//

//First, JS has to take control over the form if JS is enabled:
document.getElementsByTagName("form")[0].removeAttribute("action"); // this one says: "hey form, Captain JS is here, so do not process the action, give control to me!"
// only removing the "action" attribute from a form was not good, because that caused page reload after button press in Safari. So had to add below:
document.getElementsByTagName("form")[0].setAttribute("action", "javascript:void(0);")


// 2) actions that are needed AFTER the button is pressed//

function Actions(){   

  
    // first, small circle: form fields validation, same as in PHP. 
    // I could use AJAX to use same php script, but I think better to do the same validation in browser.

    var firstName = document.getElementsByName("first_name")[0].value;
    var lastName = document.getElementsByName("last_name")[0].value;
    var birthDate = document.getElementsByName("birth")[0].value;
    var email = document.getElementsByName("email")[0].value;
    var message = document.getElementsByName("message")[0].value;
    var inputsOk = []; // here I will save info about correct inputs

    var firstNameErrNode = document.getElementsByClassName("first_name_err")[0];
    var lastNameErrNode = document.getElementsByClassName("last_name_err")[0];
    var birthDateErrNode = document.getElementsByClassName("birth_err")[0];
    var emailErrNode = document.getElementsByClassName("email_err")[0];
    var messageErrNode = document.getElementsByClassName("message_err")[0];
    var inputsWithErrors = []; // here I will save all validation errors.

    // validate the input and save results to error variables:

    function validateInputAndSaveResults(){
        inputsOk = [];
        inputsWithErrors = []; //have to null them, to start ower after resubmitting data

        if (!/^[a-zA-Z-' ]*$/.test(firstName)) {
            firstNameErrNode.innerText = "First name shall contain only letters and whitespaces.";
            inputsWithErrors.push("first_name_err");
            console.log(inputsWithErrors);
        }else{
            inputsOk.push("first_name");
            console.log(inputsOk);
        }

        if (!/^[a-zA-Z-' ]*$/.test(lastName)) {
            lastNameErrNode.innerText = "Last name shall contain only letters and whitespaces.";
            inputsWithErrors.push("last_name_err");
            console.log(inputsWithErrors);
        }else{
            inputsOk.push("last_name");
            console.log(inputsOk);
        }

        var today = new Date();
        if (Date.parse(birthDate) > today) {
            birthDateErrNode.innerText = "Your birth date can not be in the future!";
            inputsWithErrors.push("birth_err");
            console.log(inputsWithErrors);
        }else{
            inputsOk.push("birth"); 
            console.log(inputsOk);
        }

        if ( email!="" &&  !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
            emailErrNode.innerText = "Seems there is a typing error, such email is not valid.";
            inputsWithErrors.push("email_err");
            console.log(inputsWithErrors);
        }else{
            inputsOk.push("email"); 
            console.log(inputsOk);
        }

        if (message.length<3) {
            messageErrNode.innerText = "Message shall contain at least 3 characters";        
            inputsWithErrors.push("message_err");
            console.log(inputsWithErrors);  
        } else if(message.length>500){
            messageErrNode.innerText = "Message shall contain less than 500 characters"  ;
            inputsWithErrors.push("message_err");
            console.log(inputsWithErrors);        
        } else {
            inputsOk.push("message"); 
            console.log(inputsOk);
        }

    }
    validateInputAndSaveResults()

    function addCssGreen(node){
        node.setAttribute("disabled", true); // I can not add this property via css class, so have to add it directly to html node.
        node.classList.add("green");
    }
    
    function addCssRed(node){
        node.classList.add("red");
    }

    function returnNormalAppearance(node){
        node.classList.remove("red");
        node.textContent = "Accepted";
    }



    // then, check for remaining errors and if there are- report errors and stay on small cycle. If no errors, launch a bigger circle: save info to DB
    function applyErrorsCssOrSave(){ 
        if (inputsWithErrors.length>0) {  //if there are some fresh errors on input
            for (let i = 0; i < inputsWithErrors.length; i++) {  //mark all errors red
                const classNameForErrorMessage = inputsWithErrors[i];
                addCssRed(document.getElementsByClassName(classNameForErrorMessage)[0])
                
            }

            for (let i = 0; i < inputsOk.length; i++) { // mark all good inputs green and "accepted" message instead of error message.
                const ClassNameOfInput = inputsOk[i];
                addCssGreen(document.getElementsByClassName(ClassNameOfInput)[0]);
                
                var correspondingErrorMessageNode = document.getElementsByClassName(ClassNameOfInput+"_err")[0];
                //console.log(correspondingErrorMessageNode.classList);
                correspondingErrorMessageNode.textContent = "Accepted"

                //if value was incorrect and a now became correct, we have to delete error message:
                if (correspondingErrorMessageNode.classList.contains("red")) {   
                    returnNormalAppearance(correspondingErrorMessageNode);
                    // node.textContent = "Accepted";
                }
            }


        } else { // if there are no errors or all the errors are fixed
            
            // first correct all remaining error messages:
            for (let i = 0; i < inputsOk.length; i++) {
                const ClassNameOfInput = inputsOk[i];
                addCssGreen(document.getElementsByClassName(ClassNameOfInput)[0]);
                returnNormalAppearance(document.getElementsByClassName(ClassNameOfInput+"_err")[0]) //  delete all error messages that may be left hanging:
            }

            // then hide button and launch spinner:
            const button = document.getElementsByTagName("button")[0]
            button.style.display = "none";
            const spinner = document.getElementsByClassName("lds-facebook")[0]
            spinner.style.display = 'block';

            //then AJAX





        }

       


    }
    applyErrorsCssOrSave()
    
    
    
};

// Actions()



// All form fields should be protected against XSS, SQL / JavaScript intections. HTML input should not be allowed. Inputs containing HTML text should be cleaned from any HTML code before they are saved in database.


// If JavaScript is enabled:

// In case of success, the most recent message should be placed on top using JavaScript The oldest message should be removed from the screen. All form 
// fields should be activated again.

// In both cases, the loader should disappear and the button should appiear instead.

