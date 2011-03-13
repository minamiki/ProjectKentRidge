<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
require('../modules/quiz.php');
require('../modules/variables.php');
if(isset($_GET['id'])){
	// check if id is empty
	if($_GET['id'] == ""){
		// tell user it is invalid
		$url_id = 0;
	}else{
		// give the real id
		$url_id = $_GET['id'];	
	}
}else{
	// tell user id is missing
	$url_id = 0;
}
$quiz = new Quiz($url_id);
$quiz_state = $quiz->exists();
if($quiz_state){ ?>
<title>Quizroo Quiz: <?php echo $quiz->quiz_name; ?></title>
<meta property="og:title" content="<?php echo $quiz->quiz_name; ?>" />
<meta property="og:type" content="game" />
<meta property="og:image" content="<?php echo $VAR_URL."quiz_images/imgcrop.php?w=50&amp;h=50&amp;f=".$quiz->quiz_picture; ?>" /> 
<meta property="og:url" content="http://quizroo.nus-hci.com/webroot/quiz.php?id=<?php echo $quiz->quiz_id; ?>" />
<meta property="og:site_name" content="Quizroo" />
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php } ?>
<link rel="shortcut icon" href="img/favicon.ico" />
<link href="css/previewExternal.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("../modules/previewQuizExternal.php"); ?>
<script type="text/javascript" src="js/common.js"></script>
</body>
</html>