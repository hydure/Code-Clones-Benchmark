<html>
<body>

<?php
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

$sql="INSERT INTO s (username, password)
        VALUES('$_POST[username]', '$_POST[password]')";

if (!mysqli_query($con, $sql)) {
        die("Error: " . mysqli_error($con));
}
echo "1 account added.";

mysqli_close($con)
?>
</body>
</html>
