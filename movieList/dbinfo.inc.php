<?
$mysql_username="a1637880_khasui";
$mysql_password="kentakenta123";
$mysql_database="a1637880_movies";
$mysql_server="mysql3.000webhost.com";

$mysql_tablename="Movies1";

// Function to strip unnecessary characters, remove backslashes
function test_input($data){
  $data = trim($data);
  $data = stripslashes($data);
  $data = strtolower($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Function to check if the request is an AJAX request
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function errorMessage($error){
  echo "<br>"; 
  echo "<div class='alert alert-danger fade in'>";
  echo "<a class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
  echo "Error: " . $error;;
  echo "</div>";
}

function successMessage($successString){
  echo "<br>";
  echo "<div class='alert alert-success fade in'>";
  echo "<a class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
  echo $successString;
  echo "</div>";
}
?>
