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
.wrapper {
  max-width: 1200px;
}

.table {
  margin: 8px 0 40px 0;
  width: 100%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  display: table;
}
@media screen and (max-width: 580px) {
  .table {
    display: block;
  }
}

.row_special {
  display: table-row;
  background: #f6f6f6;
}
.row_special:nth-of-type(odd) {
  background: #e9e9e9;
}
.row_special.header {
  font-weight: 900;
  color: #ffffff;
  background: #ea6153;
}
.row_special.green {
  background: #27ae60;
}
.row_special.blue {
  background: #2980b9;
}
@media screen and (max-width: 580px) {
  .row_special {
    padding: 8px 0;
    display: block;
  }
}

.cell {
  padding: 6px 12px;
  display: table-cell;
}
@media screen and (max-width: 580px) {
  .cell {
    padding: 2px 12px;
    display: block;
  }
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
           <div style="position: absolute; top: 15; right: 170;">
            <?php echo "Hello, " . ($_SESSION['userName']); ?>
           </div>
    	</div>
       </div>
    </div>
      
    <div class="container">
      <div class="row row-offcanvas row-offcanvas-left">
        
        <!-- sidebar -->
        <div class="col-xs-6 col-sm-2 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li class="active"><a href="#">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBReport.php">Reports</a></li>
              <li><a href="CCBEvaluate.php">Evaluate</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Datasets</h1>
          <br />

          <form action="add_dataset.php", method="post">
            <p align="center-block" style="font-size: 160%">Dataset Stitching</p>
            <input type="submit" value="Initialize Dataset">
            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT projectID, title, author, ".  
                "last_accessed, uploaded, ownership, userId FROM Projects ".
                "ORDER BY last_accessed DESC");
            echo "<div class = 'wrapper'>";
            echo "<div class='table'>";
            echo "<div class='row_special header green'>";
            echo "<div class='cell'>Add</div>";
            echo "<div class='cell'>ID</div>";
            echo "<div class='cell'>Project</div>";
            echo "<div class='cell'>Author</div>";
            echo "<div class='cell'>Last Accessed</div>";
            echo "<div class='cell'>Date Uploaded</div>";
            echo "<div class='cell'>Ownership</div>";
            echo "</div>";
            while ($row = $result->fetch_assoc()) {

                  unset($projectID, $title, $author, $last_accessed, $uploaded, $ownership, $userId);
                  $projectID = $row['projectID'];
                  $title = $row['title'];
                  $author = $row['author'];
                  $last_accessed = $row['last_accessed'];
                  $uploaded = $row['uploaded'];
                  $ownership = $row['ownership'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId || $ownership == -1)  {
                    echo "<div class='row_special'>";
                    echo "<div class='cell'><input type='checkbox' name='row[]' value='" . $row['projectID'] . "'></div>";
                    echo "<div class='cell'>".$projectID.'</div>';
                    echo "<div class='cell'>".$title.'</div>';
                    echo "<div class='cell'>".$author.'</div>';
                    echo "<div class='cell'>".$last_accessed.'</div>';
                    echo "<div class='cell'>".$uploaded.'</div>';
                    if (intval($ownership) != -1) { 
                      echo "<div class='cell'>Private</div>";
                    } else {
                      echo "<div class='cell'>Public</div>";
                    }
                    echo "</div>";
                  }
            }
            echo "</div>";
            echo "</div>";
            $con->close();
            ?>


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
            <input type = 'submit' name= 'delete_dataset_action' value = 'Delete Dataset'  id='delete_dataset_action'/>

            

            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT datasetID, projectID, userId, submit_date, status FROM Datasets ORDER BY submit_date DESC");
            //echo "<p align='center-block' style='font-size: 160%''>Dataset Browser</p>";
            echo "<div class = 'wrapper'>";
            echo "<div class='table'>";
            echo "<div class='row_special header green'>";
            echo "<div class='cell'>Dataset ID</div>";
            echo "<div class='cell'>Dataset</div>";
            echo "<div class='cell'>Project IDs</div>";
            echo "<div class='cell'>Submit Date</div>";
            echo "<div class='cell'>Status</div>";
            echo "</div>";

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
                unset($projectID, $submit_date, $status);
                array_push($tempArray, $row['projectID']);
                $submit_date = $row['submit_date'];
                $status = $row['status'];
              }
              $title = 'title';
              //$project_string = implode(', ', $tempArray);
              echo "<div class='row_special'>";
              echo "<div class='cell'>".$dID.'</div>';
              echo "<div class='cell'>".$title.'</div>';

              /** This part prints the project number normally if it is queried in the database, otherwise it is printed red
              **/
              echo "<div class='cell'>"; //begin with opening table
              $valid = true; //flag to set status as broken if anything is red
              $last_element = end($tempArray);
              foreach ($tempArray as $project_string) { //print project # as red if !exists or is no longer public
                $sql2="SELECT projectID, userID, ownership FROM Projects WHERE projectID = '$project_string'"; 
                $query = mysqli_query($con, $sql2);
                $row2 = $query->fetch_assoc();
                unset($projectID, $userID, $ownership);
                $projectID = $row2['projectID'];
                $userID = $row2['userID'];
                $ownership = $row2['ownership'];


                if (!$query) {
                  die('Query failed to execute');
                }

                if ((mysqli_num_rows($query) > 0 && $userID == $_SESSION['userSession']) || (mysqli_num_rows($query) > 0 && $ownership == -1)) {
                  echo $project_string;
                } else {
                  echo '<i style="color:red;font-family:arial; ">' . $project_string . '</i>';
                  $valid = false;
                }
                if ($last_element != $project_string) { //print a comma if this is not the last element in array for printing
                  echo ', ';
                }
              }
              echo '</div>'; //close the table value

              echo "<div class='cell'>".$submit_date.'</div>';

              if ($valid) {
                if (intval($status) == 0) { 
                  echo "<div class='cell'>Inactive</div>";
                } else {
                  echo "<div class='cell'>Active</div>";
                }
              } else {
                echo "<div class='cell'>Broken</div>";
              }
              echo "</div>";              
            }

            echo "</div>"; 
            echo "</div>";
            $con->close();
            ?>

          </form>
          <br />



        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
