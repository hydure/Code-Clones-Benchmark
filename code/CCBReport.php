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
      //$line = substr($line, 0, -1);
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
//print_r(count($sourcefile_array));    
?>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">
var GlobalVar = {};
function analyzeClones(){
  //step 1: get the DOM object of the iframe.
  var iframe = document.getElementById('iframe1');


  <?php /**
  $code_array = array();
  $line_counter = 1;
  $array_counter = 0;
  $highlighting = false;
  array_push($code_array, '<pre>');
  $handle = fopen('/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/AbstractTableRendering.java', "r");
  if ($handle) {
    while (($line = fgets($handle)) != false) {
      if ($line_counter == $start_array[$array_counter] || $highlighting) {   //highlight l
        $line = '<code><mark>' . substr($line, 0, -1) . '</mark></code><br>';
        
        if ($highlighting == false) {
          $highlighting = true;
        }
      } else {
        $line = '<code>' . substr($line, 0, -1) . '</code><br>';        
      }
      array_push($code_array, $line);
      $line_counter += 1; 
      if ($line_counter == $end_array[$array_counter]) {
        $highlighting = false;
        if ($array_counter <= count($start_array)) {
          $array_counter += 1;
        }
      } 
    } 
    fclose($handle);
  } **/
  //array_push($code_array, '</pre>');
  //$code_string = implode("", $code_array);
  $code_string = 'tit';
  $code_string = json_encode($code_string, JSON_HEX_TAG);  
  ?>
  
  var file1_selector = document.getElementById('file1Select');
  var file2_selector = document.getElementById('file2Select');
  var file1_value = file1_selector[file1_selector.selectedIndex].value;
  var file2_value = file2_selector[file2_selector.selectedIndex].value;
  value = GlobalVar.value;
  //alert("global: " + GlobalVar.value);
  var start_array = <?php  echo json_encode($start_array); ?>;
  for (var index in start_array) { //find range for selected files
    if (start_array[index][0] == value) {
      var selected_start_array = start_array[index].slice(1);
      alert(selected_start_array);
    }
  }
  var end_array = <?php  echo json_encode($end_array); ?>;
  for (var index in end_array) { //find range for selected files
    if (end_array[index][0] == value) {
      var selected_end_array = end_array[index].slice(1);
      //alert(selected_end_array);
    }
  }
  var frame1_array = [];
  var frame2_array = [];
  //alert(" file1: " + file1_value);
  var sourcefile_array = <?php echo json_encode($sourcefile_array); ?>;

  for (var index in sourcefile_array) {
    if (sourcefile_array[index][0] == file1_value) {
      frame1_array = sourcefile_array[index].slice(1);
      alert("FOUJND");

    }
    if (sourcefile_array[index][0] == file2_value) {
      frame2_array = sourcefile_array[index].slice(1);
    }
  }
  
  var code = frame2_array.join('');

  var css = '<style>pre{counter-reset: line;}code{counter-increment: line;}code:before{content: counter(line); -webkit-user-select: none; display: inline-block; border-right: 1px solid #ddd; padding: 0 .5em; margin-right: .5em;}</style>';  
  //var code = <?php echo $code_string; ?>;
  //code = "orca";
  var html_string = css + '<html><head></head><body><p>' + code + '</p></body></html>';
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
  //alert(GlobalVar.value);
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
  //alert(GlobalVar.value);
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
            <select name='datasetSelect' id = 'datasetSelect' multiple/></select>
            <input type = "submit" name ="clones_button" onClick="javascript:displayClones(); return false" value = "View Clones" id = "clones" />
          </form>
          <form>
            Clone:
            <select name= "cloneSelect" id="cloneSelect" multiple></select> 
            <input type="checkbox" id="files_frame1_checkbox" name="files_checkbox[]" value="files_frame1">Show Files in Frame One</label><br/>
            <input type="checkbox" id="files_frame2_checkbox" name="files_checkbox[]" value="files_frame2">Show Files in Frame Two</label><br/>
            <input type = "submit" name ="files_button" onClick="javascript:displayFiles(); return false" value = "View Files" id = "files" />
          </form>
          <form>
            Frame One: 
            <select name= "file1Select" id="file1Select" multiple></select> 
            Frame Two:
            <select name= "file2Select" id="file2Select" multiple></select> 
            <input type = "submit" name ="analyze_button" onClick="javascript:analyzeClones(); return false" value = "Analyze Clones" id = "clones_for_file" />
          </form>
            <div align="center">
                <iframe id="iframe1" width=80% height=70%></iframe>
               <!-- <iframe id="iframe_two" width=40% height=70%></iframe> -->
            </div>
            <!--frames for adding results above-->
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>