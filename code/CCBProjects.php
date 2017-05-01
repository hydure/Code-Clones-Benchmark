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
          <h1>Code Cloning Projects</h1>
          <br />
          <form action="scripts/add_project.php" method="post">
            URL:<br>
            <input type="text" name="url">
            <br>
	          Commit:<br>
            <input type="text" name="commit" value="head">
            <br>
            Private:
            <input type="checkbox" class="cb1" onchange="cb1Change(this)" name="ownership_type" value="1" checked>
            Public:
            <input type="checkbox" class="cb1" onchange="cb1Change(this)" name="ownership_type" value="2">
             <br>
	          <br>        
	          <input type="submit" value="Upload" class="buttonA pill"/>
          </form>
          <br />
          <form id="project_button" action="scripts/upload_project.php" method="post" enctype="multipart/form-data">
            Private:
            <input type="checkbox" class="cb2" onchange="cb2Change(this)" name="ownership_type" value="1" checked>
            Public:
            <input type="checkbox" class="cb2" onchange="cb2Change(this)" name="ownership_type" value="2">
             <br>
            <input type = "file" name = "uploaded_file"/><br />
            <input type = "button" value = "Upload"  id="submit_project" onclick="submitFunc()" class="buttonA pill"/>
          </form>
          <form id='evaluate_button' action='CCBModProjects.php' method='post' enctype='multipart/form-data'>
            <p align='center-block' style='font-size: 160%''>Browse Projects</p>
            <?php
            $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT projectID, title, userId FROM Projects");

            echo "<select name='projectSelect' id = 'projectSelect' >" ;

            while ($row = $result->fetch_assoc()) {

                  unset($projectID, $title, $userId);
                  $projectID = $row['projectID'];
                  $title = $row['title'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId) {
                    echo '<option value='.$projectID.'>'.$title.'</option>';
                  }
                 
            }
            echo "</select>";
            $con->close();
            ?>
            <input type = 'submit' name='project_action' value = 'Switch Ownership'  id='switch_ownership' class="buttonA loop" />
            <input type = 'submit' name='project_action' value = 'Delete Project' id='delete_project' class="buttonA danger trash"/>
          </form>

          <?php
          $con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
          if(mysqli_connect_errno()) {
              die("MySQL connection failed: ". mysqli_connect_error());
          }
          $result = $con->query("SELECT projectID, title, commit, last_accessed, uploaded, ownership, url, size, userId, author FROM Projects");
          echo "<div class = 'wrapper'>";
          echo "<div class='table'>";
          echo "<div class='row_special header'>";
          echo "<div class='cell'>ID</div>";
          echo "<div class='cell'>Project</div>";
          echo "<div class='cell'>Owner</div>";
          echo "<div class='cell'>Commit</div>";
          echo "<div class='cell'>Accessed</div>";
          echo "<div class='cell'>Uploaded</div>";
          echo "<div class='cell'>URL</div>";
          echo "<div class='cell'>Size(bytes)</div>";
          echo "<div class='cell'>Ownership</div>";
          echo "</div>";
          while ($row = $result->fetch_assoc()) {

                  unset($projectID, $title, $userId);
                  $projectID = $row['projectID'];
                  $title = $row['title'];
		              $author = $row['author'];
                  $commit = $row['commit'];
                  $last_accessed = $row['last_accessed'];
                  $uploaded = $row['uploaded'];
                  $ownership = $row['ownership'];
                  $url = $row['url'];
                  $size = $row['size'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId || $ownership == -1) {
                    echo "<div class='row_special'>";
                    echo "<div class='cell'>".$projectID.'</div>';
                    echo "<div class='cell'>".$title.'</div>';
                    echo "<div class='cell'>".$author.'</div>';
                    echo "<div class='cell'>".$commit.'</div>';
                    echo "<div class='cell'>".$last_accessed.'</div>';
                    echo "<div class='cell'>".$uploaded.'</div>';
                    echo "<div class='cell'>".$url.'</div>';
                    echo "<div class='cell'>".$size.'</div>';
                    
                    if (intval($ownership) != -1) { 
                      echo "<div class='cell'>Private</div>";
                    }
                    else {
                      echo "<div class='cell'>Public</div>";
                    }
                    echo "</div>";
                  }
          }    
          echo "</div>";
          echo "</div>";
          //echo "HEEEEEEEEEEEEEEEEERE" . ($_SESSION['userName']);
          $con->close();
          ?>
        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->
</html>
