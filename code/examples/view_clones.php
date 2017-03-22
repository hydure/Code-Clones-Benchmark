<!DOCTYPE html>
<html>
<body>

<?php
$con = mysqli_connect('localhost', 'root', '*XMmysq$', 'cc_bench');
if(!$con) {
        die('coult not connect: ' . mysqli_connect_error());
}

$result = mysqli_query($con,"SELECT * FROM Clones");

echo "<table border='1' cellpadding='10'>
<tr>
<th>ID</th>
<th>datasetID</th>
<th>file1</th>
<th>start</th>
<th>end</th>
<th>file2</th>
<th>start</th>
<th>end</th>
<th>sim</th>
</tr>";

$i=0;
while($row = mysqli_fetch_array($result))
{
    echo "<tr>";
    echo "<td>" . $row['cloneID'] . "</td>";
    echo "<td>" . $row['datasetID'] . "</td>";
    echo "<td>" . $row['file1'] . "</td>";
    echo "<td>" . $row['start1'] . "</td>";
    echo "<td>" . $row['end1'] . "</td>";
    echo "<td>" . $row['file2'] . "</td>";
    echo "<td>" . $row['start2'] . "</td>";
    echo "<td>" . $row['end2'] . "</td>";
    echo "<td>" . $row['sim'] . "</td>";
    echo "</tr>";
    $i++;
}
echo "</table>";

mysqli_close($con);
?>

</body>
</html>
