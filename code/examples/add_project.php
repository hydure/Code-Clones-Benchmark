<html>
<body>

<?php
if (!empty($_POST[url]) && !empty($_POST[commit])) {
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

# get title from url
$title = exec("echo $_POST[url] | sed 's:.*\.com/[^/]*/::'");

# get repo host username
$user = exec("echo $_POST[url] | sed 's:.*\.com/\([^/]*\)/:\1:'");

# get head commit number
if ("$_POST[commit]" == "head") {
	$commit = exec("./get_head_commit.sh $_POST[url]");
} else {
	$commit = $_POST[commit];
}

# get date
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

# set ownership
if ("$_POST[private]" == "1") {
	$ownership = -1;
} else {
	$ownership = -1;
}

# add entry to Projects table
$sql="INSERT INTO Projects (title, url, commit, uploaded, ownership, user)
        VALUES('$title', '$_POST[url]', '$commit', '$date', $ownership, '$user')";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
echo "1 project added.";

mysqli_close($con);
} else {
echo "Must enter URL and commit number.";
}
?>
</body>
</html>
