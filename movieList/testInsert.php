<?php
include("dbinfo.inc.php");
// Check if request sent using AJAX
if(is_ajax()){
  if (isset($_POST["movieTitle"])) {
    $movieTitle = test_input($_POST["movieTitle"]);
    echo $movieTitle;
  }
}

?>
