<!-- Does the saving of data and, 
 Directing of which files to go to (ie. /webroot/createQuiz.php?step=3)for each step when a button is clicked -->

<?php 
require('quizrooDB.php'); ?>
<?php
// get the member info
require('member.php');
require('quiz.php');
$member = new Member();

// turn on sessions
session_start();

// find out which step is it
if(isset($_GET['step'])){
	switch($_GET['step']){
		
		case 1: // save the quiz information
		
		// get the unikey from the form
		$key = $_POST['unikey'];
		
		// save the data from step 1
		$quiz_picture = ($_POST['result_picture_0'] != "") ? $_POST['result_picture_0'] : "none.gif";
		if(isset($_POST['id'])){
			$quiz = new Quiz($_POST['id']);
			$quiz_id = $quiz->update($_POST['quiz_title'], $_POST['quiz_description'], $_POST['quiz_cat'], $quiz_picture, $member->id);
		}else{
			$quiz = new Quiz();
			$quiz_id = $quiz->create($_POST['quiz_title'], $_POST['quiz_description'], $_POST['quiz_cat'], $quiz_picture, $member->id, $key);
		}
		
		// direct them to step 2
		header("Location: ../webroot/createQuiz.php?step=2&id=".$quiz_id);
		
		break;		
		case 2: // save the quiz results
		
		// get the id from the form
		$quiz_id = $_POST['id'];
		
		// save the results from step 2
		$quiz = new Quiz($quiz_id);
		// Quiz Results
		for($i = 0; $i < $_POST['resultCount']; $i++){
			if(isset($_POST['result_title_'.$i]) && isset($_POST['result_description_'.$i]) && isset($_POST['result_picture_'.$i])){
				$result_title = $_POST['result_title_'.$i];
				$result_description = $_POST['result_description_'.$i];
				$result_picture = ($_POST['result_picture_'.$i] != "") ? $_POST['result_picture_'.$i] : "none.gif";
				if(isset($_POST['ur'.$i])){
					$quiz->updateResult($result_title, $result_description, $result_picture, $_POST['ur'.$i], $member->id);
				}else{
					$quiz->addResult($result_title, $result_description, $result_picture, $member->id);
				}
			}
		}
		
		// check the direction to go
		if($_POST['save'] == "Previous Step"){
			header("Location: ../webroot/createQuiz.php?step=1&id=".$quiz_id);
		}else{
			header("Location: ../webroot/createQuiz.php?step=3&id=".$quiz_id);
		}
		
		break;
		case 3:
		
		// get the id from the form
		$quiz_id = $_POST['id'];
		
		// save the questions from step 3
		$quiz = new Quiz($quiz_id);
		
		// Insert the questions and options
		$questionArray = explode("_", $_POST['optionCounts']);

		$question = array();
		for($i = 0; $i < $_POST['questionCount']; $i++){
			if(isset($_POST['question_'.$i])){
				if(isset($_POST['uq'.$i])){
					$question_id = $quiz->updateQuestion($_POST['question_'.$i], $_POST['uq'.$i], $member->id);
				}else{
					$question_id = $quiz->addQuestion($_POST['question_'.$i], $member->id);
				}
			}
			// Quiz Options
			for($j = 0; $j < $questionArray[$i]; $j++){
				if(isset($_POST['q'.$i.'o'.$j]) && isset($_POST['q'.$i.'r'.$j]) && isset($_POST['q'.$i.'w'.$j])){
					if(isset($_POST['uq'.$i.'o'.$j])){
						$quiz->updateOption($_POST['q'.$i.'o'.$j], $_POST['q'.$i.'r'.$j], $_POST['q'.$i.'w'.$j], $_POST['uq'.$i.'o'.$j], $member->id);
					}else{
						$quiz->addOption($_POST['q'.$i.'o'.$j], $_POST['q'.$i.'r'.$j], $_POST['q'.$i.'w'.$j], $question_id, $member->id);
					}
				}
			}
		}
		
		// check the direction to go
		if($_POST['save'] == "Previous Step"){
			header("Location: ../webroot/createQuiz.php?step=2&id=".$quiz_id);
		}else{
			header("Location: ../webroot/createQuiz.php?step=4&id=".$quiz_id);
		}
		
		break;
		case 4: // final step
		
		// get the id from the form
		$quiz_id = $_POST['id'];
		
		// check the direction to go
		if($_POST['save'] == "Previous Step"){
			header("Location: ../webroot/createQuiz.php?step=3&id=".$quiz_id);
		}elseif($_POST['save'] == "Preview"){
			header("Location: ../webroot/createQuizSuccess.php?id=".$quiz_id);
		}else{
			header("Location: ../webroot/publishQuiz.php?id=".$quiz_id);
		}

		break;
	}
}
?>
