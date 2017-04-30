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
            while ($row = mysqli_fetch_array($history)) {
                print_r($row);
            }
            continue;
        }

        $sql="INSERT INTO Clones (cloneID, datasetID, projectID, ".
            "userID, file, start, end, sim, detector, language) ".
            "VALUES($cloneID, $datasetID, $projectID, '$userID', '$file', ".
            "$st, $end, $sim, 'nicad', '$lang')";

        #if (!mysqli_query($con, $sql)) {
        #    die("Error: " . mysqli_error($con));
        #}
        echo "$sql\n";
        $num_clones++;
    }
}
mysqli_close($con);

$num_classes=`grep "Number" $out_file | awk '{print $4}'`;
echo "There were $num_classes classes of clones.<br>";
echo "Added $num_clones clones.";
echo "\n";

# save files with clone fragments
#echo "<br>./save_frags.sh $detector $out_file $args<br>";
#echo shell_exec("bash save_frags.sh $detector $out_file $args");
?>
