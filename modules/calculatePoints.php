<?php
include("variables.php");
//----------------------------------------
// For calculatation of points to award
//----------------------------------------

function calculatePoints($facebookID, $quiz_id, $quiz_publish_status){
	include("../Connections/quizroo.php");
	// register the global variables
	global $GAME_BASE_POINT, $GAME_MULTIPLIER, $GAME_REWARD_RETAKES, $achievement_array;
	
	// check if user has already taken this quiz
	mysql_select_db($database_quizroo, $quizroo);
	$queryCheck = sprintf("SELECT COUNT(store_id) AS count FROM q_store_result WHERE `fk_member_id` = %s AND `fk_quiz_id` = %s", $facebookID, $quiz_id);
	$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
	$row_getResults = mysql_fetch_assoc($getResults);
	$timesTaken = $row_getResults['count'];	
	mysql_free_result($getResults);
	
	if(($timesTaken == 1 || $GAME_REWARD_RETAKES) && $quiz_publish_status){
		// The following factors should be fulfilled before points are awarded
		// - first time taking this question OR always reward flag on
		// - quiz is published

		// get the multiplier value
		$queryCheck = sprintf("SELECT COUNT(store_id) AS count FROM `q_store_result` WHERE `fk_member_id` = %s AND DATE(`timestamp`) = DATE(NOW())", $facebookID);
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$todayMultiplier = $row_getResults['count'];
		mysql_free_result($getResults);
		
		// calculate the points by multiplier
		$points = $GAME_BASE_POINT + ($todayMultiplier - 1) * ($GAME_MULTIPLIER);
		
		// check the current member stats (for level up calculation later)
		$queryCheck = sprintf("SELECT `level`, quiztaker_score FROM `s_members` WHERE `member_id` = %d", $facebookID);
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$old_level = $row_getResults['level'];
		$old_score = $row_getResults['quiztaker_score'];
		mysql_free_result($getResults);
		
		// check if the there is a levelup:
		///////////////////////////////////////
		
		// check the level table 
		$queryCheck = sprintf("SELECT id FROM `g_levels` WHERE points <= (SELECT `quiztaker_score` FROM s_members WHERE member_id = %d)+%s ORDER BY points DESC LIMIT 0, 1", $facebookID, $points);
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$new_level = $row_getResults['id'];
		mysql_free_result($getResults);
		
		if($new_level > $old_level){
			// a levelup has occurred
			$achievement_array[] = $new_level;	// provide the ID of the level acheievement
			
			// update the member table to reflect the new level
			$queryUpdate = sprintf("UPDATE s_members SET quiztaker_score = quiztaker_score + %s, quiztaker_score_today = quiztaker_score_today + %s, level = %d WHERE member_id = %s", $points, $points, $new_level, $facebookID);
		}else{
			// just update the member table to reflect the points
			$queryUpdate = sprintf("UPDATE s_members SET quiztaker_score = quiztaker_score + %s, quiztaker_score_today = quiztaker_score_today + %s WHERE member_id = %s", $points, $points, $facebookID);
		}
		// execute the update statement
		mysql_query($queryUpdate, $quizroo) or die(mysql_error());	
	}
}
?>
