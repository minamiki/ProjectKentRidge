<?php require_once('uploadFunctions.php');

$unikey = $_GET['unikey'];
$uploaddir = '../quiz_images/';
$basename = $unikey."_".basename($_FILES['uploadfile']['name']);
$imagename = $uploaddir.$basename;
$size = $_FILES['uploadfile']['size'];
if($size > 1048576*2){
	echo "error file size > 2 MB";
	unlink($_FILES['uploadfile']['tmp_name']);
	exit;
}
if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $imagename)){
	echo "success";
	
	// find out the ratio of the image
	list($width_orig, $height_orig) = getimagesize($imagename);
	
	$max_image_size = 800;
	// find out if the image is too big
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
	
	$image_p = imagecreatetruecolor($width, $height);
	
	// handle JPEG
	if(exif_imagetype($imagename) == IMAGETYPE_JPEG){
		$image = imagecreatefromjpeg($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagejpeg($image_p, $imagename, 100);
		imagedestroy($image_p);
	}
	// handle GIF
	if(exif_imagetype($imagename) == IMAGETYPE_GIF){
		$image = imagecreatefromgif($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagegif($image_p, $imagename, 10);
		imagedestroy($image_p);						
	}
	// handle PNG
	if(exif_imagetype($imagename) == IMAGETYPE_PNG){
		$image = imagecreatefrompng($imagename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// output the image
		imagepng($image_p, $imagename, 10);
		imagedestroy($image_p);
	}
}else{
	echo "error ".$_FILES['uploadfile']['error']." --- ".$_FILES['uploadfile']['tmp_name']." %%% ".$file."($size)";
}
?>