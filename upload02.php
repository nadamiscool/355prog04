<?php

// see HTML form (upload02.html) for overview of this program

// include code for database access
require 'database.php';

// set PHP variables from data in HTML form 
$fileName       = $_FILES['Filename']['name'];
$tempFileName   = $_FILES['Filename']['tmp_name'];
$fileSize       = $_FILES['Filename']['size'];
$fileType       = $_FILES['Filename']['type'];
$fileDescription = $_POST['Description']; 

// set server location (subdirectory) to store uploaded files
$fileLocation = "uploads/";
$fileFullPath = $fileLocation . $fileName; 
if (!file_exists($fileLocation))
    mkdir ($fileLocation); // create subdirectory, if necessary



// connect to database
$pdo = Database::connect();

// exit, if requested file already exists -- in the database table 
$fileExists = false;
$sql = "SELECT filename FROM customer WHERE filename='$fileName'";
foreach ($pdo->query($sql) as $row) {
    if ($row['filename'] == $fileName) {
        $fileExists = true;
    }
}
if ($fileExists) {
    echo "File <html><b><i>" . $fileName 
        . "</i></b></html> already exists in DB. Please rename file.";
    exit(); 
}

// exit, if requested file already exists -- in the subdirectory 
if(file_exists($fileFullPath)) {
    echo "File <html><b><i>" . $fileName 
        . "</i></b></html> already exists in file system, "
        . "but not in database table. Cannot upload.";
    exit(); 
}
if (!$fileName) {
   die("No filename.");
}

// abort if file is not an image
// never assume the upload succeeded
if ($_FILES['Filename']['error'] !== UPLOAD_ERR_OK) {
   die("Upload failed with error code " . $_FILES['file']['error']);
}
$info = getimagesize($_FILES['Filename']['tmp_name']);
if ($info === FALSE) {
   die("Error Unable to determine <i>image</i> type of uploaded file");
}
if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) 
        && ($info[2] !== IMAGETYPE_PNG)) {
   die("Not a gif/jpeg/png");
}

$fp      = fopen($tempFileName, 'r');
$content = fread($fp, filesize($tempFileName));
$content = addslashes($content);
fclose($fp);

// if all of above is okay, then upload the file
$result = move_uploaded_file($tempFileName, $fileFullPath);

// if upload was successful, then add a record to the SQL database
if ($result) {
    echo "Your file <html><b><i>" . $fileName 
        . "</i></b></html> has been successfully uploaded";
    $sql = "INSERT INTO customer (filename, filesize, filetype, content, description, path) "
    . "VALUES ('$fileName', '$fileSize', '$fileType', '$content', '$fileDescription', '$fileFullPath')";

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $pdo->prepare($sql);
    $q->execute(array());
// otherwise, report error
} else {
    echo "Upload denied for this file. Verify file size < 2MB. ";
}

// list all files in database 
// ORDER BY BINARY filename ASC (sorts case-sensitive, like Linux)
echo '<br><br>All files in database...<br><br>';
$sql = 'SELECT * FROM customer ' 
    . 'ORDER BY BINARY filename ASC;';
$i = 0; 
foreach ($pdo->query($sql) as $row) {
    echo ' ... [' . $i++ . '] --- ' . $row['filename']  . '<br>' . $row['description'].'<br>'.
            '<img width=100 src="data:image/jpeg;base64,'
        . base64_encode( $row['content'] ).'"/>'. '<br>'. $row['path'];
   
}
echo '<br><br>';

// list all files in subdirectory
/*echo 'All files in subdirectory...<br>';
echo '<pre>';
$arr = array_slice(scandir("$fileLocation"), 2);
asort($arr);
print_r($arr);
echo '<pre>';
echo '<br><br>'; */

// disconnect
Database::disconnect(); 