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

/**
if(isset($_POST['select_dataset_action']))
{
	$id = $_POST['datasetSelect'];
	echo "dataset is $id";
	header("refresh:2;../ccb/CCBReport.php?data=$id");
}
 **/
?>


<html>
<head></head>
<body>

<form action = "../ccb/CCBReport.php" id='deleteDataset' method='post' enctype='multipart/form-data'>
            <p align='center-block' style='font-size: 160%''>Manage Datasets</p>
            <?php
            #$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
		        $con = new mysqli('localhost', 'root', '*XMmysq$', 'cc_bench');
            if(mysqli_connect_errno()) {
                die("MySQL connection failed: ". mysqli_connect_error());
            }
            $result = $con->query("SELECT datasetID, userId FROM Datasets");

            echo "<html>";
            echo "<body>";
            echo "<select name='datasetSelect' id = 'datasetSelect' >" ;
            $dataset_dropdown = array();
            while ($row = $result->fetch_assoc()) {

                  unset($datasetID, $userId);
                  $datasetID = $row['datasetID'];
                  $userId = $row['userId'];
                  if ($_SESSION['userSession'] == $userId && !in_array($datasetID, $dataset_dropdown)) {
                    echo '<option value='.$datasetID.'>'.$datasetID.'</option>';
                    array_push($dataset_dropdown, $datasetID);
                  }
            }
            echo "</select>";
            echo "</body>";
            echo "</html>";
            $con->close();
            ?>
            <input type = 'submit' name= 'select_dataset_action' value = 'Select Dataset'  id='select_dataset_action' />
          </form>



</body>
</html>







