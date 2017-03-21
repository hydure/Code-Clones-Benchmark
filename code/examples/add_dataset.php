<html>
<body>

<?php
if (!empty($_POST['row'])) {
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}

$i=1;
foreach($_POST['row'] as $row) {
	$sql="INSERT INTO Datasets (project".$i."ID) VALUES($row)";
}

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
echo "Dataset Successfully added.";

mysqli_close($con);
} else {
echo "No projects were selected.";
}
?>

</body>
</html>
