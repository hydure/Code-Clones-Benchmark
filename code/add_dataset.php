<?php
header("refresh:3;url=CCBDatasets.php");
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
  max-width: 800px;
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
	<link href="gh-buttons.css" type = "text/css" rel="stylesheet">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>
    function cb1Change(obj) {
        var cbs = document.getElementsByClassName("cb1");
        for (var i = 0; i < cbs.length; i++) {
            cbs[i].checked = false;
        }
        obj.checked = true;
    }
    function cb2Change(obj) {
        var cbs = document.getElementsByClassName("cb2");
        for (var i = 0; i < cbs.length; i++) {
            cbs[i].checked = false;
        }
        obj.checked = true;
    }
  </script>
        <script type="text/javascript">
            function showSnackbar() {
                // Get the snackbar DIV
                var x = document.getElementById("snackbar")

                // Add the "show" class to DIV
                x.className = "show";

                // After 3 seconds, remove the show class from DIV
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
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
              <li class="active"><a href="#">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBReport.php">Reports</a></li>
              <li><a href="CCBEvaluate.php">Evaluate</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>              
            </ul>
        </div>


        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
        <h1>Code Cloning Datasets</h1> <br>

<?php
//session_start();
if (!empty($_POST['row'])) {
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}

$sql = "SELECT * FROM Datasets ORDER BY datasetID DESC limit 1;";
$query = mysqli_query($con, $sql);
$datasetID = mysqli_fetch_assoc($query)['datasetID'] + 1;
$userId = intval($_SESSION['userSession']);
$submit_date = 'Never';

date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');
if ("$_POST[ownership_type]" == "1") {
            $ownership = 0;
        } else {
            $ownership = -1;
        }
foreach($_POST['row'] as $row) {
    $sql="INSERT INTO Datasets (datasetID, projectID, userId, submit_date, ".
         "status, ownership) VALUES ($datasetID, $row, $userId, '$submit_date', FALSE, '$ownership')";
    $date_sql = "UPDATE Projects SET last_accessed='$date' WHERE projectID=".$row;
    if(!$con->query($date_sql))
        echo "failed to update dataset info<br>";

	if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
	}
}


echo "Dataset Successfully added.";

mysqli_close($con);
} else {
echo "No projects were selected.";
}

?>
<hr>
<p>You will be redirected in 3 seconds... or click <a href="CCBDatasets.php">here</a> to go back</p>
</div>
</html>
