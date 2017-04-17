<?php
session_start();
require_once '../user/class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
 $user_home->redirect('../index.php');
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
	<meta http-equiv="refresh" content="4">
	<title>Code Clones Benchmark</title>
	<link href="../styles/CCB1.1.css" type = "text/css" rel="stylesheet">
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
              <input type="button" onclick="location.href='../user/logout.php';" value="Logout" 
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
              <li class="active"><a href="#">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBReport.php">Reports</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
  	
        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
          <h1>Description of Project</h1>

          	<p><strong>Code Clones</strong> are pieces of source code that are similar. These pieces can be defined as clones if they are <strong>textually, structurally or functionally similar.</strong> While there are several code clone detection techniques, an open crowd-sourced benchmark of true clones to evaluate these clones effectively is missing. <strong>The goal of this project is to create a web solution for running and evaluating clone detectors.</strong> </p>

			<p>	The system will allow users to select and parameterize a set of clone detectors on an uploaded dataset (or upload a new one). Additionally, there should be basic functionality to support uploading a new clone detection approach. The web interface should also be deployed as a web-app where a user can explore the database and see the actual source code of the clone pairs. Then, it should offer the possibility for any user to evaluate clone pairs (either on their own private results or the public benchmark).</p>
<br>
<br>

<h3>Status of Running Code Detectors</h3>
<?php
$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}
$ID_array = array();
$uid = $_SESSION['userSession'];
$result = $con->query("SELECT datasetID, percent FROM Datasets WHERE userId='$uid' && status=1");
echo "<html>";
echo "<body>";
echo "<table>";
echo "<tr>";
echo "<th>Dataset ID</th>";
echo "<th>Percent Done</th>";

echo "</tr>";
while ($row = $result->fetch_assoc()) {
	$data = $row['datasetID'];
	$percent = $row['percent'];
	echo "<tr>";
	echo "<th>$data</th>";
	echo "<th>$percent%</th>";
	echo "</tr>";
}
echo "</table>"; 
echo "</body>";
echo "</html>";
?>
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
