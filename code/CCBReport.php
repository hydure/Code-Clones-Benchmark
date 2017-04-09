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
$sql = "SELECT datasetID, userID, file, start, end, detector FROM Clones WHERE userID = '$currUserID'";
$result = $con->query($sql);
$dataset_array_Deckard = array();
$dataset_array_Nicad = array();
$dataset_array_CCFinderX = array();
$file_array = array();
$dataset_files = array();
$last_dataset = 0;
$last_file = '';
$first_dataset = true;
while ($row = $result->fetch_assoc()) {
  unset($datasetID, $userID, $file, $start, $end, $detector);
  $datasetID = $row['datasetID'];
  $userID = $row['userID'];
  $detector = $row['detector'];
  $file = $row['file'];
  if ($_SESSION['userSession'] == $userID) { //handles detector -> dataset selection
    if ($detector == 'Deckard' && !in_array($datasetID, $dataset_array_Deckard)) {
      array_push($dataset_array_Deckard, $datasetID);
    }
    if ($detector == 'Nicad' && !in_array($datasetID, $dataset_array_Nicad)) {
      array_push($dataset_array_Nicad, $datasetID);
    }
    if ($detector == 'CCFinderX'&& !in_array($datasetID, $dataset_array_CCFinderX)) {
      array_push($dataset_array_CCFinderX, $datasetID);
    }
  }
  
  if ($last_dataset != $datasetID) { //Handles file creation
    array_unshift($dataset_files, $last_dataset);
    array_push($file_array, $dataset_files);
    if (!$first_dataset) {
      $dataset_files = array($last_file);
    } else {
      $dataset_files = array($file);
      $first_dataset = false;
    }
  } else {
    if (!in_array($last_file, $dataset_files)) {
      array_push($dataset_files, $last_file);
    }
  }
  $last_dataset = $datasetID;
  $last_file = $file;
}
array_unshift($dataset_files, $datasetID);
array_push($file_array, $dataset_files);
array_splice($file_array, 0, 1);       
$con->close();
//print_r($file_array);              
?>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">
function injectHTML(){

  //step 1: get the DOM object of the iframe.
  var iframe = document.getElementById('iframe_one');


  //step 1.5: get the correct string to be printed!
  <?php
  $datasetID = 1;
  $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
  if(mysqli_connect_errno()) {
      die("MySQL connection failed: ". mysqli_connect_error());
  }
  $file = 'src/AbstractTableRendering.java';
  $sql = "SELECT cloneID, start, end FROM Clones where datasetID= '$datasetID' AND file='$file'";
  $result = $con->query($sql);
  $clonesArray=array();
  while ($row = $result->fetch_assoc()) {
    $cloneID = $row['cloneID'];
    array_push($clonesArray, $cloneID);
    $start= $row['start'];
    $end = $row['end'];
  }

  $code_array = array();
  array_push($code_array, '<pre>');
  $handle = fopen('/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java', "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      $line = '<code>' . substr($line, 0, -1) . '</code><br>';
      array_push($code_array, $line);
    }
    fclose($handle);
  }
  array_push($code_array, '</pre>');
  $code_string = implode("", $code_array);
  $code_string = json_encode($code_string, JSON_HEX_TAG);
  $con->close();
  ?>

  var css = '<style>pre{counter-reset: line;}code{counter-increment: line;}code:before{content: counter(line); -webkit-user-select: none; display: inline-block; border-right: 1px solid #ddd; padding: 0 .5em; margin-right: .5em;}</style>';
  var code = <?php echo $code_string; ?>;
  var html_string = css + '<html><head></head><body><p>' + code + '</p></body></html>';
  /* if jQuery is available, you may use the get(0) function to obtain the DOM object like this:
  var iframe = $('iframe#target_iframe_id').get(0);
  */

  //step 2: obtain the document associated with the iframe tag
  var iframedoc = iframe.document;
    if (iframe.contentDocument)
      iframedoc = iframe.contentDocument;
    else if (iframe.contentWindow)
      iframedoc = iframe.contentWindow.document;

   if (iframedoc) {
     iframedoc.open();
     iframedoc.writeln(html_string);
     iframedoc.close();
   } else {
    alert('Cannot inject dynamic contents into iframe.');
   }


}
function displayDatasets() {
  
  var dataset_array;
  if (document.getElementById("detector1_checkbox").checked) { //return only Nicad datasets
    dataset_array = <?php  echo json_encode($dataset_array_Nicad); ?>;
  }
  if (document.getElementById("detector2_checkbox").checked) { //return only Deckard datasets
    dataset_array = <?php  echo json_encode($dataset_array_Deckard); ?>;
  }
  if (document.getElementById("detector1_checkbox").checked && document.getElementById("detector2_checkbox").checked) { //return both datasets
    dataset_array = <?php 
    $merged_array = array_unique(array_merge($dataset_array_Nicad, $dataset_array_Deckard), SORT_REGULAR);
    sort($merged_array);
    echo json_encode($merged_array); 
    ?>;
  }
  var select = $("#datasetSelect");
  select.empty(); // empties previous values;
  for (var value in dataset_array) {
    var option = document.createElement('option');
    option.innerHTML = dataset_array[value];
    option.value = dataset_array[value];
    select.append(option);
  }
}


</script>
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
        <div class="col-xs-6 col-sm-1 sidebar-offcanvas" id="sidebar" role="navigation">
            <ul class="nav">
              <li><a href="CCBHome.php">Home</a></li>
              <li><a href="CCBProjects.php">Projects</a></li>
              <li><a href="CCBDatasets.php">Datasets</a></li>
              <li><a href="CCBTools.php">Tools</a></li>
              <li class"active"><a href="#">Reports</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>
            </ul>
        </div>
        <!-- main area -->
        <div class="col-xs-12 col-sm-11">

          
            
          <form>
            <input type="checkbox" id="detector1_checkbox" name="detector[]" value="nicad">Nicad</label><br/>
            <input type="checkbox" id="detector2_checkbox" name="detector[]" value="deckard">Deckard</label><br/>
            <input type="checkbox" id="detector3_checkbox" name="detector[]" value="ccfinderx">CCFinderX</label><br/>
            <input type = "submit" name ="datasets_button" onClick="javascript:displayDatasets(); return false" value = "View Datasets" id = "datasets" />
          </form>
          <form>
            <select name='datasetSelect' id = 'datasetSelect' multiple/>
            <input type = "submit" name ="clones_button" onClick="javascript:displayClones(); return false" value = "View Clones" id = "clones" />
          </form>
            <form action="iframe.php" method="post" enctype='multipart/form-data'>

            Clone Clone:
            <select name= "clone_selected" id="clone_selected" multiple> 
              <?php 
                $datasetID = intval($_POST['datasetSelect']);
                $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
                if(mysqli_connect_errno()) {
                    die("MySQL connection failed: ". mysqli_connect_error());
                }
                $cloneID_array = array();
                $result = $con->query("SELECT cloneID FROM Clones where datasetID = '$datasetID'");
                while ($row = $result->fetch_assoc()) {
                  unset($cloneID);
                  $cloneID = $row['cloneID'];
                    if (!in_array($cloneID, $cloneID_array)) {
                      array_push($cloneID_array, $cloneID);
                      echo '<option value='.$cloneID.'>'.$cloneID.'</option>';
                    }
                }
              ?>
            </select>
            Frame One: 
            <select name= "file1_selected" id="file1_selected" multiple> 
              <?php 
                $file_array = array();
                $result = $con->query("SELECT file FROM Clones where datasetID = '$datasetID'");
                while ($row = $result->fetch_assoc()) {
                  unset($file);
                  $file = $row['file'];
                    if (!in_array($file, $file_array)) {
                      array_push($file_array, $file);
                      echo '<option value='.$file.'>'.$file.'</option>';
                    }
                }
              ?>
            </select>
            Frame Two:
            <select name= "file2_selected" id="file2_selected" multiple> 
              <?php 
                $file_array = array();
                $result = $con->query("SELECT file FROM Clones where datasetID = '$datasetID'");
                while ($row = $result->fetch_assoc()) {
                  unset($file);
                  $file = $row['file'];
                    if (!in_array($file, $file_array)) {
                      array_push($file_array, $file);
                      echo '<option value='.$file.'>'.$file.'</option>';
                    }
                }
                $con->close();
              ?>
            </select>
            <input type = "submit" name ="analyze_button" value = "Analyze Clones" id = "clone_dataset" />
            </form>

            <div align="center">
                <iframe id="iframe_one" width=60% height=70%></iframe>
               <!-- <iframe id="iframe_two" width=40% height=70%></iframe> -->
            </div>
            <!--frames for adding results above-->
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>