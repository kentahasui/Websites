<?php
include("dbinfo.inc.php");
// Check if request sent using AJAX
if(is_ajax()){
  if (isset($_POST["movieTitle"])){
    $movieTitle = test_input($_POST["movieTitle"]);
    
    // Connect to database
    $conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);
    // Check connection
    if ($conn->connect_error){
      sendErrorMessage($conn->connect_error);
      exit;
    }

    // Sql command
    $sql = "INSERT INTO Movies1 (movietitle) VALUES ('{$movieTitle}')";

    // Use command, check if it went through
    if ($conn->query($sql) === TRUE){
      sendSuccessMessage("{$movieTitle} has been added to the database!");
    } else{
      $error = "ERROR: SQL Command Failed: {$conn->error}";
      sendErrorMessage($error);
    }

    // Close connection
    $conn->close();
  }
}

?>
