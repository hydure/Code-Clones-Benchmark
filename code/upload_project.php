<?php
 session_start();
// Check if a file has been uploaded
if(isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['size'] != 0) {
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
        //$data = $dbLink->real_escape_string(file_get_contents($_FILES  ['uploaded_file']['tmp_name']));
        $size = intval($_FILES['uploaded_file']['size']);
        $userId = intval($_SESSION['userSession']);
        $author = ($_SESSION['userName']);
        if ("$_POST[ownership_type]" == "1") {
            $ownership = $userId;
        } else {
            $ownership = -1;
        }
        
        //echo 'userID' . $userId;
        // Create the SQL query
        $query = "
            INSERT INTO `Projects` (
                `title`, `ownership`, `size`, `uploaded`, `userId`, `author`
            )
            VALUES (
                '{$name}', '{$ownership}', {$size}, NOW(), '{$userId}', '{$author}'
            )";
 
        // Execute the query
        $result = $dbLink->query($query);
        //get the project id of uploaded file
        if($result){
            $query2 = "Select projectID from Projects where userId='{$userId}' AND title='{$name}' AND ownership='{$ownership}'";
            $return = $dbLink->query($query2);
            $row = $return->fetch_assoc();
            $drname = $row['projectID'];
            //$path = "/home//MyNAS/files/p$drname";
            $path = "/home/reid/Desktop/MyNAS/files/p$drname";
            $file = $_FILES['uploaded_file']['tmp_name'];
            if(!mkdir($path, 0700, true)){
                echo 'An error accured while the file was being uploaded. '
                . 'Could not create directory';
                //Delete from database if failed to create directory
                $delete = "DELETE FROM Projects where projectID=$drname";
                $dbLink->query($delete);
            } else{
                if(!move_uploaded_file($file, "$path/$name")){
                    $delete = "DELETE FROM Projects where projectID=$drname";
                    $dbLink->query($delete);
                    echo 'An error accured while the file was being uploaded. '
                        . 'Could not save file';
                } else{
                    echo 'Success! Your file was successfully added!';
                }
            } //**/
        } else{
            echo "An error occured while the file was being uploaded.";
        }
        // Close the mysql connection
        $dbLink->close();
    } else {
        echo 'An error accured while the file was being uploaded. '
           . 'Error code: '. intval($_FILES['uploaded_file']['error']);
    }
 
}
else {
    echo 'Please choose a file to upload.';
}
 
// Echo a link back to the main page
echo '<p>Click <a href="CCBProjects.php">here</a> to go back</p>';
?>
