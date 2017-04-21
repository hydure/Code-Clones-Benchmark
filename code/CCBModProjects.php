<!-- This page handles file deletion and evaluation! -->
<?php
session_start();
if ($_POST['project_action'] == 'Delete Project') {
	$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
	if(mysqli_connect_errno()) {
		die("MySQL connection failed: ". mysqli_connect_error());
	}
	$size = NULL;
	$project_val = $_POST['projectSelect'];
	//delete folder if applicable
	$query = "Select title, size from Projects where projectID='".$project_val."'";
	if(!$return = $con->query($query)){
		echo "Failed to query database";
	}
	else{
		$value = $return->fetch_assoc();
		//url is being used to determine if a project is a url upload or not
		$size = $value['size'];
	}
	//if there is a url
	if($size){
		//remove the file
		$path = "/home/pi/MyNAS/files/p$project_val";
		$title = $value['title'];
		if(!unlink("$path/$title")){
			echo "Failed to delete file";
		}
		if(!rmdir($path)){
			echo "Failed to delete server file!";
		}
	}
	
	$sql = "DELETE FROM Projects WHERE projectID='".$project_val."'"; //delete project from database
	if ($con->query($sql) == TRUE) {
		echo "Record delted sucessfully";

	} else {
		echo "Error Deleting Record: " . $con->error;
	}
	$sql = "SELECT datasetID FROM Datasets where projectID='".$project_val."'";
	$result = $con->query($sql);
	while($row = $result->fetch_assoc()){
		$uid = $_SESSION['userSession'];
		$sql = "UPDATE Datasets SET status=-1 WHERE datasetID='".$row['datasetID']."'"; //update status in datasets
		if ($con->query($sql) == TRUE) {
			echo "Record delted sucessfully";

		} else {
			echo "Error Deleting Record: " . $con->error;
		}
	}
	$con->close();
	header('Location:CCBProjects.php');
}

if ($_POST['project_action'] == 'Switch Ownership') {
	$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
	if(mysqli_connect_errno()) {
		die("MySQL connection failed: ". mysqli_connect_error());
	}
	$project_val = $_POST['projectSelect'];

	$query = "SELECT ownership, userId, projectID from Projects where projectID='".$project_val."'";
	//$result = $con->query("SELECT projectID, title, commit, last_accessed, uploaded, ownership, url, size, userId, author FROM Projects");
	if(!$return = $con->query($query)){
		echo "Failed to query database";
	} else {
		$value = $return->fetch_assoc();
		$ownership_val = $value['ownership'];
		$userId = $value['userId'];
		echo "$userId";
		$curr_user = $_SESSION['userSession'];
		$query1 = "SELECT datasetID from Datasets where projectID = ".$project_val;
		$return = $con->query($query1);
		if ($ownership_val == -1 && $curr_user = $userId) { //switch to private if its public & the user is the owner of the file
			while($row = $return->fetch_assoc()){
				$datasetID = $row['datasetID'];
				$sql = "UPDATE Datasets SET status=-1 WHERE userId!=".$userId." && datasetID =".$datasetID;
				if($con->query($sql) != TRUE) echo "failed to switch database status";
			}
			$sql = "UPDATE Projects SET ownership = " . $userId . " WHERE projectID='".$project_val."'";
		}
		if ($ownership_val != -1 && $curr_user = $userId) { //switch to public if its private & the user is the owner of the file
			while($row = $return->fetch_assoc()){
				$datasetID = $row['datasetID'];
				$sql = "UPDATE Datasets SET status=0 WHERE userId!=".$userId." && datasetID =".$datasetID;
				if($con->query($sql) != TRUE) echo "failed to switch database status";
			}
			$sql = "UPDATE Projects SET ownership = -1 WHERE projectID='".$project_val."'";
		}
		echo $query;
	}


	if ($con->query($sql) == TRUE) {
		echo "Record changed sucessfully";

	} else {
		echo "Error Changing Record: " . $con->error;
	}
	$con->close();
	header('Location:CCBProjects.php');
}


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
}

tr:nth-child(even) {
    background-color: #dddddd;
}

div.round {
	border: = 2px solid grey;
	border-radius: = 8px;
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
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>

          <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Projects</h1>
          <br />
            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
          	$result = $con->query("SELECT projectID, title, commit, last_accessed, uploaded, ownership, url, size, userId FROM Projects");

			echo "<html>";
			echo "<body>";
			echo "<table>";
			echo "<tr>";
			echo "<th>Project ID</th>";
			echo "<th>Name</th>";
			echo "<th>commit</th>";
			echo "<th>Last Accessed</th>";
			echo "<th>Date Uploaded</th>";
			echo "<th>Ownership</th>";
			echo "<th>URL</th>";
			echo "<th>Size (bytes)</th>";
			echo "</tr>";

			$project_val = $_POST['projectSelect'];
			//$project_val = $_POST['ProjectSelect'];
			//echo $project_val;

			while ($row = $result->fetch_assoc()) {
			
				unset($projectID, $title, $userId);
				$projectID = $row['projectID'];
				$userId = $row['userId'];
				if ($_SESSION['userSession'] == $userId && $projectID == $project_val) {
					$title = $row['title'];
					$commit = $row['commit'];
					$last_accessed = $row['last_accessed'];
					$uploaded = $row['uploaded'];
					$ownership = $row['ownership'];
					$url = $row['url'];
					$size = $row['size'];
					#$content = $row['content'];

					echo "<tr>";
					echo '<th>'.$projectID.'</th>';
					echo '<th>'.$title.'</th>';
					echo '<th>'.$commit.'</th>';
					echo '<th>'.$last_accessed.'</th>';
					echo '<th>'.$uploaded.'</th>';
					if (intval($ownership) == -1) { 
					  echo '<th>Private</th>';
					}
					else {
					  echo '<th>Public</th>';
					}
					echo '<th>'.$url.'</th>';
					echo '<th>'.$size.'</th>';
					echo "</tr>";
					//echo '<tr>';
					//echo '<th>'.$content.'</th>';
					//echo '<\tr>';
					break;
				}
			}   
			echo "</table";
			echo "<br/>";
			//echo "<div class='code'>";
			echo "Content: ";
			#$echo '<div class="round">'.$content.'</div>';
			//echo "</div>";
			echo "<br/>";
			echo "</body>";
			echo "</html>";
            $con->close();
            ?>

        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
