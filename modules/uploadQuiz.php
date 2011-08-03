<?php
require('uploadFunctions.php');
//check file size, if too huge, display error msg and discard
//check file uploaded is a valid file
//check image size, resize if too big
//create image files of respective file types

$unikey = $_GET['unikey'];
$uploaddir = '../quiz_images/';
$basename = $unikey."_".basename($_FILES['uploadfile']['name']);
$imagename = $uploaddir.$basename;
$size = $_FILES['uploadfile']['size'];
//if image file size is more than 2mb, remove the uploaded file
if($size > 1048576*2){
	echo "error file size > 2 MB";
	unlink($_FILES['uploadfile']['tmp_name']);
	exit;
}

//if uploaded file is a valid file, move to $imagename
if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $imagename)){
	echo "success";
	
	// find out the ratio of the image
	list($width_orig, $height_orig) = getimagesize($imagename);
	
	$max_image_size = 480;
	// find out if the image is too big
	//resize the image if too big
	if($width_orig > $max_image_size || $height_orig > $max_image_size){
		if($width_orig > $max_image_size){
			$width = $max_image_size;
			$height = $max_image_size * ($height_orig/$width_orig);
		}else{
			$width = $max_image_size * ($width_orig/$height_orig);
			$height = $max_image_size;
		}							  
	}else{
		$width = $width_orig;
		$height = $height_orig;
	}
	
	// create a temporary image
	$image_p = imagecreatetruecolor($width, $height);
	
	// handle JPEG
	if(exif_imagetype($imagename) == IMAGETYPE_JPEG){
		$image = imagecreatefromjpeg($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagejpeg($image_p, $imagename, 80);
	}
	// handle GIF
	if(exif_imagetype($imagename) == IMAGETYPE_GIF){
		$image = imagecreatefromgif($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagegif($image_p, $imagename);					
	}
	// handle PNG
	if(exif_imagetype($imagename) == IMAGETYPE_PNG){
		$image = imagecreatefrompng($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagepng($image_p, $imagename, 8);
	}
	if($image_p){
		// destroy the temporary image
		imagedestroy($image_p);
	}
}else{
	error_log("Image Upload error: ".$_FILES['uploadfile']['error']." --- ".$_FILES['uploadfile']['tmp_name']." %%% ".$file."($size)");
}
?>