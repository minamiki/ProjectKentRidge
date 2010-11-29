<?php
require 'database.php';

//$method = $_REQUEST['method'];
$status = new Status();

//if($method=='achievements-notification'){
	echo json_encode($status->checkAchievements(0));	
//}

class Status
{
		
function Status(){
}

function checkAchievements($memberid){
	$database = new Database();
	//$quizTaken = $database->get('q_store_result',array('fk_quiz_id'),'fk_member_id='.$memberid.' AND unread=1');
	$quizTaken = array('1');
	
	return $quizTaken;
}
	
}
?>