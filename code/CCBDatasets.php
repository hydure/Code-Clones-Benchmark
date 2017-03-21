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
                  if ($_SESSION['userSession'] == $userId) {
                    echo "<tr>";
                    echo '<th>'.$projectID.'</th>';
                    echo '<th>'.$title.'</th>';
                    echo '<th>'.$last_accessed.'</th>';
                    echo '<th>'.$uploaded.'</th>';
                    if (intval($ownership) == -1) { 
                      echo '<th>Private</th>';
                    } else {
                      echo '<th>Public</th>';
                    }
                    echo "<td><input type='checkbox' name='row[]' value='" . $row['projectID'] . "'>". $row['projectID'] . "</td>";
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

            $tempDID = -1;
            $curr_flag = false; 

            while ($row = $result->fetch_assoc()) {

              unset($datasetID, $projectID, $userId, $submit_date, $running_flag);
              $datasetID = $row['datasetID'];
              $userId = $row['userId'];

              if ($tempDID == $datasetID && $_SESSION['userSession'] == $userId) { //scenario 3: we are appending projects array
                array_push($row_data, $copied_PID);
              } else {
                  if ($curr_flag) { //scenarion 2: we encounter a new dataset of anyone, must print output
                    array_push($row_data, $copied_PID);
                    $project_string = implode(', ', $row_data);
                    echo "<tr>";
                    echo '<th>'.$tempDID.'</th>';
                    echo '<th>'.$project_string.'</th>';
                    echo '<th>'.$copied_Submit_Date.'</th>';
                    if (intval($copied_Running_Flag) == 0) { 
                      echo '<th>Inactive</th>';
                    } else {
                      echo '<th>Active</th>';
                    }
                    echo "</tr>";


                    $curr_flag = false;
                  } //scenario 1: we encounter a new dataset of the user, but nothings initialized
                  if ($_SESSION['userSession'] == $userId) {
                    $projectID = $row['projectID'];
                    $row_data = array();
                    $curr_flag = true;
                  }
              }
              $tempDID = $datasetID;
              $projectID = $row['projectID'];
              $copied_PID = $projectID;
              $submit_date= $row['submit_date'];
              $copied_Submit_Date = $submit_date;
              $running_flag = $row['running_flag'];
              $copied_Running_Flag = $running_flag;
              


            }
            if ($curr_flag) { //print last dataset if it was currently being appended
              array_push($row_data, $copied_PID);
              $project_string = implode(', ', $row_data);
              echo "<tr>";
              echo '<th>'.$datasetID.'</th>';
              echo '<th>'.$project_string.'</th>';
              echo '<th>'.$copied_Submit_Date.'</th>';
              if (intval($copied_Running_Flag) == 0) { 
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
            <p align='center-block' style='font-size: 160%''>Delete Dataset</p>
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