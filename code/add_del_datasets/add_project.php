<?php
session_start();
?>

<html>
    <head>
        <script type="text/javascript">
         <!--
            function showSnackbar() {
                // Get the snackbar DIV
                var x = document.getElementById("snackbar")

                // Add the "show" class to DIV
                x.className = "show";

                // After 3 seconds, remove the show class from DIV
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
            }
         //-->
      </script>
    </head>
<body>

<?php
error_reporting(0);
if (!empty($_POST[url]) && !empty($_POST[commit])) {
$con = mysqli_connect('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

# normalize url
#$url = exec("echo $_POST[url] | sed 's:\(.*com\)/\([^/]*\)/\([^/]*\)/.*:\1/\2/\3:'");
#echo "$url<br>";

# get title from url
$title = exec("echo $_POST[url] | sed 's:.*\.com/[^/]*/::' | sed 's:/.*::'");

# get repo host username
$user = exec("echo $_POST[url] | sed 's:.*com/::' | sed 's:/.*::'");

# get head commit number
if ("$_POST[commit]" == "head") {
	$commit = exec("/home/pi/Code-Clones-Benchmark/code/get_head_commit.sh $_POST[url] | head -c 12");
} else {
	$commit = exec("echo $_POST[commit] | head -c 12");
}

# get date
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

#get userId
$uid = $_SESSION['userSession'];

# set ownership
if ("$_POST[private]" != "1") {
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
$sql="INSERT INTO Projects (title, url, commit, uploaded, ownership, userId, author)
        VALUES('$title', '$_POST[url]', '$commit', '$date', '$ownership', '$uid', '$user')";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
    echo '<link href="CCB1.1.css" type = "text/css" rel="stylesheet">
          <div id="snackbar">Successfully added a project!</div>';
}
mysqli_close($con);
} else {
    echo '<link href="CCB1.1.css" type = "text/css" rel="stylesheet">',
          '<div id="snackbar">Must enter URL and commit number.</div>',
          'showSnackbar()', '</script>';
}
echo '<p>Click <a href="ccb/CCBProjects.php">here</a> to go back</p>';
?>
</body>
</html>
