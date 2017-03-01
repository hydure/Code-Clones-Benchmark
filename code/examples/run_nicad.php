<html>
<body>

<?php
$cmd="./script.sh $_POST[username] $_POST[password]";
exec("$cmd");
?>

</body>
</html>
