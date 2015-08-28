<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Movie!</title>
  <link rel="stylesheet" 
        href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>
  <link rel = "stylesheet" type="text/css" href="style.css"/>
  <link href="http://fonts.googleapis.com/css?family=Lobster" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Cabin" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>

<body>
  <div class="container-fluid">
    <div class="page-header">
      <h1 class="text-center">Random Movie Selector</h1>
    </div>
  
    <div class="forms" id="insertMovie">
      <h2>Insert a movie you want to see in the future!</h2>
      <!--Form to insert data into MySQL database using PHP -->
      <form id='insertTitleForm' action="insertTitle.php" method="post">
        <label for="Title">Title</label>
        <input type="text" name="movieTitle" required autofocus maxlength="255"/>
        <input class="btn btn-primary" type="submit" />
      </form>
    </div>
  
    <div class="forms" id="getMovie">
      <h2>Retrieve a random Movie!</h2>
      <!--form to retrieve a random movie from MySQL database -->
      <form id='randomMovieForm' action="randomMovie.php" method="post">
        <input class="btn btn-primary" type="submit" name="randomMovie" value="Random Movie!"/>
      </form>
    </div>

    <div class="results" id="resultMovieTitle">
      <p class="texts" id="randomMovieTitle"></p><br>
    </div>

    <div class='alert alert-success fade in'>
    </div>


  </div>
</body>
</html>
