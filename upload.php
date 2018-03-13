<?php
//if upload form is submitted
if(isset($_POST["upload"])){
    //get the file information
    $fileName = basename($_FILES["image"]["name"]);
    $fileTmp = $_FILES["image"]["tmp_name"];
    $fileType = $_FILES["image"]["type"];
    $fileSize = $_FILES["image"]["size"];
    $fileExt = substr($fileName, strrpos($fileName, ".") + 1);
    
    //specify image upload directory
    $largeImageLoc = 'uploads/images/thumb/'.$fileName;
    $thumbImageLoc = 'uploads/images/'.$fileName;
 
    //check file extension
    if((!empty($_FILES["image"])) && ($_FILES["image"]["error"] == 0)){
        if($fileExt != "jpg" && $fileExt != "jpeg" && $fileExt != "png"){
            $error = "Sorry, only JPG, JPEG & PNG files are allowed.";
        }
    }else{
        $error = "Select a JPG, JPEG & PNG image to upload";
    }
    
    //if everything is ok, try to upload file
    if(strlen($error) == 0 && !empty($fileName)){
        if(move_uploaded_file($fileTmp, $largeImageLoc)){
            //file permission
            chmod ($largeImageLoc, 0777);
            
            //get dimensions of the original image
            list($width_org, $height_org) = getimagesize($largeImageLoc);
            
            //get image coords
            $x = (int) $_POST['x'];
            $y = (int) $_POST['y'];
            $width = (int) $_POST['w'];
            $height = (int) $_POST['h'];

            //define the final size of the cropped image
            $width_new = $width;
            $height_new = $height;
            
            //crop and resize image
            $newImage = imagecreatetruecolor($width_new,$height_new);
            
            switch($fileType) {
                case "image/gif":
                    $source = imagecreatefromgif($largeImageLoc); 
                    break;
                case "image/pjpeg":
                case "image/jpeg":
                case "image/jpg":
                    $source = imagecreatefromjpeg($largeImageLoc); 
                    break;
                case "image/png":
                case "image/x-png":
                    $source = imagecreatefrompng($largeImageLoc); 
                    break;
            }
            
            imagecopyresampled($newImage,$source,0,0,$x,$y,$width_new,$height_new,$width,$height);
            
            switch($fileType) {
                case "image/gif":
                    imagegif($newImage,$thumbImageLoc); 
                    break;
                case "image/pjpeg":
                case "image/jpeg":
                case "image/jpg":
                    imagejpeg($newImage,$thumbImageLoc,90); 
                    break;
                case "image/png":
                case "image/x-png":
                    imagepng($newImage,$thumbImageLoc);  
                    break;
            }
            imagedestroy($newImage);
            
            //remove large image
            //unlink($imageUploadLoc);

            //display cropped image
            echo 'CROP IMAGE:<br/><img src="'.$thumbImageLoc.'"/>';
        }else{
            $error = "Sorry, there was an error uploading your file.";
        }
    }else{
        //display error
        echo $error;
    }
}
?>
