<html>
<body>

<?php
session_start();
if (!empty($_POST['row'])) {
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}

$sql = "SELECT * FROM Datasets ORDER BY datasetID DESC limit 1;";
$query = mysqli_query($con, $sql);
$datasetID = mysqli_fetch_assoc($query)['datasetID'] + 1;
$userId = intval($_SESSION['userSession']);
$submit_date = 'Never';

date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');
if ("$_POST[ownership_type]" == "1") {
            $ownership = 0;
        } else {
            $ownership = -1;
        }
foreach($_POST['row'] as $row) {
    $sql="INSERT INTO Datasets (datasetID, projectID, userId, submit_date, ".
         "status, ownership) VALUES ($datasetID, $row, $userId, '$submit_date', FALSE, '$ownership')";
    $date_sql = "UPDATE Projects SET last_accessed='$date' WHERE projectID=".$row;
    if(!$con->query($date_sql))
        echo "failed to update dataset info<br>";

	if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
	}
}


echo "Dataset Successfully added.";

mysqli_close($con);
} else {
echo "No projects were selected.";
}

echo '<p>Click <a href="CCBDatasets.php">here</a> to go back</p>';
?>
</body>
</html>
