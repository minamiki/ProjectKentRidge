<?php
include("inc/header-php.php");
include('../modules/quiz.php');

// check if the quiz exists
if(isset($_GET['id'])){
	$quiz = new Quiz($_GET['id']);
	// check if the quiz exists
	if($quiz->exists()){
		$quiz_exist = true;
		// Unpublish the quiz
		if(isset($_GET['unlist'])){
			$state = $quiz->unpublish($member->id);
			if(!$state){
				$quiz_exist = false;
			}
		}else{
			// Publish the quiz
			require('../modules/checkAchievements.php');
			
			// prepare the achievement array for possible multiple achievements
			$achievement_array = array();
			
			// set the quiz as published
			$level = $quiz->publish($member->id);
			
			if($level == false){
				// authentication error
				$quiz_exist = false;
			}elseif($level == -2){
				// publish failed, redirect user
				header("Location: modifyQuiz.php?step=4&id=".$quiz->quiz_id);
			}else{
				if($level != -1){
					$achievement_array[] = $level;	// provide the ID of the level acheievement
				}
				
				// check if there are any achievements
				$achievement_array = checkAchievements($member->id, $achievement_array);
			}
			
			//----------------------------------------
			// Display splash screen with results
			//----------------------------------------
			$achievement_details = retrieveAchievements($achievement_array);
		}
	}else{
		$quiz_exist = false;
	}
}else{
	$quiz_exist = false;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Publish Quiz</title>
<?php include("inc/header-css.php");?>
<link href="css/quiz.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/publishEngine.php"); ?>
<?php include("inc/footer-js.php"); ?>
<?php if(!isset($_GET['unlist'])){ ?>
<script type="text/javascript" src="../webroot/js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details; ?>);
</script>
<?php } ?>
</body>
</html>