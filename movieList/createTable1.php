<?php
include("dbinfo.inc.php");
// Make a MYSQL connection
$connection = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);

// Check if server connected successfully
if($connection->connect_errror){
  die("Connection failed: " . $conn->connect_error);
}

// success message
echo "Connected successfully <br>";

// Use SQL to create table
$sql = "CREATE TABLE Movies1 (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
movietitle VARCHAR(255) NOT NULL, 
seen TINYINT(1) NOT NULL DEFAULT 0
)";

if($connection->query($sql) === TRUE){
  echo "Table Movies1 created successfully";
} else {
  echo "Error creating table: " . $connection->error;
}

// close connection
mysqli_close($connection);
?>
