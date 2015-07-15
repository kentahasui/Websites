<?php
include("dbinfo.inc.php");
// Script to insert new movie title into database
$conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);

// Check connection
if ($conn->connect_error){
  die("Connection failed: " . $conn->connect_error . "<br>");
}

// prepare and bind
$stmt = $conn->prepare("INSERT INTO Movies1 (movietitle) VALUES(?)");
$stmt->bind_param("s", $movietitle);

// set parameters and execute
$movietitle = "big fish";
$stmt->execute();

echo "New records created successfully <br>";
$stmt->close();
$conn->close();

?>
