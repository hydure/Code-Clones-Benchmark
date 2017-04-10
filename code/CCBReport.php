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
  start_array = ((datasetID1, $start1, $start2, ...),(datasetID2, $start1, $start2, ...), ..)
  **/
  if ($last_datasetID != $datasetID) {
    array_unshift($dataset_start, $last_datasetID);
    array_push($start_array, $dataset_start);
    array_unshift($dataset_end, $last_datasetID);
    array_push($end_array, $dataset_end);
    $dataset_start = array($start);
    $dataset_end = array($end);
  } else {
     array_push($dataset_start, $start);
     array_push($dataset_end, $end);
  }
  /** handles file creation, where 
  file_array = ((datasetID1, $file1, $file2, ...),(datasetID2, $file1, $file2, ...), ...)
  **/
  if ($last_datasetID != $datasetID) { 
    array_unshift($dataset_files, $last_datasetID);
    array_push($file_array, $dataset_files);
    $dataset_files = array($file);
  } else {
    if (!in_array($file, $dataset_files)) {
      array_push($dataset_files, $file);
    }
  }
  
  $last_datasetID = $datasetID;
  $last_file = $file;
}
//deletes first blank array values and adds the most recent start, end, or file to array
array_unshift($dataset_cloneID, $last_datasetID);
array_push($cloneID_array, $dataset_cloneID);
array_splice($cloneID_array, 0, 1);
array_unshift($dataset_start, $last_datasetID);
array_push($start_array, $dataset_start);
array_unshift($dataset_end, $last_datasetID);
array_push($end_array, $dataset_end);
array_splice($start_array, 0, 1); 
array_splice($end_array, 0, 1); 
array_unshift($dataset_files, $last_datasetID);
array_push($file_array, $dataset_files);
array_splice($file_array, 0, 1);       
$con->close();        
?>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script type="text/javascript">
function displayFile(){
  //alert('shame');
  //step 1: get the DOM object of the iframe.
  var iframe = document.getElementById('iframe1');
//alert('Cannot inject dynamic contents into iframe.');


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
  //$code_array = array('color');
  //array_push($code_array, '</pre>');
  //$code_string = implode("", $code_array);
  $code_string = 'tit';
  $code_string = json_encode($code_string, JSON_HEX_TAG);
  
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
  if (document.getElementById("detector1_checkbox").checked && document.getElementById("detector2_checkbox").checked) { //return both datasets
    dataset_array = <?php 
    $merged_array = array_unique(array_merge($dataset_array_Nicad, $dataset_array_Deckard), SORT_REGULAR);
    sort($merged_array);
    echo json_encode($merged_array); 
    ?>;
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

function displayClonesAndFiles() { 
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
  
  var file_array = <?php  echo json_encode($file_array); ?>;
  for (var index in file_array) { //find range for selected files
    if (file_array[index][0] == value) {
      var selected_file_array = file_array[index].slice(1);
    }
  }
  var file1_selector = document.getElementById('file1Select');
  var file2_selector = document.getElementById('file2Select');
  $("#file1Select").empty();
  $("#file2Select").empty();
  for (var index in selected_file_array) { //displays files in both multiselectors
    var option = document.createElement('option');
    option.innerHTML = selected_file_array[index];
    option.value = selected_file_array[index];
    file1_selector.append(option); 
    var option = document.createElement('option'); //must do this twice or the other append doesn't work
    option.innerHTML = selected_file_array[index];
    option.value = selected_file_array[index];
    file2_selector.append(option);   
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
            <input type = "submit" name ="clones_button" onClick="javascript:displayClonesAndFiles(); return false" value = "View Clones" id = "clones" />
          </form>
          <form>
            Clone:
            <select name= "cloneSelect" id="cloneSelect" multiple></select> 
            Frame One: 
            <select name= "file1Select" id="file1Select" multiple></select> 
            Frame Two:
            <select name= "file2Select" id="file2Select" multiple></select> 
            <input type = "submit" name ="analyze_button" onClick="javascript:displayFile(); return false" value = "Analyze Clones" id = "clones_for_file" />
          </form>
            <div align="center">
                <iframe id="iframe1" width=60% height=70%></iframe>
               <!-- <iframe id="iframe_two" width=40% height=70%></iframe> -->
            </div>
            <!--frames for adding results above-->
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>