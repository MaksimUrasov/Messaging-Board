// -----------------------THE NEW, OOP MVC STYLE--------------------------



class Model {
    constructor(view) {
        this.view = view
        this.today = new Date();
    }


    validateInput(firstName,lastName,birthDate,eMail,msg){
        result = {};
        // inputsOk = {};
        // inputsWithErrors = {}; //have to null them, to start ower after resubmitting data

        // result.containsErrors = false;

        result.first_name = firstName; // here key names shall be same as Form classes, to use them later to change appearance
        result.last_name = lastName;
        result.birth = birthDate;
        result.email = eMail;
        result.message = msg;
    
        // result.firstNameErr = "";
        // result.lastNameErr = "";
        // result.birthDateErr  = "";
        // result.eMailErr = "";
        // result.msgErr = "";

        if (!/^[a-zA-Z-' ]*$/.test(firstName)) {
            result.first_name_err = "First name shall contain only letters and whitespaces.";
            // result.containsErrors = true;
        }else{
            // inputsOk.push("first_name");
            // console.log(inputsOk);
        }

        if (!/^[a-zA-Z-' ]*$/.test(lastName)) {
            result.last_name_err = "Last name shall contain only letters and whitespaces.";
            // result.containsErrors = true;
        }else{
            // inputsOk.push("last_name");
            // console.log(inputsOk);
        }

        
        if (Date.parse(birthDate) > today) {
            result.birth_err = "Your birth date can not be in the future!"
            // result.containsErrors = true;
        }else{
            // inputsOk.push("birth"); 
            // console.log(inputsOk);
        }

        if ( eMail!="" &&  !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(eMail)) {
            result.email_err = "Seems there is a typing error, such email is not valid."
            // result.containsErrors = true;
        }else{
            // inputsOk.push("email"); 
            // console.log(inputsOk);
        }

        if (msg.length<3) {
            result.message_err ="Message shall contain at least 3 characters";
            // result.containsErrors = true;
        } else if(msg.length>500){
            result.message_err = "Message shall contain less than 500 characters";  
            // result.containsErrors = true;      
        } else {
            // inputsOk.push("message"); 
            // console.log(inputsOk);
        }

        return result;

    }

    saveToDB(givenObject) { // object shall contain only data to save, no errors

        firstName = givenObject.first_name;
        lastName = givenObject.last_name;
        birthDate = givenObject.birth;
        eMail = givenObject.email;
        msg = givenObject.message;

        const xhttp = new XMLHttpRequest();
        // xhttp.onload = function() {
        //     // here I will parse the data
        //   //document.getElementById("txtHint").innerHTML = this.responseText;
        // }
        xhttp.open("POST", "/run_button_press.php", true);
        xhttp.setRequestHeader("Content-Type", "application/json, charset=utf-8");
        xhttp.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                
                try {// if there are errors, they will be saved in JSON message too
                    let response = JSON.parse(this.responseText);  // response shall contain an object, which includes true/false and message.

                    if (response["success_or_not"]) {// "1"- means "am fine, your message saved"

                        this.view.showServerMessages(response["text_message"], null); 
                        console.log(response);

                        this.view.removeOldMessageAndAddNew(firstName,lastName, birthDate, eMail, msg)
                        this.view.removeInputValues(givenObject);


                    }  // server responded that message not saved
                    this.view.showServerMessages(null,response["text_message"]);
                    console.log(response);
    

                } catch (error) {  // JSON could not be parsed correctly
                    this.view.showServerMessages(null,`Something went wrong :( Message may be not saved. JSON not parsed, error: ${error}`);
                    console.log("Here is unparsed JSON: " + this.responseText)                           
                }
                     
            
            } else { // connection to server has failed
                this.view.showServerMessages(null,"Connection to server has failed");
                console.log('Error Code: '+ xhttp.status);
                console.log('Error Message: '+ xhttp.statusText);
            }

            // despite there may be errorrs, this piece of code shall be executed anyway:
            hideOrShow("lds-facebook","button");
            restoreInputErrorMessageText();
            hideOrShow(null, "mandatory_notice");
            for (let i = 0; i < inputsOk.length; i++) {
                let classNameOfInput = inputsOk[i];
                removeCssGreen(classNameOfInput);                            
            }

        };
        let messageObject = {first_name:firstName,last_name:lastName,birth:birthDate,email:eMail,message:msg}; // must be JS object
        xhttp.send(JSON.stringify(messageObject));
    }
    
    
}

class View {
    constructor() {

        this.firstNameErrNode = document.getElementsByClassName("first_name_err")[0];
        this.lastNameErrNode = document.getElementsByClassName("last_name_err")[0];
        this.birthDateErrNode = document.getElementsByClassName("birth_err")[0];
        this.emailErrNode = document.getElementsByClassName("email_err")[0];
        this.msgErrNode = document.getElementsByClassName("message_err")[0];

        // this.inputsOk = []; // here I will save info about correct inputs
        // this.inputsWithErrors = []; // here I will save all validation errors.

        // Below variables are necessary to restore these text values after successfull submit:
        this.firstNameErrText =  firstNameErrNode.innerText;
        this.lastNameErrText =  lastNameErrNode.innerText;
        this.birthDateErrText =  birthDateErrNode.innerText;
        this.emailErrText =  emailErrNode.innerText;
        this.msgErrText =  msgErrNode.innerText;

        // this.today = new Date();

        this.serverSuccessMessageNode = document.getElementById("server_success_message") 
        this.serverErrorMessageNode = document.getElementById("server_error_message") 
    }    

    cnangeNotesAndInputsAppearance(validatedObject){
        //here we have 3 options: 
        // there were no errors and now no errors - fine, lets save info to DB
        // there were no errors but now some appeared - have to mark new errors and confirm good inputs
        // there were errors and now no errors  - have to remove error messages and then save info to DB
        // there were errors and now still have some errors - have to mark new errors and confirm good inputs

        for (const [key, value] of Object.entries(validatedObject)) {
                // Input fields have 2 statuses: normal and green. 
                // Notes have 3 statuses: normal, red with error message, accepted.
            if (key == Object.keys(validatedObject).includes(key+"_err") )  { // e.g. key is "name" and there is nameErr in the array, then no need to mark NAME input green
                continue

            } else if (key != Object.keys(validatedObject).includes(key+"_err") ) { //there is no corresponding errors to that input field  
                this.valueToGreen(key); //and mark all good inputs green 
                this.noteToAccepted(key+"_err") //and note changed to "accepted" instead of error message.

            } else if (key.includes("_err")) {  
                this.noteToRed(key, value);
            }
             
        }
    }


    valueToGreen(classToChange){
        let node = document.getElementsByClassName(classToChange)[0];
        node.setAttribute("disabled", true); // I can not add this property via css class, so have to add it directly to html node.
        node.classList.add("green");
    }
    
    valueToNormal(classToChange){
        let node = document.getElementsByClassName(classToChange)[0];
        node.removeAttribute("disabled");
        node.classList.remove("green");
    }
    
    noteToRed(classToChange,newErrorNote){
        let node = document.getElementsByClassName(classToChange)[0];
        node.classList.add("red");
        node.textContent = newErrorNote;
    }
    
    noteToAccepted(classToChange){
        let node = document.getElementsByClassName(classToChange)[0];
        node.classList.remove("red");
        node.textContent = "Accepted";
    }

    noteToNormal(classToChange, newMessage){
        let node = document.getElementsByClassName(classToChange)[0];
        node.textContent = newMessage;
    }
    
    hideOrShow(classToHide, classToShow) {
        if (classToHide) {
            const hideNode = document.getElementsByClassName(classToHide)[0]
            hideNode.classList.add("hide"); 
        }
        if (classToShow) {
            const showNode = document.getElementsByClassName(classToShow)[0]
            showNode.classList.remove("hide");  //Cannot read property 'classList' of undefined 
        }
    }
    
    
    restoreInputErrorMessageText(){
        firstNameErrNode.innerText = firstNameErrText
        lastNameErrNode.innerText = lastNameErrText
        birthDateErrNode.innerText = birthDateErrText
        emailErrNode.innerText = emailErrText
        msgErrNode.innerText = msgErrText
    }
    
    removeInputValues(givenObject){
        for (let i = 0; i < Object.keys(givenObject).length; i++) {
            let classNameOfInput = Object.keys(givenObject)[i];
            document.getElementsByClassName(classNameOfInput)[0].value = "";                            
        }
    }

    createName(firstName,lastName,eMail) {
        if (eMail){
            return `<a href='mailto:${eMail}'>${firstName} ${lastName}</a>`
        }
        return `${firstName} ${lastName}`;
    }

    calculateAge(birthDate) {
        
        this.resultInMiliseconds = today - new Date(birthDate)
        // let result = (today - new Date(birthDate) ) ;
        return new Date(resultInMiliseconds).getFullYear()-1970;// convert miliseconds to date after 1970 (eg 1 year will be 1971-1970)
    }

    showServerMessages(goodMessage, badMessage){
        this.serverSuccessMessageNode.innerHTML = goodMessage;
        this.serverErrorMessageNode = badMessage;
    }


    removeOldMessageAndAddNew(firstName,lastName, birthDate, eMail, msg){

        // let oldMessagesNodesArray = ;
        let containerNodes = document.getElementsByClassName("container_for_one_old_message");
        // console.log(containerNodes);
        var lastMessageContainerNode = containerNodes[containerNodes.length-1];
        // console.log(lastMessageContainerNode);
        //  let firstMessageContainerNode = oldMessagesNodesArray[0]; 

        //prepare a place for a new message and create a container for that message:
        let parentNode =  document.getElementById("container_for_old_messages");
        let referenceNode = document.getElementsByClassName("container_for_one_old_message")[0];
        let newMessageNode = lastMessageContainerNode.cloneNode(true);// does not matter what message do we copy, we will change its values later.
        // let insertedMessageNode = parentNode.insertBefore(newMessageNode, referenceNode);
        parentNode.insertBefore(newMessageNode, referenceNode);


        //then add fresh input values to that newly created message container (which is now a clone of the last message)
        document.getElementsByClassName("old_name")[0].innerHTML = this.createName(firstName,lastName,eMail);
        document.getElementsByClassName("old_age")[0].innerHTML = `${this.calculateAge(birthDate)} years.`;
        document.getElementsByClassName("old_message")[0].innerHTML = msg;

        

        // then hide the last message (hide does not work because user can post several messages, so each time the last has to be deleted)
        lastMessageContainerNode.remove();
    }
}

class Controller {
    constructor(model, view) {
        this.model = model
        this.view = view
        this.today = new Date();
        takeOverControl() //First, JS has to take control over the form if JS is enabled
    };


    takeOverControl() {
        document.getElementsByTagName("form")[0].removeAttribute("action"); // this one says: "Hey FORM, Captain JS is here, so do not process
        // the action, give control to me!"
        // only removing the "action" attribute from a form was not good, because that caused page reload after button press in Safari. 
        // So had to add below:
        document.getElementsByTagName("form")[0].setAttribute("action", "javascript:void(0);")
        console.log("I am ready to continue, but this function has to be simplified");
    }

    action(){   // this function is triggered when the button is pressed
  

    
        let firstName = document.getElementsByName("first_name")[0].value; 
        let lastName = document.getElementsByName("last_name")[0].value;
        let birthDate = document.getElementsByName("birth")[0].value;
        let eMail = document.getElementsByName("email")[0].value;  // here in JS I can not use same variable name as had in PHP, to create an object let messageObject.  
        let msg = document.getElementsByName("message")[0].value; // email and message were identical. Objects keys have to be 
            
        this.view.serverSuccessMessageNode.innerHTML = "";// if the last message has been sent and user submits another message.
        this.view.serverErrorMessageNode.innerHTML = "";

        // first, small circle: form fields validation, same as in PHP. 
        // I could use AJAX to use same php script, but I think better to do the same validation in browser.
        
        let validationResultObject = this.model.validateInput(firstName,lastName,birthDate,eMail,msg);
        if (Object.keys(validationResultObject) == "_err") { //if there are some fresh errors on input
            this.view.cnangeNotesAndInputsAppearance(validationResultObject);
        } else { // if there are no errors or all the errors are fixed (but old errors may be still on the screen)
            this.view.cnangeNotesAndInputsAppearance(validationResultObject); // amend all errors that may be there to accepted.
            
            // then hide button and launch spinner: // better to add class, as it can be removed
            this.view.hideOrShow("button", "lds-facebook");
            this.view.hideOrShow("mandatory_notice", null);

            this.model.saveToDB(validationResultObject) // DB response will give instructions to view class directly

        }

    };


}

const view = new View();
const model = new Model(view);
const app = new Controller(model,view);







// // -----------------------THE OLD, FUNCTIONAL STYLE--------------------------

// // 1) actions that are needed BEFORE the button is pressed

// //First, JS has to take control over the form if JS is enabled:
// document.getElementsByTagName("form")[0].removeAttribute("action"); // this one says: "hey form, Captain JS is here, so do not process the action, give control to me!"
// // only removing the "action" attribute from a form was not good, because that caused page reload after button press in Safari. So had to add below:
// document.getElementsByTagName("form")[0].setAttribute("action", "javascript:void(0);")



// let firstNameErrNode = document.getElementsByClassName("first_name_err")[0];
// let lastNameErrNode = document.getElementsByClassName("last_name_err")[0];
// let birthDateErrNode = document.getElementsByClassName("birth_err")[0];
// let emailErrNode = document.getElementsByClassName("email_err")[0];
// let msgErrNode = document.getElementsByClassName("message_err")[0];

// let inputsOk = []; // here I will save info about correct inputs
// let inputsWithErrors = []; // here I will save all validation errors.

// // Below variables are necessary to restore these text values after successfull submit:
// let firstNameErrText =  firstNameErrNode.innerText;
// let lastNameErrText =  lastNameErrNode.innerText;
// let birthDateErrText =  birthDateErrNode.innerText;
// let emailErrText =  emailErrNode.innerText;
// let msgErrText =  msgErrNode.innerText;

// let today = new Date();

// let serverSuccessMessageNode = document.getElementById("server_success_message") 
// let serverErrorMessageNode = document.getElementById("server_error_message") 

// // Functions for visual appearance :
// function addCssGreen(classToChange){
//     let node = document.getElementsByClassName(classToChange)[0];
//     node.setAttribute("disabled", true); // I can not add this property via css class, so have to add it directly to html node.
//     node.classList.add("green");
// }

// function removeCssGreen(classToChange){
//     let node = document.getElementsByClassName(classToChange)[0];
//     node.removeAttribute("disabled");
//     node.classList.remove("green");
// }

// function addCssRed(classToChange){
//     let node = document.getElementsByClassName(classToChange)[0];
//     node.classList.add("red");
// }

// function removeColorAndChangeToAccepted(classToChange){
//     let node = document.getElementsByClassName(classToChange)[0];
//     node.classList.remove("red");
//     node.textContent = "Accepted";
// }

// function hideOrShow(classToHide, classToShow) {
//     if (classToHide) {
//         const hideNode = document.getElementsByClassName(classToHide)[0]
//         hideNode.classList.add("hide"); 
//     }
//     if (classToShow) {
//         const showNode = document.getElementsByClassName(classToShow)[0]
//         showNode.classList.remove("hide");  //Cannot read property 'classList' of undefined 
//     }
// }


// function restoreInputErrorMessageText(){
//     firstNameErrNode.innerText = firstNameErrText
//     lastNameErrNode.innerText = lastNameErrText
//     birthDateErrNode.innerText = birthDateErrText
//     emailErrNode.innerText = emailErrText
//     msgErrNode.innerText = msgErrText
// }

// function removeInputValues(){
//     for (let i = 0; i < inputsOk.length; i++) {
//         let classNameOfInput = inputsOk[i];
//         document.getElementsByClassName(classNameOfInput)[0].value = "";                            
//     }
// }


// function calculateAge(birthDate) {
//     let resultInMiliseconds = today - new Date(birthDate)
//     // let result = (today - new Date(birthDate) ) ;
//     return new Date(resultInMiliseconds).getFullYear()-1970;// convert miliseconds to date after 1970 (eg 1 year will be 1971-1970)
// }


// function createName(firstName,lastName,eMail) {
//     if (eMail){
//         return `<a href='mailto:${eMail}'>${firstName} ${lastName}</a>`
//     }
//     return `${firstName} ${lastName}`;
// }


// function removeOldMessageAndAddNew(firstName,lastName, birthDate, eMail, msg){

//     // let oldMessagesNodesArray = ;
//     let containerNodes = document.getElementsByClassName("container_for_one_old_message");
//     // console.log(containerNodes);
//     var lastMessageContainerNode = containerNodes[containerNodes.length-1];
//     // console.log(lastMessageContainerNode);
//     //  let firstMessageContainerNode = oldMessagesNodesArray[0]; 

//     //prepare a place for a new message and create a container for that message:
//     let parentNode =  document.getElementById("container_for_old_messages");
//     let referenceNode = document.getElementsByClassName("container_for_one_old_message")[0];
//     let newMessageNode = lastMessageContainerNode.cloneNode(true);// does not matter what message do we copy, we will change its values later.
//     // let insertedMessageNode = parentNode.insertBefore(newMessageNode, referenceNode);
//     parentNode.insertBefore(newMessageNode, referenceNode);


//     //then add fresh input values to that newly created message container (which is now a clone of the last message)
//     document.getElementsByClassName("old_name")[0].innerHTML = createName(firstName,lastName,eMail);
//     document.getElementsByClassName("old_age")[0].innerHTML = `${calculateAge(birthDate)} years.`;
//     document.getElementsByClassName("old_message")[0].innerHTML = msg;

    

//     // then hide the last message (hide does not work because user can post several messages, so each time the last has to be deleted)
//     lastMessageContainerNode.remove();
// }





// // 2) actions that are needed AFTER the button is pressed//

// function Actions(){   

  
//     // first, small circle: form fields validation, same as in PHP. 
//     // I could use AJAX to use same php script, but I think better to do the same validation in browser.

//     let firstName = document.getElementsByName("first_name")[0].value; 
//     let lastName = document.getElementsByName("last_name")[0].value;
//     let birthDate = document.getElementsByName("birth")[0].value;
//     let eMail = document.getElementsByName("email")[0].value;  // here in JS I can not use same variable name as had in PHP, to create an object let messageObject.  
//     let msg = document.getElementsByName("message")[0].value; // email and message were identical. Objects keys have to be 
        
//     if (serverSuccessMessageNode) {  // if that text exist, we have to delete it while spinner spins
//         serverSuccessMessageNode.innerHTML = "";
//     }
//     if (serverErrorMessageNode) {
//         serverSuccessMessageNode.innerHTML = ""; // if the last message has been sent and user submits another message.
//         serverErrorMessageNode.innerHTML = "";
//     }
    

//     // validate the input and save results to error variables:

//     function validateInputAndSaveResults(){
//         inputsOk = [];
//         inputsWithErrors = []; //have to null them, to start ower after resubmitting data

//         if (!/^[a-zA-Z-' ]*$/.test(firstName)) {
//             firstNameErrNode.innerText = "First name shall contain only letters and whitespaces.";
//             inputsWithErrors.push("first_name_err");
//             console.log(inputsWithErrors);
//         }else{
//             inputsOk.push("first_name");
//             console.log(inputsOk);
//         }

//         if (!/^[a-zA-Z-' ]*$/.test(lastName)) {
//             lastNameErrNode.innerText = "Last name shall contain only letters and whitespaces.";
//             inputsWithErrors.push("last_name_err");
//             console.log(inputsWithErrors);
//         }else{
//             inputsOk.push("last_name");
//             console.log(inputsOk);
//         }

        
//         if (Date.parse(birthDate) > today) {
//             birthDateErrNode.innerText = "Your birth date can not be in the future!";
//             inputsWithErrors.push("birth_err");
//             console.log(inputsWithErrors);
//         }else{
//             inputsOk.push("birth"); 
//             console.log(inputsOk);
//         }

//         if ( eMail!="" &&  !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(eMail)) {
//             emailErrNode.innerText = "Seems there is a typing error, such email is not valid.";
//             inputsWithErrors.push("email_err");
//             console.log(inputsWithErrors);
//         }else{
//             inputsOk.push("email"); 
//             console.log(inputsOk);
//         }

//         if (msg.length<3) {
//             msgErrNode.innerText = "Message shall contain at least 3 characters";        
//             inputsWithErrors.push("message_err");
//             console.log(inputsWithErrors);  
//         } else if(msg.length>500){
//             msgErrNode.innerText = "Message shall contain less than 500 characters"  ;
//             inputsWithErrors.push("message_err");
//             console.log(inputsWithErrors);        
//         } else {
//             inputsOk.push("message"); 
//             console.log(inputsOk);
//         }

//     }
//     validateInputAndSaveResults()





//     // then, check for remaining errors and if there are- report errors and stay on small cycle. If no errors, launch a bigger circle: save info to DB
//     function applyErrorsCssOrSave(){ 
//         if (inputsWithErrors.length>0) {  //if there are some fresh errors on input
//             for (let i = 0; i < inputsWithErrors.length; i++) {  //mark all errors red
//                 const classNameForErrorMessage = inputsWithErrors[i];
//                 addCssRed(classNameForErrorMessage)
                
//             }

//             for (let i = 0; i < inputsOk.length; i++) { // mark all good inputs green and "accepted" message instead of error message.
//                 const ClassNameOfInput = inputsOk[i];
//                 addCssGreen(ClassNameOfInput);
//                 removeColorAndChangeToAccepted(ClassNameOfInput+"_err")
//                 // let correspondingErrorMessageNode = document.getElementsByClassName(ClassNameOfInput+"_err")[0];
//                 //console.log(correspondingErrorMessageNode.classList);
//                 // correspondingErrorMessageNode.textContent = "Accepted"

//                 //if value was incorrect and a now became correct, we have to delete error message:
//                 if (document.getElementsByClassName(ClassNameOfInput+"_err")[0].classList.contains("red")) {   
//                     removeColorAndChangeToAccepted(ClassNameOfInput+"_err");
//                     // node.textContent = "Accepted";
//                 }
//             }


//         } else { // if there are no errors or all the errors are fixed
            
//             // first correct all remaining error messages:
//             for (let i = 0; i < inputsOk.length; i++) {
//                 const ClassNameOfInput = inputsOk[i];
//                 addCssGreen(ClassNameOfInput);
//                 removeColorAndChangeToAccepted(ClassNameOfInput+"_err") //  delete all error messages that may be left hanging
//             }

//             // then hide button and launch spinner: // better to add class, as it can be removed

//             hideOrShow("button", "lds-facebook");
//             hideOrShow("mandatory_notice", null);
            


            
//             function saveToDB(firstName,lastName, birthDate, eMail, msg) {
//                 const xhttp = new XMLHttpRequest();
//                 // xhttp.onload = function() {
//                 //     // here I will parse the data
//                 //   //document.getElementById("txtHint").innerHTML = this.responseText;
//                 // }
//                 xhttp.open("POST", "/run_button_press.php", true);
//                 xhttp.setRequestHeader("Content-Type", "application/json, charset=utf-8");
//                 xhttp.onload = function() {
//                     if (this.readyState == 4 && this.status == 200) {
                        
//                         try {// if there are errors, they will be saved in JSON message too
//                             console.log("Here is unparsed JSON: " + this.responseText)
//                             let response = JSON.parse(this.responseText);  
                            
//                             if (response["success_or_not"]) {// this is the case when server responds "1"- which means "am fine, your message saved"
//                                 serverSuccessMessageNode.innerHTML = response["text_message"]
//                                 removeOldMessageAndAddNew(firstName,lastName, birthDate, eMail, msg)
//                             removeInputValues();
//                             console.log(response);
//                             }

//                         } catch (error) {
//                             serverErrorMessageNode.innerHTML = `Something went wrong :( Message may be not saved. JSON error: ${error}`;
//                             console.log(response);                            
//                         }


//                         hideOrShow("lds-facebook","button");
//                         restoreInputErrorMessageText();
//                         hideOrShow(null, "mandatory_notice");
//                         for (let i = 0; i < inputsOk.length; i++) {
//                             let classNameOfInput = inputsOk[i];
//                             removeCssGreen(classNameOfInput);                            
//                         }
                        
                        
//                         // document.getElementsByClassName("serverErrorMessageNode")[0].innerHTML = response;
                    
//                     } else {
//                         console.log('Error Code: '+ xhttp.status);
//                         console.log('Error Message: '+ xhttp.statusText);
//                     }
//                 };
//                 let messageObject = {first_name:firstName,last_name:lastName,birth:birthDate,email:eMail,message:msg}; // must be JS object
//                 // console.log(messageObject);
//                 // console.log("message to be sent is: " + JSON.stringify(messageObject));
//                 xhttp.send(JSON.stringify(messageObject));
//             }

//             saveToDB(firstName,lastName, birthDate, eMail, msg)

//         } 
    


//     }
//     applyErrorsCssOrSave()
    
    
    
// };

// // Actions()

