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

# connect to database
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}
    
# set userID and datasetID
$userID = intval($_SESSION['userSession']);
$datasetID=$_POST['datasetSelect'];

# obtain projectIDs from selected dataset
$sql="SELECT projectID FROM Datasets WHERE datasetID=".$datasetID;
$pIDs = mysqli_query($con, $sql);
if (!$pIDs) {
    die("Error: " . mysqli_error($con));
}

foreach($_POST['detector'] as $detector) {

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
            "datasetID=".$datasetID." AND detector='nicad'".
            " AND language='$lang'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            break;
        }
       
        # build arguments: language projectID1 url1 ...
        $args="$lang ".$datasetID;
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
        #$file = "/home/pi/MyNAS/nicad/".$datasetID.".html";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' >$file 2>/dev/null &";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' | grep -v 'known hosts'";
        $nicad_raw = shell_exec($cmd);
        #echo "$nicad_raw<br>";
        
        #update tags in Datasets
        date_default_timezone_set('America/New_York');
        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE Datasets SET Nicad_flag=1, submit_date='$date' ".
               "WHERE datasetID=".$datasetID;
        if(!$con->query($sql))
            echo "failed to update dataset info";

        # write nicad output to file
        $file = "/home/pi/MyNAS/nicad/".$datasetID.".html";
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

                # check if clone has been added already
                $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
                    "projectID=$projectID AND detector='nicad' AND ".
                    "language='$lang' AND file='$file' AND start='$st' ".
                    "AND end=$end");
                if ($history->num_rows > 0) {
                    continue;
                }
       
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
        echo "Added $num_clones clones.";

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
            "datasetID=".$datasetID." AND detector='deckard'".
            " AND language='$lang'");
        if ($history->num_rows > 0) {
            echo "You ran this dataset already!<br>";
            exit;
        }

        # build arguments: language projectID1 url1 ...
        $args="$lang ".$datasetID;
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
        #$file = "/home/pi/MyNAS/nicad/".$datasetID.".html";
        #$cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$nicad_path $args' >$file 2>/dev/null &";
        $cmd="ssh -o StrictHostKeyChecking=no clone@45.33.96.10 '$deckard_path $args' | grep -v 'known hosts'";
        $deckard_raw = shell_exec($cmd);
        #echo "$deckard_raw<br>";

        #update tags in Datasets
        date_default_timezone_set('America/New_York');
        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE Datasets SET Deckard_flag=1, submit_date='$date' ".
               "WHERE datasetID=".$datasetID;
        if(!$con->query($sql))
            echo "failed to update dataset info";

        if (preg_match('/^Error: there are no/', $deckard_raw)) {
            echo "$deckard_raw<br>";
            break;
        }

        # write deckard output to file
        $file = "/home/pi/MyNAS/deckard/".$datasetID.".html";
        file_put_contents($file, $deckard_raw);

        # get cloneID
        $query = mysqli_query($con, 
            "SELECT * FROM Clones ORDER BY cloneID DESC limit 1");
        $cloneID=mysqli_fetch_assoc($query)['cloneID'] + 1;
        
        if (!($handle = fopen($file, "r"))) {
            echo "Error: cannot open file '$file'<br>";
            break;
        }

        $num_classes=0;
        $num_clones=0;
        $prev="\n";
        while (($line = fgets($handle)) != false) {
            if ($line != "\n") {
                $parsed = explode(" ", $line);
                $tmp = $parsed[1];
                $file=preg_replace(":src/[0-9]*/:", "", $tmp);
                $projectID=preg_replace(":src/:", "", $tmp);
                $projectID=preg_replace(":/.*:", "", $projectID);
                $index = explode(":", substr($parsed[2], 4));
                $start = $index[1];
                $end = intval(($index[2])) + intval($start) - 1;

                # check if clone has been added already
                $history = mysqli_query($con, "SELECT cloneID FROM Clones WHERE ".
                    "projectID=$projectID AND detector='deckard' AND ".
                    "language='$lang' AND file='$file' AND start='$start' ".
                    "AND end=$end");
                if ($history->num_rows > 0) {
                    continue;
                } 

                $sql = "INSERT INTO Clones (cloneID, datasetID, projectID, ".
                        "userID, file, start, end, detector, language) ".
                        "VALUES ( '{$cloneID}', '{$datasetID}', ".
                        "'{$projectID}', '{$userID}','{$file}', ".
                        "'{$start}', '{$end}', 'deckard', '$lang')";

                if (!mysqli_query($con, $sql)) {
                    die("Error: " . mysqli_error($con));
                }
                $num_clones++;
            } else if ($line != $prev) { 
                $cloneID += 1;
                $num_classes++;
            }
            $prev=$line;
        }
        fclose($handle);

        echo "There were $num_classes classes of clones.<br>";
        echo "Added $num_clones clones.";
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
