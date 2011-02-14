<?php include("inc/header-php.php"); ?>
<?php
include('../modules/quiz.php');
$quiz = new Quiz($_GET['id']);
// Unpublish the quiz
if(isset($_GET['unlist'])){
	if($quiz->isOwner($member->id)){
		$quiz->unpublish();
	}
}else{
	// Publish the quiz
	require('../modules/checkAchievements.php');
	
	// prepare the achievement array for possible multiple achievements
	$achievement_array = array();
	
	// check if member is owner of the quiz
	if($quiz->isOwner($member->id)){
		// set the quiz as published
		$level = $quiz->publish();
		
		if($level == false){
			// publish failed, redirect user
			header("Location: modifyQuiz.php?step=4&id=".$quiz->quiz_id);
		}else{
			if($level != -1){
				$achievement_array[] = $level;	// provide the ID of the level acheievement
			}
			
			// check if there are any achievements
			checkAchievements($member->id);
		}
	}
	//----------------------------------------
	// Display splash screen with results
	//----------------------------------------
	$achievement_details = retrieveAchievements($achievement_array);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Publish Quiz</title>
<?php include("inc/header-css.php");?>
</head>

<body>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/publishEngine.php"); ?>
<?php include("inc/footer-js.php"); ?>
<?php if(!isset($_GET['unlist'])){ ?>
<script type="text/javascript" src="../webroot/js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details?>);
</script>
<?php } ?>
</body>
</html>