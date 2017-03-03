<html>
<body>

<?php
if (!empty($_POST['detector'])) {
echo "You chose:<br>"; 
foreach($_POST['detector'] as $detector) {
echo "$detector<br>";
}
} else {
echo "No code clone detectors were selected.<br>";
}
?>

</body>
</html>
