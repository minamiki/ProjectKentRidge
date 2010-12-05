<?php
require 'database.php';

$method = $_REQUEST['method'];
$status = new Status();

if($method=='achievements-notification'){
	echo json_encode($status->checkAchievements(0));	
}else if($method=='system-notification'){
	echo json_encode($status->checkSystem(0));	
}

class Status
{
		
function Status(){
}

function checkAchievements($memberid){
	$database = new Database();
	$result = array();
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
}
?>