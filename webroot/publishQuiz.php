<?php include("inc/header-php.php"); ?>
<?php
//----------------------------------------
// Publish the quiz
//----------------------------------------
include('../modules/quiz.php');
require('../modules/checkAchievements.php');
$quiz = new Quiz($_GET['id']);

// prepare the achievement array for possible multiple achievements
$achievement_array = array();

// check if member is owner of the quiz
if($quiz->isOwner($member->id)){
	// set the quiz as published
	$level = $quiz->publish();
	
	echo $level;
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
<script type="text/javascript" src="../webroot/js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details?>);
</script>

</body>
</html>