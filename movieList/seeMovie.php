<?php 
include("dbinfo.inc.php");

// Connect to database
$conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);
// Check connection
if ($conn->connect_error){
  echo "ERROR: Connection failed: {$conn->connect_error}";
}

$movieTitle = ""; 
// Check whether the form has been submitted
if(isset($_POST["resultMovie"])){
  // Access the input from the form "resultForm"
  $movieTitle = test_input($_POST["resultMovie"]);
}

// SQL command
$sql = "UPDATE Movies1 SET Seen=1 WHERE movietitle='{$movieTitle}'";

if ($conn->query($sql) === TRUE){
  echo "{$movieTitle} has been marked as seen!";
}
  else{
  echo "ERROR: {$conn->error}";
}

$conn->close();
?>
