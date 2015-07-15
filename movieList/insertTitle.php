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
      die("ERROR: Connection failed: " . $conn->connect_error);
    }

    // Sql command
    $sql = "INSERT INTO Movies1 (movietitle) VALUES ('{$movieTitle}')";

    // Use command, check if it went through
    if ($conn->query($sql) === TRUE){
      echo "{$movieTitle} has been added to the database!";
    } else{
      $error = "ERROR: SQL Command Failed: {$conn->error}";
      echo $error;
    }

    // Close connection
    $conn->close();
  }
}

?>
