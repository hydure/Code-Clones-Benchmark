<html>
<body>

<?php
if (!empty($_POST['detector'])) {
    echo "You chose to run < "; 
    foreach($_POST['detector'] as $detector) {
        echo "$detector ";
    }

    echo "> on dataset ".$_POST['datasetSelect']."<br>";
} else {
    echo "No code clone detectors were selected.<br>";
}
?>

</body>
</html>
