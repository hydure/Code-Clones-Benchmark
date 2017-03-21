<html>
<body>

<?php
$cmd="/home/pi/Code-Clones-Benchmark/code/examples/run_nicad.sh $_POST[python]";
echo shell_exec("$cmd");
?>

</body>
</html>
