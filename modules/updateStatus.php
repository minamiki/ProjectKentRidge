<?php
require 'database.php';

$method = $_REQUEST['method'];
$status = new Status();

if($method=='achievements-notification'){
	echo json_encode($status->checkAchievements(0));	
}else if($method=='system-notification'){
	echo json_encode($status->checkSystem(0));	
}else if($method=='clear-achievements-notification'){
	echo json_encode($status->clearAchievementsNotification(0));	
}else if($method=='clear-system-notification'){
	echo json_encode($status->clearSystemNotification(0));	
}else if($method=='recent-achievements-notification'){
	echo json_encode($status->recentAchievementsNotification(0));
}

class Status
{
	
function Status(){
}

function checkAchievements($memberid){
	$result = array();
	$database = new Database();
	$achievements = $database->get('g_achievements_log',array('fk_achievement_id'),'fk_member_id="'.$memberid.'" AND isRead="0"');
	foreach($achievements as $achievement){
		$description = $database->get('g_achievements',array('description'),'id="'.$achievement['fk_achievement_id'].'"');
		array_push($result,$description[0]);
	}
	
	return $result;
}

function checkSystem($memberid){
	$database = new Database();
	$systemnotes = $database->get('s_notifications',array('id','notification'),'fk_member_id='.$memberid.' AND isRead=0');

	return $systemnotes;
}

function clearAchievementsNotification($memberid){
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