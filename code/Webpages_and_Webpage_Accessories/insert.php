
<html>
<body>



#change to pi user and password before uploading
<?php
$con = mysqli_connect('localhost', 'root', 'S3quenC3', 'UserInfo');


if (!$con)
	{
	die('could not connect: ' . mysqli_connect_error());
	}


$sql="INSERT INTO RegistrationInfo (firstname, lastname, email, username, password)

VALUES
('$_POST[firstname]', '$_POST[lastname]', '$_POST[email]', '$_POST[username]', 
'$_POST[password]')";


if (!mysqli_query($con, $sql))
	{
	
	die('Error: ' . mysqli_error($con));
	}

echo "1 record added";


mysqli_close($con)
?>
</body>
</html>
