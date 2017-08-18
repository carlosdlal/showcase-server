<?php
//include('../libraries/image/simple_image.php');
    class photos{
        public function moveArchive($context){
            //$location = "../../uploads/";
            $location = "/home/carlos-nuvlar/Devs/www/servertester/images";
            $params = json_decode($context->params);
            error_log($params->fileName);
            $uploadfile = $context->db->real_escape_string($params->fileName);
            $uploadfilename = $_FILES['file']['tmp_name'];

            error_log("*Location: ".$location." *Upload File: ".$uploadfile." *File Name: ".$uploadfilename);
            
            if(move_uploaded_file($uploadfilename, $location.'/'.$uploadfile)){
                error_log("File successfully uploaded!");
                $context->response['success'] = 1;
            }
            else{
                error_log("Upload error!");
                $context->response['success'] = 0;
            }
        }
    }