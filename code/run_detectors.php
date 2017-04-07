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
        foreach ($_POST['n_language'] as $lang) {
            if ($lang != "") break;
        }
        
        # connect to database
        con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
        if(!$con) {
                die('could not connect: ' . mysqli_connect_error());
        }
 # check if dataset has been examined already
        $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
            "datasetID=".$_POST['datasetSelect']." AND detector='nicad'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            exit;
        } else {
            echo "blah<br>";
            exit;
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
	#update tags in Datasets
	$dateAR = getdate();
	$date = $dateAR['mon']."/".$dateAR['mday']."/".$dateAR['year'];
	echo "The date is $date";
	$sql = "UPDATE Datasets SET Nicad_flag=1, submit_date='$date' WHERE datasetID=".$_POST['datasetSelect'];
	if(!$con->query($sql))
		echo "failed to update dataset info";
	

        # run nicad
        $nicad_path="/home/clone/nicad.sh";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' | grep -v 'known hosts'";
        $nicad_raw = shell_exec($cmd);
        echo "$nicad_raw<br>";

        $file = "/home/pi/MyNAS/nicad/".$_POST['datasetSelect'].".html";
        file_put_contents($file, $nicad_raw);
        shell_exec("chmod 0600 $file");

        # get clones
        $clones=`./parse.sh $file`;
        $clones=explode(" ", $clones);

        # add clones to database
        $num_clones=0;
        for($i=0; $i<count($clones)-1; ) {
            $num_frags=$clones[$i++]; 
            $sim=$clones[$i++];

            $query = mysqli_query($con, 
                "SELECT * FROM Clones ORDER BY cloneID DESC limit 1");
            $cloneID=mysqli_fetch_assoc($query)['cloneID'] + 1;

            echo "num_frags: $num_frags<br>";
            for ($j=0; $j<$num_frags; $j++) {
                $datasetID=$clones[$i++];
                $projectID=$clones[$i++];
                $file=$clones[$i++];
                $st=$clones[$i++];
                $end=$clones[$i++];

                $sql="INSERT INTO Clones (cloneID, datasetID, projectID, ".
                    "file, start, end, sim, detector) ".
                    "VALUES($cloneID, $datasetID, $projectID, '$file', $st, 
                    $end, $sim, 'nicad')";
                echo "$sql<br>";;

                if (!mysqli_query($con, $sql)) {
                    die("Error: " . mysqli_error($con));
                }
                $num_clones++;
            }
        }

        $num_classes=`grep "Number" $file | awk '{print $4}'`;
        echo "There were $num_classes classes of clones.<br>";
        echo "Added $num_clones clones pairs.";

        mysqli_close($con);
    }
}


} else {
    echo "No code clone detectors were selected.<br>";
}
?>

</body>
</html>
