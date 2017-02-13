<html>
<body>

<?php
$con = mysqli_connect('localhost', 'root', 'MYSQL2013Mac', 'bookstore');
if (!$con)
	{
	die('could not connect: ' . mysqli_connect_error());
	}

/* mysql_select_db("bookstore", $con); */
$sql="INSERT INTO nametable (fname, lname)
VALUES
('$_POST[fname]', '$_POST[lname]')";

if (!mysqli_query($con, $sql))
	{
	die('Error: ' . mysqli_error($con));
	}
echo "1 record added";

mysqli_close($con)
?>
</body>
</html>
