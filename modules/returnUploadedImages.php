<?php // return uploaded images
//glob: searches for all the pathnames matching the parameter - http://php.net/manual/function.glob.php
foreach(glob("quiz_images/".$_GET['unikey']."*") as $filename){ ?>
<img name="" src="quiz_images/imgcrop.php?w=90&h=60&f=<?php echo basename($filename); ?>" width="90" height="60">
<?php } ?>