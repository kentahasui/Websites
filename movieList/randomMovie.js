/* randomMovie.js: This file defines javascript functions to display a random movie on a page.
   It references the script randomMovie.php. These functions call the getRequest() method,
   defined in the file ajaxRequest.js */

function output(responseText){
  document.getElementById('randomMovieTitle').innerHTML = responseText;
}

function randomMovie(){
  getRequest('hello.php', output);
  return false;
}
