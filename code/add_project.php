<?php
header("refresh:3;url=CCBProjects.php");
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
        <h1>Code Cloning Projects</h1> <br>
<?php
error_reporting(0);
if (!empty($_POST[url]) && !empty($_POST[commit])) {
$con = mysqli_connect('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

# normalize url
$url = preg_match(":(.*com/[^/]*/[^/]*):", $_POST['url'], $matches, PREG_OFFSET_CAPTURE);
$url = $matches[0][0];
#echo "$url<br>";

# get title from url
$title = exec("echo $url | sed 's:.*\.com/[^/]*/::' | sed 's:/.*::'");

# get repo host username
$user = exec("echo $url | sed 's:.*com/::' | sed 's:/.*::'");

# get head commit number
if ("$_POST[commit]" == "head") {
	$commit = exec("/home/pi/Code-Clones-Benchmark/code/get_head_commit.sh $url | head -c 12");
} else {
	$commit = exec("echo $_POST[commit] | head -c 12");
}

# get date
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

#get userId
$uid = $_SESSION['userSession'];

# set ownership
if ("$_POST[ownership_type]" != "1") {
	$ownership = -1;
} else {
	$ownership = (int) $uid;
}
if($stmt = mysqli_prepare($con, "Select commit FROM Projects where title=? AND ownership=?")){
	mysqli_stmt_bind_param($stmt, "si", $title, $ownership);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $dbcommit);
	$check = TRUE;
	while(mysqli_stmt_fetch($stmt)){
		if($commit == $dbcommit){
			$check = False;
			echo "Project $title with commit $commit already exists!";
		}
	}
}



# add entry to Projects table if no matching commit number
if($check){
$sql="INSERT INTO Projects (title, url, commit, uploaded, ownership, userId, author)
        VALUES('$title', '$url', '$commit', '$date', '$ownership', '$uid', '$user')";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
    #echo '<link href="CCB1.1.css" type = "text/css" rel="stylesheet">
          #<div id="snackbar">Successfully added a project!</div>';
    echo "Successfully added a project!";
}
mysqli_close($con);
} else {
    #echo '<link href="CCB1.1.css" type = "text/css" rel="stylesheet">',
          #'<div id="snackbar">Must enter URL and commit number.</div>',
          #'showSnackbar()', '</script>';
    echo "Must enter URL and commit number.";
}
?>
<hr>
<p>You will be redirected in 3 seconds... or click <a href="CCBProjects.php">here</a> to go back</p>
</div>
</html>
