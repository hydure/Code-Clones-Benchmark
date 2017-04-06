<html>
<body>

<?php
if (!empty($_POST['detector'])) {


echo "You chose to run < "; 
foreach($_POST['detector'] as $detector) {
    echo "$detector ";
}

echo "> on dataset ".$_POST['datasetSelect']."<br>";

foreach($_POST['detector'] as $detector) {
    if ($detector == "Nicad") {
        # get language
        foreach ($_POST['language'] as $lang) {
            if ($lang != "") break;
        }
        
        # connect to database
        $con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
        if(!$con) {
                die('could not connect: ' . mysqli_connect_error());
        }

        # obtain projectIDs from selected dataset
        $sql="SELECT projectID FROM Datasets WHERE datasetID=".$_POST['datasetSelect'];
        $pIDs = mysqli_query($con, $sql);
        if (!$pIDs) {
            die("Error: " . mysqli_error($con));
        }

        # build arguments: language projectID1 url1 ...
        $args="$lang ".$_POST['datasetSelect'];
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

        # run nicad
        $nicad_path="/home/clone/nicad.sh";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' | grep -v 'known hosts'";
        echo shell_exec($cmd);
    }
}


} else {
    echo "No code clone detectors were selected.<br>";
}
?>

</body>
</html>
