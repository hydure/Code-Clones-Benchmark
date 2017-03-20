<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
 $user_home->redirect('index.php');
}

$stmt = $user_home->runQuery("SELECT * FROM Accounts WHERE userId=:uid");
$stmt->execute(array(":uid"=>$_SESSION['userSession']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
});
</script>


<!DOCTYPE html>
<html lang="en">
<!-- still need to create sidebar, etc. -->
<head>
	<title>Code Clones Benchmark</title>
	<link href="CCB1.1.css" type = "text/css" rel="stylesheet">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<div class="page-container">
  
	<!-- top navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
       <div class="container">
    	<div class="navbar-header">
           <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".sidebar-nav">
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
           <a class="navbar-brand" href="#">Code Clones Benchmark</a>
           <div style="position: absolute; top: 8; right: 70; width: 80px; height: 30px;">
              <input type="button" onclick="location.href='logout.php';" value="Logout" 
            class="btn btn-primary center-block" />
           </div>
    	</div>
       </div>
    </div>
      
    <div class="container">
      <div class="row row-offcanvas row-offcanvas-left">
        
        <!-- sidebar -->
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome1.1.php">Home</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li class="active"><a href="#">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="#">About</a></li> 
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
  	
        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Projects</h1>
          <br />
          <form action="add_project.php" method="post">
            URL:<br>
            <input type="text" name="url">
            <br>
	          Commit:<br>
            <input type="text" name="commit" value="head">
            <br>
	          Private:<br>
	          <input type="checkbox" name="private" value="1">
	          <br>        
	          <input type="submit" value="Upload"/>
          </form>
          <br />
          <form action="#">
            <p align="center-block" style="font-size: 160%">Delete Project</p>
            <select name = "project">
              <!--Create a code that lists all the projects available, but for now an example -->
              <option value = "project1" style="font-size: 160%">Project 1</option>
            </select>
          </form>
          <br />
          <form action="#">
            <p align="center-block" style="font-size: 160%">Browse Projects</p>
            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT projectID, title, userId FROM Projects");
            echo "<html>";
            echo "<body>";
            echo "<select name='projectSelect'>";

            while ($row = $result->fetch_assoc()) {

                  unset($projectID, $title, $userId);
                  $projectID = $row['projectID'];
                  $title = $row['title'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId) {
                    echo '<option value="'.$projectID.'">'.$title.'</option>';
                  }
                 
            }
            echo "</select>";
            echo "</body>";
            echo "</html>";
            $con->close();
            ?>
            <!--<select name = "project">
              Create a code that lists all the projects available, but for now an example
              <option value = "project1" style="font-size: 160%">Project 1</option>
            </select> -->
          </form>
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>