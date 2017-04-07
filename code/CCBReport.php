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
    //unset($projectID, $submit_date, $status);
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
      //echo $line;
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
  //most of the browser supports .document. Some supports (such as the NetScape series) .contentDocumet, while some (e.g. IE5/6) supports .contentWindow.document
  //we try to read whatever that exists.
  var iframedoc = iframe.document;
    if (iframe.contentDocument)
      iframedoc = iframe.contentDocument;
    else if (iframe.contentWindow)
      iframedoc = iframe.contentWindow.document;

   if (iframedoc) {
     // Put the content in the iframe
     iframedoc.open();
     iframedoc.writeln(html_string);
     iframedoc.close();
   } else {
    //just in case of browsers that don't support the above 3 properties.
    //fortunately we don't come across such case so far.
    alert('Cannot inject dynamic contents into iframe.');
   }


}
/**
**/
function generateDatasets() {
  //alert("the cooks");
  if (document.getElementById('Deckard_checkbox').checked) {
    var detector = 'Deckard';
  } 
  if (document.getElementById('Nicad_checkbox').checked) {
    var detector = 'Nicad';
  }
  createCookie('detector', 'Nicad', 7);

  
  
  <?php

  //$detector = $_COOKIE["detector"]; 
  $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
  if(mysqli_connect_errno()) {
      die("MySQL connection failed: ". mysqli_connect_error());
  }
  $userId = $_SESSION['userSession']; /**
  if ($detector == 'Nicad') {
    $sql = "SELECT datasetID FROM Datasets WHERE userId = '$userId' AND Nicad_flag = 1";
  } **/
  $sql = "SELECT datasetID FROM Datasets WHERE userId = '$userId' AND Nicad_flag = 1";
  //if ($detector == 'Deckard') {
    //$sql = "SELECT datasetID FROM Datasets WHERE userId = '$userId' AND Deckard_flag = 1";
  //}
  $result = $con->query($sql);
  unset($datasetID_array);
  $datasetID_array = array();
  while ($row = $result->fetch_assoc()) {
      unset($datasetID);
      $datasetID = $row['datasetID'];
      if (!in_array($datasetID, $datasetID_array)) {
        array_push($datasetID_array, $datasetID);
      }
  }

  $con->close(); 
  ?>
  var datasetID_array = <?php echo json_encode($datasetID_array); ?>;
  var select = document.getElementById('dataset_selector');

  for (var prop in datasetID_array) {
    var opt = document.createElement('option');
    opt.innerHTML = datasetID_array[prop];
    opt.value = datasetID_array[prop];
    select.append(opt);
  }
  //eraseCookie('detector');

}

function createCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}


$(document).ready(function() {


  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
});
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

            <!--frames for adding results. each iframe should contain one set-->
            <!--add inside of quotes after iframe src=" "-->

            
            <button id = "iframe_button" onClick="javascript:injectHTML();">Inject HTML</button>

            <form action="#">
              <p align="center-block">Choose a Clone Detector:</p>
              <label><input type="checkbox" name="detector[]" id="Nicad_checkbox" value="Nicad">Nicad</label><br />
              <label><input type="checkbox" name="detector[]" id="CCFinderX_checkbox" value="CCFinderX">CCFinderX</label><br />
              <label><input type="checkbox" name="detector[]" id="Deckard_checkbox" value="Deckard">Deckard</label><br />
            <button id="detector_button" onClick="javascript:generateDatasets();">Generate Datasets</button> 
            </form>

            <select id="dataset_selector" name="DS" multiple></select>
            <select id="clone_selector" multiple></select>

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