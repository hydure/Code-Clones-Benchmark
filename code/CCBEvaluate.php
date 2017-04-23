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



//this area of code finds ALL relevant data for the parameter selection
$con = new mysqli('localhost', 'root', '*XMmysq$', 'cc_bench');
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}
$currUserID = ($_SESSION['userSession']);
$sql = "SELECT cloneID, datasetID, userID, file, start, end, detector, language FROM Clones WHERE userID = '$currUserID'";
$result = $con->query($sql);
$dataset_array_Deckard = array();
$dataset_array_Nicad = array();
$dataset_array_CCFinderX = array();
$file_array = array();
$dataset_files = array();
$dataset_start = array();
$dataset_end = array();
$dataset_cloneID = array();
$dataset_language = array();
$language_array = array();
$start_array = array();
$end_array = array(); 
$cloneID_array = array();
$cloneID_index = array();
$last_datasetID = 0;
$last_cloneID = 0;
$last_file = '';
while ($row = $result->fetch_assoc()) { //store all possible relevant data into their correct arrays
  unset($datasetID, $userID, $file, $start, $end, $detector, $language);
  $cloneID = $row['cloneID'];
  $datasetID = $row['datasetID'];
  $userID = $row['userID'];
  $detector = $row['detector'];
  $language = $row['language'];
  $file = $row['file'];
  $start = $row['start'];
  $end = $row['end'];
  //handles detector -> dataset selection
  if ($_SESSION['userSession'] == $userID) { 
    if ($detector == 'deckard' && !in_array($datasetID, $dataset_array_Deckard)) {
      array_push($dataset_array_Deckard, $datasetID);
    }
    if ($detector == 'nicad' && !in_array($datasetID, $dataset_array_Nicad)) {
      array_push($dataset_array_Nicad, $datasetID);
    }
    if ($detector == 'CCFinderX'&& !in_array($datasetID, $dataset_array_CCFinderX)) {
      array_push($dataset_array_CCFinderX, $datasetID);
    }
  }
  /** handles language array creation, where
  language array = ((datasetID1, $language1), (datasetID2, $language), ..)
  **/
  if ($last_datasetID != $datasetID) {
    array_push($dataset_language, $datasetID);
    array_push($dataset_language, $language);
    if (!in_array($dataset_language, $language_array)) {
      array_push($language_array, $dataset_language);
      $dataset_language = array();
    }
  }
  /** handles cloneID array creation, where
  cloneID array = ((datasetID1, $clone1, $clone2, ...),(datasetID2, $clone1, $clone2, ...), ..)
  **/
  if ($last_datasetID != $datasetID) {
    array_unshift($dataset_cloneID, $last_datasetID);
    //array_push($cloneID_index, $index);
    array_push($cloneID_array, $dataset_cloneID);
    $dataset_cloneID= array($cloneID);
  } else {
    if (!in_array($cloneID, $dataset_cloneID)) {
      array_push($dataset_cloneID, $cloneID);
    }
  } 
  /** handles clone line start and end array creation, where
  start_array = ((cloneID1, $start1, $start1's_file, $start2, $start2's_file...),(cloneID2, $start1, $start1's_file, $start2, $start2's_file, ...), ..)
  **/
  if ($last_cloneID != $cloneID) {
    array_unshift($dataset_start, $last_cloneID);
    array_push($start_array, $dataset_start);
    array_unshift($dataset_end, $last_cloneID);
    array_push($end_array, $dataset_end);
    $dataset_start = array($start);
    array_push($dataset_start, $file);
    $dataset_end = array($end);
    array_push($dataset_end, $file);
  } else {
     array_push($dataset_start, $start);
     array_push($dataset_end, $end);
     array_push($dataset_start, $file);
     array_push($dataset_end, $file);
  }
  /** handles file creation, where 
  file_array = ((datasetID1, $file1, $file2, ...),(datasetID2, $file1, $file2, ...), ...)
  **/
  if ($last_cloneID != $cloneID) { 
    array_unshift($dataset_files, $last_cloneID);
    array_push($file_array, $dataset_files);
    $dataset_files = array($file);
  } else {
    if (!in_array($file, $dataset_files)) {
      array_push($dataset_files, $file);
    }
  }
  $last_cloneID = $cloneID;
  $last_datasetID = $datasetID;
  $last_file = $file;
}
//deletes first blank array values and adds the most recent start, end, or file to array
array_unshift($dataset_cloneID, $last_datasetID);
array_push($cloneID_array, $dataset_cloneID);
array_splice($cloneID_array, 0, 1);
array_unshift($dataset_start, $last_cloneID);
array_push($start_array, $dataset_start);
array_unshift($dataset_end, $last_cloneID);
array_push($end_array, $dataset_end);
array_splice($start_array, 0, 1); 
array_splice($end_array, 0, 1); 
array_unshift($dataset_files, $last_cloneID);
array_push($file_array, $dataset_files);
array_splice($file_array, 0, 1);       
$con->close();

$handle_array = array(); //HERE IS WHERE WE NEED TO STORE ALL CORRECT FILE PATHS IN THIS ARRAY
$filepath1 = '/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractAsyncTableRendering.java';
array_push($handle_array, $filepath1);
$filepath2 = '/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java';
array_push($handle_array, $filepath2);

/** source file stored as 
( ($filename1, line1, line2, ...), ($filename2, line1, line2, ...), ...)
**/
$c = 0;
$sourcefile_array = array();
foreach ($handle_array as $handlepath) {
  $line_array = array(); //array to store an entire file, with a line as a single index (newline char stripped)
  $handle = fopen($handlepath, "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      array_push($line_array, $line);
    }
  }
  fclose($handle);
  if ($c == 0) {  //THIS IS TEMPORARY, FIX WHEN WE GET CORRECT HANDLE_ARRAY
    array_unshift($line_array, "src/AbstractAsyncTableRendering.java");
  }
  if ($c == 1) {
    array_unshift($line_array, "src/AbstractTableRendering.java");
  }
  array_push($sourcefile_array, $line_array);
  $c += 1;

}

?>
<link rel="stylesheet" type="text/css" href="hlns.css" media="screen">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/highlight.min.js"></script>
<script type='text/javascript' src="highlightjs-line-numbers.min.js"></script>
<script>hljs.initHighlightingOnLoad(); hljs.initLineNumbersOnLoad();
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">


function displayClones() { 
  var selector = document.getElementById('datasetSelect');  
  var value = selector[selector.selectedIndex].value;
  var cloneID_array = <?php  echo json_encode($cloneID_array); ?>;
  for (var index in cloneID_array) { //find range for selected cloneID
    if (cloneID_array[index][0] == value) {
      var selected_cloneID_array = cloneID_array[index].slice(1);
    }
  }
  var clone_selector = document.getElementById('cloneSelect');
  $("#cloneSelect").empty();
  for (var index in selected_cloneID_array) { //displays clones
    var option = document.createElement('option');
    option.innerHTML = selected_cloneID_array[index];
    option.value = selected_cloneID_array[index];
    clone_selector.append(option);    
  }
}

window.onload = function () {
  var language_array = <?php echo json_encode($language_array); ?>;
  alert(language_array);
}



</script>
<html lang="en">
<!-- still need to create sidebar, etc. -->
<head>
	<title>Code Clones Benchmark</title>
  <style>
    .box {
      float:left;
      margin-right:20px;
    }

    .clear {
      clear:both;
    }
  </style>
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
        <div class="col-xs-6 col-sm-1 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li><a href="CCBReport.php">Reports</a></li>
              <li class"active"><a href="#">Evaluate</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>
            </ul>
        </div>
        <!-- main area -->
        <div class="col-xs-12 col-sm-11">

        <form>
          Dataset Select:
          <select name="datasetSelect" id="datasetSelect" multiple></select>
        </form>

              
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->

</div><!--/.page-container-->

<div>

</html>
