/* 
NAME: Script.js
PURPOSE: Defines JQUERY AJAX functions to process form inputs and send form data
AUTHOR: Kenta Hasui
*/

// Wait till document is ready
$(function(){

// ---------------------------------------------------
// Compile Handlebars templates
// ---------------------------------------------------

// Template to display random movie
var theTemplateScript = $("#results-template").html();
var theTemplate = Handlebars.compile(theTemplateScript);

// Template to display all movies
var allMoviesTemplateScript = $("#allMoviesTemplate").html();
var allMoviesTemplate = Handlebars.compile(allMoviesTemplateScript);

// Template to display alert messages
var alertTemplateScript = $("#alertsTemplate").html();
var alertTemplate = Handlebars.compile(alertTemplateScript);

// ---------------------------------------------------
// Functions to display success or error messages
// ---------------------------------------------------
function successMessage(message){
  $(".alert").remove();
  var alertData = {string:message,
                   type: "alert-success"};
  var alertHTML = alertTemplate(alertData);
  $("#alerts").html(alertHTML);
}

function errorMessage(message){
  $(".alert").remove();
  var alertData = {string: message,
                   type: "alert-danger"};
  var alertHTML = alertTemplate(alertData);
  $("#alerts").html(alertHTML);
}

// ------------------------------------------------------
// Variable to hold request
// All JQuery variables are prefixed with $
// ------------------------------------------------------
var request;

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
    data: serializedData, 
    dataType: "json"
  });

  // Callback handler. Called on success! Response is a JSON object
  request.done(function (responseJSON, textStatus, jqXHR){
    if(responseJSON["Error"]){
      errorMessage(responseJSON["Message"]);
    }
    else{
      successMessage(responseJSON["Message"]);
    }
  });

  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    console.log("The following error occurred: "+ textStatus, errorThrown);
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
    type:"post",
    dataType:"json"
  });

  request.done(function(responseJSON){
    // Extract message
    var response = responseJSON["Message"];
    // Success
    if(responseJSON["Error"]){
      errorMessage(response)
    }
    // Error
    else{
      // If the resultForm doesn't exist, render the html
      if(!$("#resultForm").length){
        var resultData = {name:response};
        var resultHTML = theTemplate(resultData);
        // Display HTML in browser page
        $("#resultMovieDiv").html(resultHTML);
        // Add the submit function
        $("#resultForm").submit(seeMovieSubmit);
      }
      // Otherwise just update the input
      else{
        $("#resultMovie").val(response);
      }
    }
    
  });

  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    console.log("The following error occurred: "+ textStatus, errorThrown);
  });

  event.preventDefault();
});

// ------------------------------------------------
// Function to submit the allMoviesForm. 
// This displays the results of a SQL Query as a result
// ------------------------------------------------
$("#allMoviesForm").submit(function(event){
  if(request) {
    request.abort();
  }
  // Set up local variables: this represents the whole form
  var $form = $(this);
  
  // Ajax request
  request = $.ajax({
    url: "/allMovies.php",
    type: "post",
    dataType: "json"
  });
  
  // Success event handler
  request.done(function(responseJSON){
    if(responseJSON["Error"]){
      errorMessage(responseJSON["Message"]);
    }
    else{
      var allMoviesObject = {allMovies : responseJSON};
      var allMoviesHTML = allMoviesTemplate(allMoviesObject);
      $("#allMoviesDiv").html(allMoviesHTML);
      $(".toggleButton").click(toggleButtonClick)
    }
  });
  
  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    console.log("The following error occurred: "+ textStatus, errorThrown);
  });
  
  // Prevent the page from refreshing
  event.preventDefault();
});

$("#hideAllMoviesButton").click(function(event){
  $("#allMoviesTable").remove();
});

// ------------------------------------------------
// Function to submit the result movie form. 
// This marks a movie as seen in the mySQL database
// ------------------------------------------------
function seeMovieSubmit(event){  
  var $form = $(this);
  // Select and cache all fields
  var $inputs = $form.find("input");
  // Serialize the data 
  var serializedData = $form.serialize();
  
  // Send the data
  request = $.ajax({
    url: "/seeMovie.php",
    type: "post",
    dataType:"json",
    data: serializedData
  });

  request.done(function(responseJSON){
    if(responseJSON["Error"]){
      errorMessage(responseJSON["Message"]);
    }
    else{
      successMessage(responseJSON["Message"]);
    }
  });

  // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    alert("The following error occurred: "+ textStatus, errorThrown);
  }); 

  // Prevent the page from refreshing
  event.preventDefault();
}

// ------------------------------------------------
// Function to toggle the "seen" values in the table
// This changes the seen entry in the mySQL database
// ------------------------------------------------
function toggleButtonClick(){
  // Disable button and extract movie title
  $button = $(this);
  $button.prop("disabled", true);
  $row = $button.parent().parent();
  $movieName = $row.children(".tableMovieTitle").text();
  $seen = $row.children(".tableMovieSeen");
  
  request = $.ajax({
    url: "/toggleMovie.php", 
    type: "post", 
    data: {"ToggleMovieTitle":$movieName},
    dataType: "json"
  });
  
  request.done(function(responseJSON){
    if(responseJSON["Error"]){
      errorMessage(responseJSON["Message"]);
    }
    else{
      $seen.text()=='Yes' ? $seen.text('No') : $seen.text('Yes');
    }
  });
  
   // Callback handler that will be called on failure
  request.fail(function (jqXHR, textStatus, errorThrown){
    // Log the error to the console
    alert("The following error occurred: "+ textStatus, errorThrown);
  }); 
  
  $button.prop("disabled", false);
}

}); // End document.ready()
