<?php
session_start();
require_once 'class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
 $user_home->redirect('index.php');
}

$stmt = $user_home->runQuery("SELECT * FROM Accounts WHERE userID=:uid");
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
  <script>
    function submitFunc() {

      var number_of_checked_checkbox= $("input[name=ownership_type]:checked").length;
      if(number_of_checked_checkbox != 1){
          alert("You must select a single ownership type.");
      } else {
          document.getElementById("project_button").submit();
      }
    }
  </script>
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
           <input type="button" onclick="location.href='logout.php';" value="Logout" 
            class="btn btn-primary center-block" />
    	</div>
       </div>
    </div>
      
    <div class="container">
      <div class="row row-offcanvas row-offcanvas-left">
        
        <!-- sidebar -->
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome1.1.php">Home</a></li>
              <li class="active"><a href="#">Tools</a></li>
              <li><a href="CCBDatabase.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="#">About</a></li> 
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
  	
        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Tools</h1>
          <form action="detector.php" method="post">
            <p>Please select your code cloner(s):
              <br />
              <label><input type="checkbox" name="detector[]" value="Nicad">Nicad</label><br />
              <label><input type="checkbox" name="detector[]" value="CCFinderX">CCFinderX</label><br />
              <label><input type="checkbox" name="detector[]" value="Deckard">Deckard</label><br />
	      <input type="submit" value="Test">
            </p>
          </form>
          <br />
          <br />
          <form action="#">
            <p align="center-block">Choose a Dataset</p>
            <select name = "dataset">
              <!--Create a code that lists all the datasets available, but for now an example -->
              <option value = "dataset1">Dataset 1</option>
            </select>
          </form>
          <form id="project_button" action="upload.php" method="post" enctype="multipart/form-data">
            <p align="center-block">Submit Compressed Source Directory</p>
            Private:
            <input type="checkbox" name="ownership_type" value="1" checked>
            Public:
            <input type="checkbox" name="ownership_type" value="2">
             <br>
            <input type = "file" name = "uploaded_file" /><br />
            <input type = "button" value = "Upload"  id="submit_project" onclick="submitFunc()" />
                   



          </form>
         	
         	<div class="col-md-4 text-center"> 
    			<button id="singlebutton" name="singlebutton" class="btn btn-primary center-block">Run</button> 
			</div>
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
