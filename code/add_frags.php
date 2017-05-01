<?php
$detector = $argv[1];
$lang = $argv[2];
$userID = $argv[3];
$out_file = $argv[4];

# connect to database
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('could not connect: ' . mysqli_connect_error());
}
    
if ($detector == 'nicad') {
    # get clones
    $clones=`./parse.sh $out_file`;
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

            # nicad appends .pyindent, .ifdefed
            $file=preg_replace("/\.pyindent/", "", $file);
            $file=preg_replace("/\.ifdefed/", "", $file);

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
                "VALUES($cloneID, $datasetID, $projectID, '$userID', '$file', ".
                "$st, $end, $sim, 'nicad', '$lang')";

            if (!mysqli_query($con, $sql)) {
                die("Error: " . mysqli_error($con));
            }
            $num_clones++;
        }
    }
} elseif ($detector == 'deckard') {
    $err = shell_exec("grep 'Error: there are no' $out_file");
    if (empty($err)) {
        # get datasetID
        preg_match(":/([0-9]*)_out:", $out_file, $matches, PREG_OFFSET_CAPTURE);
        $datasetID = $matches[1][0];

        # get cloneID
        $query = mysqli_query($con, 
            "SELECT * FROM Clones ORDER BY cloneID DESC limit 1");
        $cloneID=mysqli_fetch_assoc($query)['cloneID'] + 1;
        
        if (!($handle = fopen($out_file, "r"))) {
            echo "Error: cannot open file '$file'<br>";
        } else {
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
        }
    }
}
mysqli_close($con);

#$num_classes=`grep "Number" $out_file | awk '{print $4}'`;
#echo "There were $num_classes classes of clones.<br>";
#echo "Added $num_clones clones.";

# save files with clone fragments
#echo "<br>./save_frags.sh $detector $out_file $args<br>";
#echo shell_exec("bash save_frags.sh $detector $out_file $args");
?>
