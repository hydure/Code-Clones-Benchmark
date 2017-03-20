 <?php
 session_start();
// Check if a file has been uploaded
if(isset($_FILES['uploaded_file'])) {
    // Make sure the file was sent without errors
    if($_FILES['uploaded_file']['error'] == 0) {
        // Connect to the database
        $dbLink = new mysqli('127.0.0.1', 'root', '*XMmysq$', 'cc_bench');
        if(mysqli_connect_errno()) {
            die("MySQL connection failed: ". mysqli_connect_error());
        }
 
        // Gather all required data
        $name = $dbLink->real_escape_string($_FILES['uploaded_file']['name']);
        $mime = $dbLink->real_escape_string($_FILES['uploaded_file']['type']);
        $data = $dbLink->real_escape_string(file_get_contents($_FILES  ['uploaded_file']['tmp_name']));
        $size = intval($_FILES['uploaded_file']['size']);

        if ("$_POST[ownership_type]" == "1") {
            $ownership = -1;
        } else {
            $ownership = -1;
        }

        $userId = intval($_SESSION['userSession']);
        //echo 'userID' . $userId;
 
        // Create the SQL query
        $query = "
            INSERT INTO `Projects` (
                `title`, `ownership`, `size`, `content`, `uploaded`, `userId`
            )
            VALUES (
                '{$name}', '{$ownership}', {$size}, '{$data}', NOW(), '{$userId}'
            )";
 
        // Execute the query
        $result = $dbLink->query($query);
 
        // Check if it was successfull
        if($result) {
            echo 'Success! Your file was successfully added!';
        }
        else {
            echo 'Error! Failed to insert the file'
               . "<pre>{$dbLink->error}</pre>";
        }
    }
    else {
        echo 'An error accured while the file was being uploaded. '
           . 'Error code: '. intval($_FILES['uploaded_file']['error']);
    }
 
    // Close the mysql connection
    $dbLink->close();
}
else {
    echo 'Error! A file was not sent!';
}
 
// Echo a link back to the main page
echo '<p>Click <a href="CCBTools.php">here</a> to go back</p>';
?>

