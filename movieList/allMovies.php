<?php
include("dbinfo.inc.php");

// Connect to database
$conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);
// Check connection
if ($conn->connect_error){
  die("ERROR: Connection failed: " . $conn->connect_error);
}

// SQL query string
$sql = "SELECT movietitle, seen FROM Movies1 ORDER BY movietitle"; 
// Array variable to store results
$rows;

// Run query
$result = $conn->query($sql);
// If there was an error, log it
if($result === false){
  echo "ERROR: {$conn->error}";
}
// Otherwise return a JSON object containing all rows
else{
  // Place all results in an array
  while($row = $result->fetch_assoc()){
    if($row["seen"]==1){
      $row["seen"] = "Yes";
    }
    else{
      $row["seen"] = "No";
    }
    $rows[] = $row;
  }
  // Free result set
  $result->free();
  // Return a JSON representation of the results
  echo json_encode($rows);
}

// close connection
$conn->close();
?>