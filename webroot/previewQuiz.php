<?php include("inc/header-php.php"); ?>
<?php require('../modules/quiz.php');
require('../modules/variables.php');
$quiz = new Quiz($_GET['id']);
if($quiz->exists()){
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Quizroo: Preview Quiz</title>
<meta property="og:title" content="<?php echo $quiz->quiz_name; ?>"/>
<meta property="og:type" content="game"/>
<meta property="og:image" content="<?php echo $VAR_URL."quiz_images/imgcrop.php?w=50&amp;h=50&amp;f=".$quiz->quiz_picture; ?>"/> 
<meta property="og:url" content="<?php echo $FB_CANVAS."previewQuiz.php?id=".$quiz->quiz_id; ?>"/>
<meta property="og:site_name" content="Quizroo"/>
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>"/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php }else{ ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="og:title" content=""/> 
<meta property="og:type" content="game"/>
<meta property="og:image" content=""/> 
<meta property="og:url" content=""/>
<meta property="og:site_name" content="Quizroo"/>
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>"/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Quiz not found</title>
<?php } ?>
<?php include("inc/header-css.php");?>
</head>

<body>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/previewQuiz.php"); ?>
<?php include("inc/footer-js.php"); ?>
</body>
</html>