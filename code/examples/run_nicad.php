<html>
<body>

<?php
$cmd="/home/pi/Code-Clones-Benchmark/code/examples/script.sh $_POST[username] $_POST[password] $_POST[python]";
echo shell_exec("$cmd");
?>

</body>
</html>
