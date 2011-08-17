<!-- This is an interface for previewing quizzes, including pages of UI in which taking the status od the quiz to define the title accordingly and enable quiz recommendation id the user has taken the quiz -->

<?php
require('inc/header-php.php');
require('../modules/quiz.php'); //require all the database operations on quizzes
require('../modules/variables.php');
if(isset($_GET['id'])){
	// check if id is empty
	if($_GET['id'] == ""){
		// attempt to extract the other id parameter
		$url_vars = explode('&', $_SERVER["QUERY_STRING"]);
		$url_id = 0;
		foreach($url_vars as $test_id){
			if(is_numeric($test_id)){
				$url_id = $test_id;
				break;
			}
		}
	}else{
		// give the real id
		$url_id = $_GET['id'];	
	}
}else{
	// attempt to extract the other id parameter
	$url_vars = explode('&', $_SERVER["QUERY_STRING"]);
	$url_id = 0;
	foreach($url_vars as $test_id){
		if(is_numeric($test_id)){
			$url_id = $test_id;
			break;
		}
	}
}
$quiz = new Quiz($url_id); // define the variable 'quiz' for the extracted quiz
$quiz_state = $quiz->exists(); // return false if the quiz does not exist or is not published
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![if !IE]><html xmlns="http://www.w3.org/1999/xhtml"><![endif]>
<!--[if lt IE 9]><html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- If the quiz exists, title is Preview Quiz. If not, title is Quiz not found-->
<?php if($quiz_state){ ?>
<title>Quizroo: Preview Quiz</title>
<?php }else{ ?>
<title>Quizroo: Quiz not found</title>
<?php } ?>
<?php include("inc/header-css.php");?>
<link href="css/quiz.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/previewQuiz.php"); ?>
<?php include("inc/footer-js.php"); ?>
<!-- if the user has taken the quiz, enable recommendation of quiz-->
<?php if($quiz_state){ if($quiz->hasTaken($member->id)){ ?>
<script type="text/javascript">
$(document).ready(function(){
	// Enable recommendation of quiz
	Share.recommend($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>});
});
</script>
<?php }} ?>
</body>
</html>

