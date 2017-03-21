<?php
session_start();
?>

<html>
<body>

<?php
error_reporting(0);
if (!empty($_POST[url]) && !empty($_POST[commit])) {
$con = mysqli_connect('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

# get title from url
$title = exec("echo $_POST[url] | sed 's:.*\.com/[^/]*/::'");


# get repo host username
$user = exec("echo $_POST[url] | sed 's:.*\.com/\([^/]*\)/:\1:'");

# get head commit number
if ("$_POST[commit]" == "head") {
	$commit = exec("/home/pi/Code-Clones-Benchmark/code/examples/get_head_commit.sh $_POST[url]");
} else {
	$commit = $_POST[commit];
}

# get date
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

#get userId
$uid = $_SESSION['userSession'];
# set ownership
if ("$_POST[private]" == "1") {
	$ownership = -1;
} else {
	$ownership = (int) $uid;
}
if($stmt = mysqli_prepare($con, "Select commit FROM Projects where title=? AND ownership=?")){
	mysqli_stmt_bind_param($stmt, "si", $title, $ownership);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $dbcommit);
	$check = TRUE;
	while(mysqli_stmt_fetch($stmt)){
		if($commit == $dbcommit){
			$check = False;
			echo "Project $title with commit $commit already exists!";
		}
	}
}



# add entry to Projects table if no matching commit number
if($check){
$sql="INSERT INTO Projects (title, url, commit, uploaded, ownership, userId)
        VALUES('$title', '$_POST[url]', '$commit', '$date', '$ownership', '$uid')";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
echo "Successfully added a project!";
}
mysqli_close($con);
} else {
echo "Must enter URL and commit number.";
}
echo '<p>Click <a href="CCBProjects.php">here</a> to go back</p>';
?>
</body>
</html>