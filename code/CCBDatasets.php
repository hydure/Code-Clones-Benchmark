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

<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    font size ="3";
}

tr:nth-child(even) {
    background-color: #dddddd;
}


</style>

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
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li class="active"><a href="#">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Datasets</h1>
          <br />
          <form action="#">
            <p style="font-size: 160%">Upload Dataset</p>
         	  <input type = "text" placeholder="URL for Code" required=""> 
            <input type = "submit" name = "upload" value = "Upload" />
          </form>
          <br />
          <form action="add_dataset.php", method="post">
            <p align="center-block" style="font-size: 160%">Dataset Stitching</p>

            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT projectID, title, last_accessed, uploaded, ownership, userId FROM Projects");
            echo "<html>";
            echo "<body>";
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Project</th>";
            echo "<th>Last Accessed</th>";
            echo "<th>Date Uploaded</th>";
            echo "<th>Ownership</th>";
            echo "<th>Add</th>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {

                  unset($projectID, $title, $last_accessed, $uploaded, $ownership, $userId);
                  $projectID = $row['projectID'];
                  $title = $row['title'];
                  $last_accessed = $row['last_accessed'];
                  $uploaded = $row['uploaded'];
                  $ownership = $row['ownership'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId || $ownership == -1)  {
                    echo "<tr>";
                    echo '<th>'.$projectID.'</th>';
                    echo '<th>'.$title.'</th>';
                    echo '<th>'.$last_accessed.'</th>';
                    echo '<th>'.$uploaded.'</th>';
                    if (intval($ownership) != -1) { 
                      echo '<th>Private</th>';
                    } else {
                      echo '<th>Public</th>';
                    }
                    echo "<td><input type='checkbox' name='row[]' value='" . $row['projectID'] . "'></td>";
                    echo "</tr>";
                  }
            }
            echo "</table";
            echo "</body>";
            echo "</html>";
            $con->close();
            ?>
            <input type="submit" value="Initialize Dataset">
            <br />

          <form action="#">
            

            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT datasetID, projectID, userId, submit_date, running_flag FROM Datasets");
            //echo "<p align='center-block' style='font-size: 160%''>Dataset Browser</p>";
            echo "<html>";
            echo "<body>";
            echo "<table>";
            echo "<tr>";
            echo "<th>Dataset ID</th>";
            echo "<th>Project ID(s)</th>";
            echo "<th>Submit Date</th>";
            echo "<th>Status</th>";
            echo "</tr>";

            $ID_array = array();
            while ($row = $result->fetch_assoc()) {

              unset($datasetID, $userId);
              $datasetID = $row['datasetID'];
              $userId = $row['userId'];
              if ($_SESSION['userSession'] == $userId && !in_array($datasetID, $ID_array)) {
                array_push($ID_array, $datasetID);
              }
            }

            foreach ($ID_array as $dID) {
              $sql="SELECT * FROM Datasets where datasetID = ". $dID;
              $result = mysqli_query($con, $sql);
              $tempArray = array();
              while ($row = $result->fetch_assoc()) {
                unset($projectID, $submit_date, $running_flag);
                array_push($tempArray, $row['projectID']);
                $submit_date = $row['submit_date'];
                $running_flag = $row['running_flag'];
              }
              $project_string = implode(', ', $tempArray);         
              echo "<tr>";
              echo '<th>'.$dID.'</th>';
              echo '<th>'.$project_string.'</th>';
              echo '<th>'.$submit_date.'</th>';
              if (intval($running_flag) == 0) { 
                echo '<th>Inactive</th>';
              } else {
                echo '<th>Active</th>';
              }
              echo "</tr>";              
            }

            echo "</table"; 
            echo "</body>";
            echo "</html>";
            $con->close();
            ?>

          </form>
          <br />

          <form id='deleteDataset' action='delete_dataset.php' method='post' enctype='multipart/form-data'>
            <p align='center-block' style='font-size: 160%''>Manage Datasets</p>
            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT datasetID, userId FROM Datasets");

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
            <input type = 'submit' name= 'delete_dataset_action' value = 'Delete Dataset'  id='delete_dataset_action' />
          </form>

        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>