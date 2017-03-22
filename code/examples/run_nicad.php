<html>
<body>

<?php
$cmd="/home/pi/Code-Clones-Benchmark/code/examples/run_nicad.sh $_POST[python]";
$nicad_raw = shell_exec("$cmd");
#echo $nicad_raw;

$datasetID = 1;
$file = "/home/pi/MyNAS/".$datasetID."_nicad.html";
file_put_contents($file, $nicad_raw);
?>

</body>
</html>
