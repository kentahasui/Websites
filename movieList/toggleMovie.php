<?php
include("dbinfo.inc.php");

// Connect to database
$conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);
// Check connection
if ($conn->connect_error){
  sendErrorMessage("Connection failed: {$conn->connect_error}");
}

$movieTitle = ""; 

if(isset($_POST["ToggleMovieTitle"])){
  // Access the button data
  $movieTitle = test_input($_POST["ToggleMovieTitle"]);
  
  // Prepare PHP statement
  $stmt = $conn->prepare("UPDATE Movies1 SET seen = 1 - seen WHERE movieTitle = ?");
  $stmt->bind_param("s", $movieTitle);
  
  // Execute statement
  if($stmt->execute()){
    sendSuccessMessage("{$movieTitle} value toggled!");
  }
  else{
    sendErrorMessage("ERROR: SQL Command Failed: {$conn->error}");
  }
  $stmt->close();
}

$conn->close();
?>