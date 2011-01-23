<?php
require 'database.php';

$method=$_REQUEST['method'];
if($method=='results'){
	$database = new Database();
	$quiz_id = $_REQUEST['quiz_id'];
	$result_id = $_REQUEST['result_id'];
	$results = array('quiz_details'=>array(),'result_details'=>array());
	
	$quiz_details = $database->get('q_quizzes',array('quiz_name','quiz_id'),'quiz_id='.$quiz_id);
	
	foreach($quiz_details as $quiz_detail){
		$results['quiz_details'][] = $quiz_detail;
	}
		
	$result_details = $database->get('q_results',array('result_title','result_description','result_picture'),'result_id='.$result_id);
	foreach($result_details as $result_detail){
		$results['result_details'][] = $result_detail;
	}	
	
	echo json_encode($results);
}
?>