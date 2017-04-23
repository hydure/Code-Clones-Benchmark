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

$dataset_array = array();
$rows_array = array();
$line_array = array();

while ($row = $result->fetch_assoc()) { //store all possible relevant data into their correct arrays
  unset($datasetID, $userID, $file, $start, $end, $detector, $language);
  $cloneID = $row['cloneID'];
  $datasetID = $row['datasetID'];
  $userID = $row['userID'];
  $detector = $row['detector'];
  $language = $row['language'];
  $sim = "sim";
  $file = $row['file'];
  $start = $row['start'];
  $end = $row['end'];

  if (!in_array($datasetID, $dataset_array)) {
    array_push($dataset_array, $datasetID);
  }
  $range = $start . '-' . $end;
  array_push($line_array, $datasetID);
  array_push($line_array, $cloneID);
  array_push($line_array, $file);
  array_push($line_array, $range);
  array_push($line_array, $sim);
  array_push($line_array, $detector);
  array_push($line_array, $language);
  array_push($rows_array, $line_array); 

}
    
$con->close();


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

function loadTable() {
  var dataset_array = <?php echo json_encode($dataset_array); ?>;
  var dataset_selector = document.getElementById('datasetSelect');
  var value = dataset_selector[dataset_selector.selectedIndex].value;
  for (i = 0; i < dataset_array.length; i++) {
    var row_special_selector = document.getElementById("row_special" + i);
    row_special_selector.style.display="";
  }
  var rows_array = <?php echo json_encode($rows_array); ?>;
  for (var index in rows_array) { //find range for selected files
    if (rows_array[index][0] == value) {
      var selected_row_array = rows_array[index].slice(1);
      alert('f');
    }
  }
}

window.onload = function () {
  var dataset_selector = document.getElementById('datasetSelect');
  var dataset_array = <?php echo json_encode($dataset_array); ?>;
  for (var index in dataset_array) { //displays clones
    var option = document.createElement('option');
    option.innerHTML = dataset_array[index];
    option.value = dataset_array[index];
    dataset_selector.append(option);    
  }
}

document.addEventListener('DOMContentLoaded', function() { //hides all rows upon loading
  var dataset_array = <?php echo json_encode($dataset_array); ?>;
  for (i = 0; i < dataset_array.length; i++) {
    var row_special_selector = document.getElementById("row_special" + i);
    row_special_selector.style.display="none";
  }
});

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

    .wrapper {
      max-width: 1200px;
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
        <h1>Code Cloning Evaluations</h1>
          <br />

        <form>
          Dataset Select:
          <select name="datasetSelect" id="datasetSelect" multiple></select>
          <input type="submit" name="select_button" onClick="javascript:loadTable(); return false" value="Select" id="select_button" />
        </form>

        <div class = 'wrapper' >
        <div class='table'>
        <div class='row_special header blue'>
        <div class='cell'>Clone ID</div>
        <div class='cell'>File</div>
        <div class='cell'>Line Range</div>
        <div class='cell'>Similarity</div>
        <div class='cell'>Detector</div>
        <div class='cell'>Language</div>
        </div>
        <?php
        for ($i = 0; $i < count($dataset_array); $i++) {
          echo "<div class='row_special' id='row_special".$i."' >";
          for ($j = 0; $j < 6; $j++) {
            echo "<div class='cell'><div id='".$i."row".$j."'></div></div>";
          }
          echo "</div>";
        }
        ?>

        </div>
        </div>


              
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->

</div><!--/.page-container-->

<div>

</html>
