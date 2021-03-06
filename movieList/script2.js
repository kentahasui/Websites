/* 
NAME: Script.js
PURPOSE: Defines JQUERY AJAX functions to process form inputs and send form data
AUTHOR: Kenta Hasui
*/

// ---------------------------------------------------
// Create container to display a selected random movie
// ---------------------------------------------------
$(function(){

var resultData = ""; 

// Extract contents of the handlebars script
var theTemplateScript = $("#results-template").html();

console.log(theTemplateScript);

// Compile the template into a javascript function
var theTemplate = Handlebars.compile(theTemplateScript);

// Call the compiled function, passing in resultData
// Append the output html to the resultMovieTitle Div
//$("#resultMovieTitle").html(theTemplate(resultData));


var $form = $("<form id='resultForm'></form>");
// ---------------------------------------------------
// Functions to display success or error messages
// ---------------------------------------------------
function successMessage(message){
  // Remove any previous messages
  $(".alert").remove();
  // Create a div for the alert message
  $("#alerts").append('<div class="alert alert-success fade in"></div>');
  // Change the text
  $(".alert").text(message);
  // Allow user to x out of alert
  $(".alert").append("<a class='close' data-dismiss='alert' aria-label='close'>&times</a>");
}

function errorMessage(message){
  $(".alert").remove();
  // Create a div for the alert message
  $("#alerts").append('<div class="alert alert-danger fade in"></div>');
  // Change the text
  $(".alert").text(message);
  // Allow user to x out of alert
  $(".alert").append("<a class='close' data-dismiss='alert' aria-label='close'>&times</a>");  
}

// ------------------------------------------------------
// Variable to hold request
// All JQuery variables are prefixed with $
var request;
// ------------------------------------------------------

// ----------------------------------------------------
// Function to submit the insert title form
// Places a new movie in the database
// ----------------------------------------------------
$("#insertTitleForm").submit(function(event){
  // abort any pending request
  if(request) {
    request.abort();
  }
  
  // Set up local variables: this represents the whole form
  var $form = $(this);
  // Select and cache all fields
  var $inputs = $form.find("input");
  // Serialize the data 
  var serializedData = $form.serialize();
  // Disable the inputs for duration of Ajax request
  $inputs.prop("disabled", true);

  // Ajax request
  request = $.ajax({
    //url: "/insertMovie.php",
    url: "/insertTitle.php",
    type: "post",
    data: serializedData
  });

  // Callback handler. Called on success!
  request.done(function (response, textStatus, jqXHR){
    if(response.includes("ERROR:")){
      errorMessage(response);
    }
    else{
      successMessage(response);
    }
  });

  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    alert("The following error occurred: "+ textStatus, errorThrown);
  });

  // Callback handler. Called regardless of success or failure
  request.always(function(){
    // Re-enable inputs
    $inputs.prop("disabled", false);
  });

  event.preventDefault();
});

// --------------------------------------------------
// Function to submit the random movie form.
// Creates a new form for the random movie, and adds a new submit button
// --------------------------------------------------
$("#randomMovieForm").submit(function(event){
  request = $.ajax({
    url:"/randomMovie.php",
    type:"post"
  });

  request.done(function(response){
    resultData = {name:response}; 
    // If the resultForm doesn't exist, render the html
    if(!$("#resultForm").length){
      var resultHTML = theTemplate(resultData);
      $("#resultMovieTitle").html(resultHTML);
      $("#resultForm").submit = $form.submit;
    }
    // Otherwise just update the input
    else{
      $("#resultMovie").val(response);
    }
  });

  request.fail(function(error){
    console.log(error);
  });

  event.preventDefault();
});

// ------------------------------------------------
// Function to submit the result movie form. 
// This marks a movie as seen in the mySQL database
// ------------------------------------------------
$form.submit(function(event){
  var $form = $(this);
  // Select and cache all fields
  var $inputs = $form.find("input");
  // Serialize the data 
  var serializedData = $form.serialize();
  
  // Send the data
  request = $.ajax({
    url: "/seeMovie.php",
    type: "post",
    data: serializedData
  });

  request.done(function(response){
    if(response.includes("ERROR:")){
      errorMessage(response);
    }
    else{
      successMessage(response);
    }
  });

  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    alert("The following error occurred: "+ textStatus, errorThrown);
  }); 

  // Prevent the page from refreshing
  event.preventDefault();
});

});
