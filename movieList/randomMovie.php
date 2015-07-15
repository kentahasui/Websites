<?php
include("dbinfo.inc.php");

// Connect to database
$conn = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);
// Check connection
if ($conn->connect_error){
  die("ERROR: Connection failed: " . $conn->connect_error . "<br>");
}

// SQL query
$sql = "SELECT movietitle FROM
  (SELECT MovieTitle FROM Movies1 WHERE seen = 0) 
  AS seent ORDER BY RAND() LIMIT 1";
// Run Query
if($result = $conn->query($sql)){
  if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $title = $row['movietitle'];
    echo $title;
  }
  else {
    echo "ERROR: 0 results";
  }
}
else{
  echo "ERROR: {$conn->error}";
}
$conn->close();

?>
