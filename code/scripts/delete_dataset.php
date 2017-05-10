<?php
$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(mysqli_connect_errno()) {
	die("MySQL connection failed: ". mysqli_connect_error());
}
$dataset_val = $_POST['datasetSelect'];
$sql = "DELETE FROM Datasets WHERE datasetID='".$dataset_val."'";
if ($con->query($sql) == TRUE) {
	echo "Dataset deleted sucessfully";

} else {
	echo "Error Deleting Dataset: " . $con->error;
}
$sql = "DELETE FROM Clones WHERE datasetID='".$dataset_val."'";
if ($con->query($sql) == TRUE) {
	echo "Dataset deleted sucessfully";

} else {
	echo "Error Deleting Dataset: " . $con->error;
}
$sql = "DELETE FROM deckard_DB WHERE datasetID='".$dataset_val."'";
if ($con->query($sql) == TRUE) {
	echo "Dataset deleted sucessfully";

} else {
	echo "Error Deleting Dataset: " . $con->error;
}
$con->close();
header('Location:../CCBDatasets.php');
?>
