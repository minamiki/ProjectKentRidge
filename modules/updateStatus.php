<?php
require 'database.php';
require 'member.php';
//fn to get 3 latest achievements from db and store into array
//calculate and store number of achievements 
//get rank, level score of quiz taker
//get today's score and total score of quiz creator and taker of the user
//get and store notifications from the database
//mark notifications and achievements as read into db
//get 5 most recent achievements from db

$method = $_REQUEST['method'];
$status = new Status();
$member = new Member();
$member_id = $member->id;

if($method=='achievements'){
	echo json_encode($status->checkAchievements($member_id));	
}else if($method=='system-notification'){
	echo json_encode($status->checkSystem($member_id));	
}else if($method=='read-achievements'){
	echo json_encode($status->readAchievements($member_id));	
}else if($method=='clear-system-notification'){
	echo json_encode($status->clearSystemNotification($member_id));	
}else if($method=='recent-achievements-notification'){
	echo json_encode($status->recentAchievementsNotification($member_id));
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
	$result = array('overview'=>array(), 'achievements'=>array(), 'rank'=>array(), 'quiztaker'=>array(),'quizcreator'=>array());
	$database = new Database();
	
	/*
	 * Checks for 3 latest achievements for user specified and fetches the image url, name and description for the achievement.
	 */
	$achievementsOverview = $database->query('SELECT fk_achievement_id,timestamp,image,name,description FROM g_achievements_log LEFT JOIN g_achievements ON fk_achievement_id=g_achievements.id WHERE (fk_member_id="'.$memberid.'" AND type<>3) ORDER BY timestamp DESC'); 
	$count = 0;
	foreach($achievementsOverview as $achievementOverview){
		if($count<3){
			array_push($result['overview'],$achievementOverview);
			$count++;
		}else{
			break;
		}
	}
	
	/*
	 * Checks for total number of achievements.
	 */
	$result['achievements'] = array('score'=>count($achievementsOverview));
	
	/*
	 * Checks for the rank for the quiz taker role.
	 */
	$rank = $database->query('SELECT image,name,description,level FROM s_members LEFT JOIN g_achievements ON rank=g_achievements.id WHERE (member_id="'.$memberid.'")');
	$levelscore = $database->get('g_levels',array('points'),'id='.$rank[0]['level'].' OR id='.($rank[0]['level']+1));
	$result['rank'] = array('image'=>$rank[0]['image'],'name'=>$rank[0]['name'],'description'=>$rank[0]['description'],'level'=>$rank[0]['level'],'levelscore'=>$levelscore[0]['points'],'nextlevelscore'=>$levelscore[1]['points']);
		
	/*
	 * Checks for total score and todays score for quiz taker and quiz creator roles for the user specified. 
	 */
	$quizScore = $database->get('s_members',array('quiztaker_score','quizcreator_score','quiztaker_score_today','quizcreator_score_today'),'member_id="'.$memberid.'"');
	
	$result['quiztaker'] = array('quiztaker_score'=>$quizScore[0]['quiztaker_score'],'quiztaker_score_today'=>$quizScore[0]['quiztaker_score_today']);
	$result['quizcreator'] = array('quizcreator_score'=>$quizScore[0]['quizcreator_score'],'quizcreator_score_today'=>$quizScore[0]['quizcreator_score_today']);
	
	return $result;
}

function checkSystem($memberid){
//get and store notifications from the database
	$database = new Database();

	$othernotes = $database->query('SELECT notification, label, timestamp, color, fk_from_id, isRead FROM s_notifications_log LEFT JOIN s_notifications_labels ON s_notifications_log.fk_label_id = s_notifications_labels.id WHERE fk_member_id='.$memberid.' ORDER BY timestamp DESC LIMIT 3');	
	$systemnotes = $database->query('SELECT notification, label, timestamp, color FROM s_notifications LEFT JOIN s_notifications_labels ON s_notifications.fk_label_id = s_notifications_labels.id WHERE isVisible=1 ORDER BY timestamp DESC');
	//.' AND isRead=0'

	$notes = array("others"=>$othernotes,"system"=>$systemnotes);
	
	return $notes;
}

function readAchievements($memberid){
//to update isRead in the db when achievements are read
	$database = new Database();
	$result = $database->update('g_achievements_log','fk_member_id',$memberid,array('isRead'),array('1'));
	if($result==1){
		return 'success';
	}else{
		return 'failed';
	}
}

function clearSystemNotification($memberid){
//to update isRead in the db when notifications are read
	$database = new Database();
	$result = $database->update('s_notifications_log','fk_member_id',$memberid,array('isRead'),array('1'));
	if($result==1){
		return 'success';
	}else{
		return 'failed';
	}
}

function recentAchievementsNotification($memberid){
	//retrieve 5 most recent notifications from db
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