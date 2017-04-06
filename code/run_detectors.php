<html>
<body>

<?php
if (!empty($_POST['detector'])) {
    echo "You chose to run < "; 
    foreach($_POST['detector'] as $detector) {
        echo "$detector ";
    }

    echo "> on dataset ".$_POST['datasetSelect']."<br>";

    $con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
    if(!$con) {
            die('could not connect: ' . mysqli_connect_error());
    }

    $sql="SELECT projectID FROM Datasets WHERE datasetID=".$_POST['datasetSelect'];
    $pIDs = mysqli_query($con, $sql);
    if (!$pIDs) {
        die("Error: " . mysqli_error($con));
    }

    $args=$_POST['datasetSelect'];
    while($row = mysqli_fetch_array($pIDs)) {
        $pURL = mysqli_query($con, "SELECT url FROM Projects WHERE projectID=".$row['projectID']);
        if (!$pURL) {
            die("Error: " . mysqli_error($con));
        }

        $args="$args ".$row['projectID'];
        $args="$args ".mysqli_fetch_array($pURL)['url'];
    }
    echo $args;

    mysqli_close($con);
} else {
    echo "No code clone detectors were selected.<br>";
}
?>

</body>
</html>
