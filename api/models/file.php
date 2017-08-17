<?php
    header('Access-Control-Allow-Origin: *');
     
    $location = "../../images";
    $uploadfile = $_POST['fileName'];
    $uploadfilename = $_FILES['file']['tmp_name'];
    error_log($uploadfile." ".$uploadfilename);
     
    if(move_uploaded_file($uploadfilename, $location.'/'.$uploadfile)){
            echo 'File successfully uploaded!';
    } else {
            echo 'Upload error!';
    }
?>
