<?php
require 'database.php';

$method = $_REQUEST['method'];
$status = new Status();
$member = 7;

if($method=='achievements'){
	echo json_encode($status->checkAchievements($member));	
}else if($method=='system-notification'){
	echo json_encode($status->checkSystem($member));	
}else if($method=='read-achievements'){
	echo json_encode($status->readAchievements($member));	
}else if($method=='clear-system-notification'){
	echo json_encode($status->clearSystemNotification($member));	
}else if($method=='recent-achievements-notification'){
	echo json_encode($status->recentAchievementsNotification($member));
}

class Status
{
	
function Status(){
}

/**
 * Returns the achievements, quiztaking score and quiz creation scores for a user specified.
 * @param int $memberid
 */
function checkAchievements($memberid){
	$result = array('overview'=>array(),'quiztaker'=>array(),'quizcreator'=>array());
	$database = new Database();
	
	/*
	 * Checks for 3 latest achievements for user specified and fetches the image url, name and description for the achievement.
	 */
	$achievementsOverview = $database->query('SELECT fk_achievement_id,timestamp FROM g_achievements_log WHERE fk_member_id="'.$memberid.'" ORDER BY timestamp DESC LIMIT 3'); 
	foreach($achievementsOverview as $achievementOverview){
		$description = $database->get('g_achievements',array('image','name','description'),'id="'.$achievementOverview['fk_achievement_id'].'"');
		array_push($result['overview'],$description[0]);
	}
	
	/*
	 * Checks for total score and todays score for quiz taker and quiz creator roles for the user specified. 
	 */
	$quizScore = $database->get('members',array('quiztaker_score','quizcreator_score','quiztaker_score_today','quizcreator_score_today'),'member_id="'.$memberid.'"');
	
	$result['quiztaker'] = array('quiztaker_score'=>$quizScore[0]['quiztaker_score'],'quiztaker_score_today'=>$quizScore[0]['quiztaker_score_today']);
	$result['quizcreator'] = array('quizcreator_score'=>$quizScore[0]['quizcreator_score'],'quizcreator_score_today'=>$quizScore[0]['quizcreator_score_today']);
	
	return $result;
}

function checkSystem($memberid){
	$database = new Database();

	$systemnotes = $database->query('SELECT notification, label, color FROM s_notifications LEFT JOIN s_notifications_labels ON s_notifications.fk_label_id = s_notifications_labels.id WHERE fk_member_id='.$memberid);
	//.' AND isRead=0'
	
	return $systemnotes;
}

function readAchievements($memberid){
	$database = new Database();
	$result = $database->update('g_achievements_log','fk_member_id',$memberid,array('isRead'),array('1'));
	if($result==1){
		return 'success';
	}else{
		return 'failed';
	}
}

function clearSystemNotification($memberid){
	$database = new Database();
	$result = $database->update('s_notifications','fk_member_id',$memberid,array('isRead'),array('1'));
	if($result==1){
		return 'success';
	}else{
		return 'failed';
	}
}

function recentAchievementsNotification($memberid){
	$result = array();
	$database = new Database();
	$achievements = $database->limit('g_achievements_log',array('fk_achievement_id'),'fk_member_id="'.$memberid.'"',5);
	foreach($achievements as $achievement){
		$description = $database->get('g_achievements',array('description'),'id="'.$achievement['fk_achievement_id'].'"');
		array_push($result,$description[0]);
	}
	
	return $result;
}

}
?>