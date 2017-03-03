<html>
<body>

<?php
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

$title = exec("echo $_POST[url] | sed 's:.*\.com/[^/]*/::'");

date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

if ("$_POST[private]" == "1") {
	$ownership = 0;
} else {
	$ownership = 0;
}

$sql="INSERT INTO Projects (title, url, commit, uploaded, ownership)
        VALUES('$title', '$_POST[url]', '$_POST[commit]', '$date', $ownership)";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
echo "1 project added.";

mysqli_close($con)
?>
</body>
</html>
