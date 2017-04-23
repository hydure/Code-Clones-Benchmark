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

<div 

<!DOCTYPE html>
<html lang="en">
<!-- still need to create sidebar, etc. -->
<head>
	<title>Code Clones Benchmark</title>
	<link href="CCB1.1.css" type = "text/css" rel="stylesheet">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>



  
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
           <div style="position: absolute; top: 15; right: 170;">
            <?php echo "Hello, " . ($_SESSION['userName']); ?>
            </div>
    	</div>
       </div>
    </div>
      
    <div class="container">
      <div class="row row-offcanvas row-offcanvas-left">
        
        <!-- sidebar -->
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li class="active"><a href="#">Tools</a></li>
              <li><a href="CCBReport.php">Reports</a></li>
              <li><a href="CCBEvaluate.php">Evaluate</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
  	
        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Tools</h1>
          <form action="run_detectors.php" method="post">
            <p align="center-block">Choose a Dataset:</p>

            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT datasetID, userId FROM Datasets where status != -1");

            echo "<html>";
            echo "<body>";
            echo "<select name='datasetSelect' id = 'datasetSelect' >" ;
            $dataset_dropdown = array();
            while ($row = $result->fetch_assoc()) {

                  unset($datasetID, $userId);
                  $datasetID = $row['datasetID'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId && !in_array($datasetID, $dataset_dropdown)) {
                    echo '<option value='.$datasetID.'>'.$datasetID.'</option>';
                    array_push($dataset_dropdown, $datasetID);
                  }
            }
            echo "</select>";
            echo "</body>";
            echo "</html>";
            $con->close();
            ?>
            
            <p>Please select your code clone detector(s):
              <br />
              <label><input type="checkbox" name="detector[]" value="nicad">Nicad</label><br />
        &emsp;&emsp;<label><input type="radio" name="n_language[]" value="py">Python (.py)</label><br>
        &emsp;&emsp;<label><input type="radio" name="n_language[]" value="java">Java (.java)</label><br>
        &emsp;&emsp;<label><input type="radio" name="n_language[]" value="c">C (.c)</label><br>
        &emsp;&emsp;<label><input type="radio" name="n_language[]" value="cs">C# (.cs)</label><br>
              <label><input type="checkbox" name="detector[]" value="deckard">Deckard</label><br />
        &emsp;&emsp;<label><input type="radio" name="d_language[]" value="java">Java (.java)</label><br>
        &emsp;&emsp;<label><input type="radio" name="d_language[]" value="php">PHP (.php)</label><br>
        &emsp;&emsp;<label><input type="radio" name="d_language[]" value="c">C (.c)</label><br>
              <label><input type="checkbox" name="detector[]" value="ccfinderx">CCFinderX</label><br />
              <div class="col-md-4 text-center"> 
    			<button id="singlebutton" name="singlebutton" class="btn btn-primary center-block" type="submit">Run</button> 
			  </div>
	      <!-- <input type="submit" value="Test"> Commenting this out and moved button up-->
            </p>
          </form>
          <br />
          <br />
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
