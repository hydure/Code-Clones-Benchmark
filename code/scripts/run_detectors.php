<?php
session_start();
require_once '../class.user.php';
$user_home = new USER();

if(!$user_home->is_logged_in())
{
 $user_home->redirect('../index.php');
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
	<link href="../gh-buttons.css" type = "text/css" rel="stylesheet">
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
              <input type="button" onclick="location.href='../logout.php';" value="Logout" 
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
              <li><a href="../CCBHome.php">Home</a></li>
              <li><a href="../CCBProjects.php">Projects</a></li>
              <li><a href="../CCBDatasets.php">Datasets</a></li>
              <li class="active"><a href="#">Tools</a></li>
              <li><a href="../CCBReport.php">Reports</a></li>
              <li><a href="../CCBEvaluate.php">Evaluate</a></li>
              <li><a href="../CCBContacts.php">Contact</a></li>              
            </ul>
        </div>
  	
        <!-- main area -->
<!-- <html>
<body> -->

        <div class="col-xs-12 col-sm-9">
          <h1>Code Cloning Tools</h1>
<?php

header("refresh:10;url=../CCBTools.php");

if (!empty($_POST['detector'])) {


#echo "<br>You chose to run < "; 
#foreach($_POST['detector'] as $detector) {
#    echo "$detector ";
#}
#
#echo "> on dataset ".$_POST['datasetSelect']."<br>";

# connect to database
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}
    
# set userID and datasetID
$userID = intval($_SESSION['userSession']);
$datasetID=$_POST['datasetSelect'];

# obtain projectIDs from selected dataset
$sql="SELECT projectID FROM Datasets WHERE datasetID=".$datasetID;
$pIDs = mysqli_query($con, $sql);
if (!$pIDs) {
    die("Error: " . mysqli_error($con));
}

foreach($_POST['detector'] as $detector) {

        /***************************
        *                          *
        *          NICAD           *
        *                          *
        ***************************/
    if ($detector == "nicad") {
        echo "<h3>NiCad</h3>";

        if (empty($_POST['n_language'])) {
            echo "Please select a language.<br>";
            break;
        }

        # get language
        foreach ($_POST['n_language'] as $lang) {
            if ($lang != "") break;
        }
        
        # check if dataset has been examined already
        $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
            "datasetID=".$datasetID." AND detector='nicad'".
            " AND language='$lang'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            break;
        }
       
        # build arguments: language datasetID projectID1 url1 ...
        $args="$lang ".$datasetID;
        while($row = mysqli_fetch_array($pIDs)) {
            $pURL = mysqli_query($con, "SELECT url FROM Projects WHERE projectID=".$row['projectID']);
            if (!$pURL) {
                die("Error: " . mysqli_error($con));
            }

            $args="$args ".$row['projectID'];
            $args="$args ".mysqli_fetch_array($pURL)['url'];
        }

        # run nicad
        $nicad_path="/home/clone/nicad.sh";
        $out_file = "/home/pi/MyNAS/nicad/".$datasetID.".html";
        $cmd="(ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' >$out_file";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' | grep -v 'known hosts' ";
        $cmd="$cmd; php add_frags.php $detector $lang $userID $out_file";
        $cmd="$cmd; bash save_frags.sh $detector $out_file $args";
        $cmd="$cmd; php send_mail.php ".$_SESSION['email']." 'Job Finished' 'Dataset: $datasetID<br>".
                    "Detector: NiCad'";
        $cmd="$cmd) >/dev/null &";
        ignore_user_abort();
        shell_exec("$cmd");

        #$nicad_raw = shell_exec($cmd);
        #echo "$nicad_raw<br>";
        
        #update tags in Datasets
        date_default_timezone_set('America/New_York');
        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE Datasets SET Nicad_flag=1, submit_date='$date' ".
               "WHERE datasetID=".$datasetID;
        if(!$con->query($sql))
            echo "failed to update dataset info";

        echo "Job submitted. You will be notified by email upon completion.";

        /***************************
        *                          *
        *          DECKARD         *
        *                          *
        ***************************/
    } else if ($detector == 'deckard') {
        echo "<h3>Deckard</h3>";
        if (empty($_POST['d_language'])) {
            echo "Please select a language.<br>";
            break;
        }

        # get language
        foreach ($_POST['d_language'] as $lang) {
            if ($lang != "") break;
        }
        
        # check if dataset has been examined already
        $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
            "datasetID=".$datasetID." AND detector='deckard'".
            " AND language='$lang'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            break;
        }

        # build arguments: language datasetID projectID1 url1 ...
        $args="$lang ".$datasetID;
        while($row = mysqli_fetch_array($pIDs)) {
            $pURL = mysqli_query($con, "SELECT url FROM Projects WHERE projectID=".$row['projectID']);
            if (!$pURL) {
                die("Error: " . mysqli_error($con));
            }

            $args="$args ".$row['projectID'];
            $args="$args ".mysqli_fetch_array($pURL)['url'];
        }

        # run deckard
        $deckard_path="/home/clone/deckard.sh";
        $out_file = "/home/pi/MyNAS/deckard/".$datasetID."_out";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$deckard_path $args' | grep -v 'known hosts'";
        #$deckard_raw = shell_exec($cmd);
        #echo "$deckard_raw<br>";

        $cmd="(ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$deckard_path $args' >$out_file";
        $cmd="$cmd; php add_frags.php $detector $lang $userID $out_file";
        $cmd="$cmd; bash save_frags.sh $detector $out_file $args";
        $cmd="$cmd; php send_mail.php ".$_SESSION['email']." 'Job Finished' 'Dataset: $datasetID<br>".
                    "Detector: Deckard'";
        $cmd="$cmd) >/dev/null &";
        ignore_user_abort();
        shell_exec("$cmd");
        #echo "$cmd<br>";

        #update tags in Datasets
        date_default_timezone_set('America/New_York');
        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE Datasets SET Deckard_flag=1, submit_date='$date' ".
               "WHERE datasetID=".$datasetID;
        if(!$con->query($sql))
            echo "failed to update dataset info";

        #if (preg_match('/^Error: there are no/', $deckard_raw)) {
            #echo "$deckard_raw<br>";
            #break;
        #}

        echo "Job submitted. You will be notified by email upon completion.";
    }
}
mysqli_close($con);


} else {
    echo "No code clone detectors were selected.<br>";
}

?>
<hr>
<p>You will be redirected in 10 seconds... or click <a href="../CCBTools.php">here</a> to go back</p>

</div>
</html>
