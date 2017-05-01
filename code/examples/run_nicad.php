<html>
<body>

<?php
$cmd="ssh -o StrictHostKeyChecking=no user@domain 'path/to/nicad.sh ".$_POST[python]."' | grep -v 'known hosts'"
$nicad_raw = shell_exec("$cmd");
echo $nicad_raw;

$datasetID = 1;
$file = "/path/to/storage/".$datasetID."_nicad.html";
file_put_contents($file, $nicad_raw);

$pairs=`/path/to/parse.sh $file`;
$pairs=explode(" ",$pairs);

$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

$num_clones=0;
for($i=0; $i<count($pairs)-1; ) {
    $file1=$pairs[$i++];
    $st1=$pairs[$i++];
    $end1=$pairs[$i++];

    $file2=$pairs[$i++];
    $st2=$pairs[$i++];
    $end2=$pairs[$i++];

    $sim=$pairs[$i++];

    $sql="INSERT INTO Clones (datasetID, file1, start1, end1, file2, start2, end2, sim)
            VALUES($datasetID, '$file1', $st1, $end1, '$file2', $st2, $end2, $sim)";

    if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
    }
    $num_clones++;
}

$num_classes=`grep "Number" $file | awk '{print $4}'`;
echo "There were $num_classes classes of clones.<br>";
echo "Added $num_clones clones pairs.";

mysqli_close($con);
?>
<br>
<a href="view_clones.php">view_clones</a>
</body>
</html>
