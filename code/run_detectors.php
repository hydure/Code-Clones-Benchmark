<html>
<body>

<?php
session_start();
if (!empty($_POST['detector'])) {


echo "You chose to run < "; 
foreach($_POST['detector'] as $detector) {
    echo "$detector ";
}

echo "> on dataset ".$_POST['datasetSelect']."<br>";

foreach($_POST['detector'] as $detector) {
    $userID = intval($_SESSION['userSession']);

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

    #update tags in Datasets
    date_default_timezone_set('America/New_York');
    $date = date('Y-m-d H:i:s');
    $sql = "UPDATE Datasets SET Nicad_flag=1, submit_date='$date' WHERE datasetID=".$_POST['datasetSelect'];
    if(!$con->query($sql))
        echo "failed to update dataset info";

        /***************************
        *                          *
        *          NICAD           *
        *                          *
        ***************************/
    if ($detector == "nicad") {
        if (empty($_POST['n_language'])) {
            echo "Please select a language.<br>";
            break;
        }

        # get language
        foreach ($_POST['n_language'] as $lang) {
            if ($lang != "") break;
        }
        
        # check if dataset has been examined already
        $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
            "datasetID=".$_POST['datasetSelect']." AND detector='nicad'".
            " AND language='$lang'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            break;
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

        # run nicad
        $nicad_path="/home/clone/nicad.sh";
        #$file = "/home/pi/MyNAS/nicad/".$_POST['datasetSelect'].".html";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' >$file 2>/dev/null &";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' | grep -v 'known hosts'";
        $nicad_raw = shell_exec($cmd);
        echo "$nicad_raw<br>";

        # write nicad output to file
        $file = "/home/pi/MyNAS/nicad/".$_POST['datasetSelect'].".html";
        file_put_contents($file, $nicad_raw);

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

            for ($j=0; $j<$num_frags; $j++) {
                $datasetID=$clones[$i++];
                $projectID=$clones[$i++];
                $file=$clones[$i++];
                $st=$clones[$i++];
                $end=$clones[$i++];

                $sql="INSERT INTO Clones (cloneID, datasetID, projectID, ".
                    "userID, file, start, end, sim, detector, language) ".
                    "VALUES($cloneID, $datasetID, $projectID, '$userID', '$file', 
                    $st, $end, $sim, 'nicad', '$lang')";

                if (!mysqli_query($con, $sql)) {
                    die("Error: " . mysqli_error($con));
                }
                $num_clones++;
            }
        }

        $num_classes=`grep "Number" $file | awk '{print $4}'`;
        echo "There were $num_classes classes of clones.<br>";
        echo "Added $num_clones clones pairs.";

        /***************************
        *                          *
        *          DECKARD         *
        *                          *
        ***************************/
    } else if ($detector == 'deckard') {
        if (empty($_POST['d_language'])) {
            echo "Please select a language.<br>";
            break;
        }

        # get language
        foreach ($_POST['d_language'] as $lang) {
            if ($lang != "") break;
        }
        
    # check if dataset has been examined already
    $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
        "datasetID=".$_POST['datasetSelect']." AND detector='nicad'".
        " AND language='$lang'");
    if ($history->num_rows > 0) {
        echo "You ran this dataset already!<br>";
        exit;
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

        # run deckard
        $deckard_path="/home/clone/deckard.sh";
        #$file = "/home/pi/MyNAS/nicad/".$_POST['datasetSelect'].".html";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' >$file 2>/dev/null &";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$deckard_path $args' | grep -v 'known hosts'";
        $deckard_raw = shell_exec($cmd);
        echo "$deckard_raw<br>";

        if (preg_match('/^Error: there are no/', $deckard_raw)) {
            break;
        }

        # write deckard output to file
        $file = "/home/pi/MyNAS/deckard/".$_POST['datasetSelect'].".html";
        file_put_contents($file, $deckard_raw);

#        # get clones
#        $clones=`./parse.sh $file`;
#        $clones=explode(" ", $clones);
#
#        # add clones to database
#        $num_clones=0;
#        for($i=0; $i<count($clones)-1; ) {
#            $num_frags=$clones[$i++]; 
#            $sim=$clones[$i++];
#
#            $query = mysqli_query($con, 
#                "SELECT * FROM Clones ORDER BY cloneID DESC limit 1");
#            $cloneID=mysqli_fetch_assoc($query)['cloneID'] + 1;
#
#            for ($j=0; $j<$num_frags; $j++) {
#                $datasetID=$clones[$i++];
#                $projectID=$clones[$i++];
#                $file=$clones[$i++];
#                $st=$clones[$i++];
#                $end=$clones[$i++];
#
#                $sql="INSERT INTO Clones (cloneID, datasetID, projectID, ".
#                    "file, start, end, sim, detector, language) ".
#                    "VALUES($cloneID, $datasetID, $projectID, '$file', $st, 
#                    $end, $sim, 'deckard', '$lang')";
#
#                if (!mysqli_query($con, $sql)) {
#                    die("Error: " . mysqli_error($con));
#                }
#                $num_clones++;
#            }
#        }
#
#        $num_classes=`grep "Number" $file | awk '{print $4}'`;
#        echo "There were $num_classes classes of clones.<br>";
#        echo "Added $num_clones clones pairs.";

    }
    mysqli_close($con);
}


} else {
    echo "No code clone detectors were selected.<br>";
}

echo '<p>Click <a href="CCBTools.php">here</a> to go back</p>';
?>

</body>
</html>
