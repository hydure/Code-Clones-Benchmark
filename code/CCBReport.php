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
$sql = "SELECT cloneID, datasetID, userID, file, start, end, detector FROM Clones WHERE userID = '$currUserID'";
$result = $con->query($sql);
$dataset_array_Deckard = array();
$dataset_array_Nicad = array();
$dataset_array_CCFinderX = array();
$file_array = array();
$dataset_files = array();
$dataset_start = array();
$dataset_end = array();
$dataset_cloneID = array();
$start_array = array();
$end_array = array(); 
$cloneID_array = array();
$cloneID_index = array();
$last_datasetID = 0;
$last_cloneID = 0;
$last_file = '';
while ($row = $result->fetch_assoc()) { //store all possible relevant data into their correct arrays
  unset($datasetID, $userID, $file, $start, $end, $detector);
  $cloneID = $row['cloneID'];
  $datasetID = $row['datasetID'];
  $userID = $row['userID'];
  $detector = $row['detector'];
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


$handle_array = array(); //HERE IS WHERE WE NEED TO STORE ALL CORRECT FILE PATHS IN THIS ARRAY 
/**
$filepath1 = '/path/to/file1';
array_push($handle_array, $filepath1);
$filepath2 = '/path/to/file2';
array_push($handle_array, $filepath2); **/

$prepend = '/home/pi/MyNAS/';
$loaded_filepath_array = array();
$sql = "SELECT datasetID, projectID, file, detector FROM Clones WHERE userID = '$currUserID'";
$result = $con->query($sql);
while ($row = $result->fetch_assoc()) { 
  unset($datasetID, $projectID, $file, $detector);
  $datasetID = $row['datasetID'];
  $projectID = $row['projectID'];
  $file = $row['file'];
  $detector = $row['detector'];
  if (!in_array($file, $loaded_filepath_array)) {
    array_push($loaded_filepath_array, $file);
    if ($detector == 'deckard') {
      $filepath = $prepend . $detector . "/" . $datasetID . "/src/" . $projectID . "/" . $file;
    }
    if ($detector == 'nicad') {
      $filepath = $prepend . $detector . "/" . $datasetID . "/" . $projectID . "/" . $file;
    }
    $dual_file_array = array();
    array_push($dual_file_array, $filepath);
    array_push($dual_file_array, $file);
    array_push($handle_array, $dual_file_array);
    //echo $filepath . " | ";
  }
}
$con->close();
/** handlea_array stored as
( ($filepath, $filename ), ($filepath, $filename ), ... )

source file stored as 
( ($filename1, line1, line2, ...), ($filename2, line1, line2, ...), ...)
**/

$sourcefile_array = array();
foreach ($handle_array as $handlepath) {
  $handlepath1 = $handlepath[0]; //file path like /path/to/storage/deckard/34/AbstractTableRendering.java
  $handlepath2 = $handlepath[1]; //file name like src/AbstractTableRendering.java
  $line_array = array(); //array to store an entire file, with a line as a single index (newline char stripped)
  $handle = fopen($handlepath1, "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      array_push($line_array, $line);
    }
    fclose($handle);
  }
  
  array_unshift($line_array, $handlepath2); //places filename at front of array
  array_push($sourcefile_array, $line_array);
}
//TEMP CODE STARTS here


?>
<link rel="stylesheet" type="text/css" href="hlns.css" media="screen">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/highlight.min.js"></script>
<script type='text/javascript' src="highlightjs-line-numbers.min.js"></script>
<script>
hljs.initHighlightingOnLoad();
hljs.initLineNumbersOnLoad();
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">

var GlobalVar = {};
function analyzeClones(){
  //step 1: get the DOM object of the iframe. 
  var file1_selector = document.getElementById('file1Select');
  var file2_selector = document.getElementById('file2Select'); 
  var file1_value = file1_selector[file1_selector.selectedIndex].value;
  var file2_value = file2_selector[file2_selector.selectedIndex].value;
  document.getElementById('file1_name').innerHTML = file1_value;
  document.getElementById('file2_name').innerHTML = file2_value;
  value = GlobalVar.value;
  var start_array = <?php  echo json_encode($start_array); ?>;
  for (var index in start_array) { //find range for selected files
    if (start_array[index][0] == value) {
      var selected_start_array = start_array[index].slice(1);
    }
  }
  var end_array = <?php  echo json_encode($end_array); ?>;
  for (var index in end_array) { //find range for selected files
    if (end_array[index][0] == value) {
      var selected_end_array = end_array[index].slice(1);
    }
  }
  var dummy1_array = []; //these will contain the unmodified source code
  var dummy2_array = [];
  var sourcefile_array = <?php echo json_encode($sourcefile_array); ?>;
  //document.getElementById('testme').innerHTML = html_string;

  for (var index in sourcefile_array) { 
    if (sourcefile_array[index][0] == file1_value) {
      dummy1_array = sourcefile_array[index].slice(1);
      //alert(dummy1_array);

    }
    if (sourcefile_array[index][0] == file2_value) {
      dummy2_array = sourcefile_array[index].slice(1);
      //alert(dummy2_array);
    } 
  }
/** <?php
  $dummy1_array = array();
  $dummy2_array = array();
  $handle = fopen('/path/to/file1', "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      array_push($dummy1_array, $line);
    }
    fclose($handle);
  }
  $handle = fopen('/path/to/file2', "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      array_push($dummy2_array, $line);
    }
    fclose($handle);
  } 
  ?>**/
  //var dummy1_array = <?php echo json_encode($dummy1_array); ?>;
  //var dummy2_array = <?php echo json_encode($dummy2_array); ?>;
  var code1 = makeIframeContent(dummy1_array, selected_start_array, selected_end_array, file1_value);
  var code2 = makeIframeContent(dummy2_array, selected_start_array, selected_end_array, file2_value); 
  //alert(code1);
  var iframe1 = document.getElementById('iframe1');
  var iframe2 = document.getElementById('iframe2');
  injectIframeContent(iframe1, code1);
  injectIframeContent(iframe2, code2);

}
function injectIframeContent(iframe, code) {
  var script1 = "<link rel='stylesheet' type='text/css' href='hlns.css' media='screen'>";
  var script2 = "<script src='//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/highlight.min.js'>";
  var script3 = "<script type='text/javascript' src='highlightjs-line-numbers.min.js'>";
  var scriptA = "</";
  var scriptB = "script>";
  var scriptC = scriptA + scriptB;
  var script4 = "<script>hljs.initHighlightingOnLoad();hljs.initLineNumbersOnLoad();";
  var script = script1 + script2 + scriptC + script3 + scriptC + script4 + scriptC;
  var html_string = script + '<html><head></head><body><p>' + code + '</p></body></html>';
  //document.getElementById('testme').innerHTML = html_string;
  //alert(html_string);
  //var iframe = document.getElementById('iframe1');
  //html_string = "<html>HELLO</html>";
  //step 2: obtain the document associated with the iframe tag
  var iframedoc = iframe.document;
    if (iframe.contentDocument)
      iframedoc = iframe.contentDocument;
    else if (iframe.contentWindow)
      iframedoc = iframe.contentWindow.document;

   if (iframedoc){
     iframedoc.open();
     iframedoc.writeln(html_string);
     iframedoc.close();
   } else {
    alert('Cannot inject dynamic contents into iframe.');
   } 
}

function makeIframeContent(dummy_array, selected_start_array, selected_end_array, file_value) {
  var line_counter = 0;
  var array_iterator = 0;
  var scroll_counter = 0;
  var highlighted = false;
  var code_array = [];
  code_array.push("<pre><code>");
  for (var index in dummy_array) {
    var line = dummy_array[index];
    if (line_counter > 0) {
      //line = line.split('"').join("&quot"); //escapes HTML markup
      line = line.split("&").join("&amp");
      line = line.split("<").join("&lt");
      line = line.split(">").join("&gt");
    } 
    if ((line_counter == selected_start_array[array_iterator] && file_value == selected_start_array[array_iterator + 1]) || highlighted) {
      line = "<mark>" + line + '</mark>';
      if (highlighted == false) {
        highlighted = true;
      }
    }
    code_array.push(line);
    line_counter++; 
    if (line_counter == selected_end_array[array_iterator]) {
      highlighted = false;
      array_iterator += 2;
    }
  }
  code_array.push('</code></pre>');
  code = code_array.join("");
  return code;
}


function displayDatasets() {
  
  var dataset_array;
  if (document.getElementById("detector1_checkbox").checked) { //return only Nicad datasets
    dataset_array = <?php  echo json_encode($dataset_array_Nicad); ?>;
  }
  if (document.getElementById("detector2_checkbox").checked) { //return only Deckard datasets
    dataset_array = <?php  echo json_encode($dataset_array_Deckard); ?>;
  }
  if (document.getElementById("detector1_checkbox").checked && document.getElementById("detector2_checkbox").checked) { //return datasets only with both detectors ran
    dataset_array = <?php
    $return_array = array_intersect($dataset_array_Nicad, $dataset_array_Deckard);
    echo json_encode($return_array);
    ?>
  }
  var dataset_selector = $("#datasetSelect");
  $("#datasetSelect").empty(); // empties previous values;
  for (var index in dataset_array) {
    var option = document.createElement('option');
    option.innerHTML = dataset_array[index];
    option.value = dataset_array[index];
    dataset_selector.append(option);
  }
}

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

function displayFiles() {
  var selector = document.getElementById('cloneSelect');  
  var value = selector[selector.selectedIndex].value;
  GlobalVar.value = value;
  var file_array = <?php  echo json_encode($file_array); ?>;
  for (var index in file_array) { //find range for selected files
    if (file_array[index][0] == value) {
      var selected_file_array = file_array[index].slice(1);
    }
  }
  var file1_selector = document.getElementById('file1Select');
  var file2_selector = document.getElementById('file2Select');
  if (document.getElementById("files_frame1_checkbox").checked) {
    $("#file1Select").empty();
  }
  if (document.getElementById("files_frame2_checkbox").checked) {
    $("#file2Select").empty();
  }
  for (var index in selected_file_array) { //displays files multiselectors
    if (document.getElementById("files_frame1_checkbox").checked) {
      var option = document.createElement('option');
      option.innerHTML = selected_file_array[index];
      option.value = selected_file_array[index];
      file1_selector.append(option);
    }
    if (document.getElementById("files_frame2_checkbox").checked) {
      
      var option = document.createElement('option');
      option.innerHTML = selected_file_array[index];
      option.value = selected_file_array[index];
      file2_selector.append(option);  
    } 
  }
}


</script>
<html lang="en">
<!-- still need to create sidebar, etc. -->
<head>
	<title>Code Clones Benchmark</title>
  <style>
    .box {
      float:left;
      padding-top:80px;
      padding-left:100px;
    }

    .clear {
      clear:both;
    }
    .iframe_container {
	position:absolute;
	z-index:100;
	top:38%;
	margin: 0 auto;
    }
    .colform {
	float:left;
	width:auto;
	padding-right:30px;
    }

  </style>
	<link href="gh-buttons.css" type = "text/css" rel="stylesheet">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<div style="position:relative;">
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
              <li class"active"><a href="#">Reports</a></li>
              <li><a href="CCBEvaluate.php">Evaluate</a></li>
              <li><a href="CCBContacts.php">Contact</a></li>
            </ul>
        </div>
        <!-- main area -->
        <div class="col-xs-12 col-sm-9">
        <p id="demo"></p>      
          <form class="colform">
            <input type="checkbox" id="detector1_checkbox" name="detector[]" value="nicad">Nicad</label><br/>
            <input type="checkbox" id="detector2_checkbox" name="detector[]" value="deckard">Deckard</label><br/>
            <!--<input type="checkbox" id="detector3_checkbox" name="detector[]" value="ccfinderx">CCFinderX</label><br/>-->
	    <br>
            <input type = "submit" name ="datasets_button" onClick="javascript:displayDatasets(); return false" value="View Datasets" id="datasets" class="buttonA"/>
          </form>
          <form class="colform">
            Datasets:
            <select name='datasetSelect' id='datasetSelect' multiple/></select>
	    <br>
            <input type="submit" name="clones_button" onClick="javascript:displayClones(); return false" value="View Clones" id="clones" class="buttonA"/>
          </form>
          <form class="colform">
	    <div style="float:left;">
            	Clones:
            	<select name="cloneSelect" id="cloneSelect" multiple></select> 
	    	<br>
	    	<input type="submit" name ="files_button" onClick="javascript:displayFiles(); return false" value="View Files" id="files" class="buttonA"/>
	    </div>
	    <div class = "checkbox_files" style="float:right;">
            	<input type="checkbox" id="files_frame1_checkbox" name="files_checkbox[]" value="files_frame1">Show Files in Frame One</label><br/>
            	<input type="checkbox" id="files_frame2_checkbox" name="files_checkbox[]" value="files_frame2">Show Files in Frame Two</label><br/>
	    </div>
          </form>
	</div><!-- /.col-xs-12 main -->
	<div class="col-xs-12 col-sm-9">
          <form class="class_frame">
	    <div style="float:left;padding-right:30px;">
            	Frame One: 
	    	<br>
            	<select name="file1Select" id="file1Select" multiple></select> 
	    </div>
	    <div style="float:left;padding-right:30px;">
            	Frame Two:
		<br>
            	<select name="file2Select" id="file2Select" multiple></select>
            	<input type="checkbox" id="only_clones_checkbox" name="only_clones" value="only_clones">Only Show Clones</label><br/> 
	    </div>
	    <br style="clear: left;">
	    <br>
	    <div>
            <input type="submit" name ="analyze_button" onClick="javascript:analyzeClones(); return false" value="Analyze Clones" id="clones_for_file" class="buttonA"/>
	    </div>
          </form>
              
          
            <!--frames for adding results above-->
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
   <div class="box" id="file1_name"></div>
   <iframe id="iframe1" frameborder="1" width=40% height=512 align="left"></iframe>
   
   <div class="box" id="file2_name"></div>
   <iframe id="iframe2" frameborder="1" width=40% height=512 align="right"></iframe>
   
<div id="testme"></div>
</div><!--/.page-container-->
</div>

</html>
