<?php

$con = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(mysqli_connect_errno()) {
    die("MySQL connection failed: ". mysqli_connect_error());
}

$sql = "SELECT * FROM Clones ORDER BY cloneID DESC limit 1;";
$query = mysqli_query($con, $sql);
$cloneID = mysqli_fetch_assoc($query)['cloneID'] + 1;
$datasetID = 1;
$detector = 'Deckard';
$file = "/home/reid/Code-Clones-Benchmark/artifacts/DeckardTesting/clusters/post_cluster_vdb_30_0_allg_0.95_30"
$projectID = 123;
$userID = $_SESSION['userSession'];

$handle = fopen($file, "r");
if ($handle) {
	while (($line = fgets($handle)) != false) {
		if ($line != "\n") {
			$parsed = explode(" ", $line);
			$file = $parsed[1];
			$index = explode(":", substr($parsed[2], 4));
			$start = $index[1];
			$end = intval(($index[2])) + intval($start);

			$sql = "INSERT INTO Clones (cloneID, datasetID, projectID, userID, file, start, end, detector )
			VALUES ( '{$cloneID}', '{$datasetID}', '{$projectID}', '{$userID}','{$file}', '{$start}', '{$end}', '{$detector}')";
			if (!mysqli_query($con, $sql)) {
        		die("Error: " . mysqli_error($con));
			}
		} else { 
			$cloneID += 1;
		}
	}

	fclose($handle);
}
mysqli_close($con);
?>