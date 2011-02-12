<?php require('../Connections/quizroo.php'); ?>
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
		$quiz = new Quiz();
		$quiz_picture = ($_POST['result_picture_0'] != "") ? $_POST['result_picture_0'] : "none.gif";
		$quiz_id = $quiz->create($_POST['quiz_title'], $_POST['quiz_description'], $_POST['quiz_cat'], $quiz_picture, $member->id);
		
		// direct them to step 2
		header("Location: ../webroot/createQuiz.php?step=2&id=".$quiz_id."&key=".$key);
		
		break;		
		case 2: // save the quiz results
		
		// get the unikey and id from the form
		$key = $_POST['unikey'];
		$quiz_id = $_POST['id'];
		
		// save the results from step 2
		$quiz = new Quiz($quiz_id);
		// Quiz Results
		for($i = 0; $i < $_POST['resultCount']; $i++){
			if(isset($_POST['result_title_'.$i]) && isset($_POST['result_description_'.$i]) && isset($_POST['result_picture_'.$i])){
				$result_title = $_POST['result_title_'.$i];
				$result_description = $_POST['result_description_'.$i];
				$result_picture = ($_POST['result_picture_'.$i] != "") ? $_POST['result_picture_'.$i] : "none.gif";
				if(isset($_POST['id'.$i])){
					$quiz->updateResult($result_title, $result_description, $result_picture, $_POST['id'.$i]);
				}else{
					$quiz->addResult($result_title, $result_description, $result_picture);
				}
			}
		}
		
		// direct them to step 3
		header("Location: ../webroot/createQuiz.php?step=3&id=".$quiz_id."&key=".$key);
		
		break;
		case 3:
		
		// get the unikey and id from the form
		$key = $_POST['unikey'];
		$quiz_id = $_POST['id'];
		
		// save the questions from step 3
		$quiz = new Quiz($quiz_id);
		
		// Insert the questions and options
		$questionArray = explode("_", $_POST['optionCounts']);

		$question = array();
		for($i = 0; $i < $_POST['questionCount']; $i++){
			if(isset($_POST['question_'.$i])){
				if(isset($_POST['uq'.$i])){
					$question_id = $quiz->updateQuestion($_POST['question_'.$i], $_POST['uq'.$i]);
				}else{
					$question_id = $quiz->addQuestion($_POST['question_'.$i]);
				}
			}
			// Quiz Options
			for($j = 0; $j < $questionArray[$i]; $j++){
				if(isset($_POST['q'.$i.'o'.$j]) && isset($_POST['q'.$i.'r'.$j]) && isset($_POST['q'.$i.'w'.$j])){
					if(isset($_POST['uq'.$i.'o'.$j])){
						$quiz->updateOption($_POST['q'.$i.'o'.$j], $_POST['q'.$i.'r'.$j], $_POST['q'.$i.'w'.$j], $_POST['uq'.$i.'o'.$j]);
					}else{
						$quiz->addOption($_POST['q'.$i.'o'.$j], $_POST['q'.$i.'r'.$j], $_POST['q'.$i.'w'.$j], $question_id);
					}
				}
			}
		}
		
		// direct them to step 4
		header("Location: ../webroot/createQuiz.php?step=4&id=".$quiz_id."&key=".$key);
		
		break;
		case 4: // final step
		
		
		$insertGoTo = "../webroot/createQuizSuccess.php?id=".$currentQuizID."#";
		header(sprintf("Location: %s", $insertGoTo));
		break;
		
	}
}else{
	// if not step is given we direct them to the first step
	header("Location: ../webroot/createQuiz.php");
}

/*
// Quiz Information
$quiz_title = $_POST['quiz_title'];
$quiz_description = $_POST['quiz_description'];
$quiz_cat = $_POST['quiz_cat'];
$quiz_picture = ($_POST['quiz_picture'] != "") ? $_POST['quiz_picture'] : "none.gif";
$quiz_member_id = $member->id;

// Quiz Results
for($i = 0; $i < $_POST['resultCount']; $i++){
	$result_title[] = $_POST['result_title_'.($i+1)];
	$result_description[] = $_POST['result_description_'.($i+1)];
	$result_picture[] = ($_POST['result_picture_'.($i+1)] != "") ? $_POST['result_picture_'.($i+1)] : "none.gif";
}
// Quiz Questions
$questionArray = explode("_", $_POST['optionCounts']);
$question = array();
for($i = 0; $i < $_POST['questionCount']; $i++){
	$question[][0] = $_POST['question_'.($i+1)];
	// Quiz Options
	for($j = 0; $j < $questionArray[$i]; $j++){
		$question[$i][1][$j][] = $_POST['q'.($i+1).'o'.($j+1)];
		$question[$i][1][$j][] = $_POST['q'.($i+1).'r'.($j+1)];
		$question[$i][1][$j][] = $_POST['q'.($i+1).'w'.($j+1)];
	}
}

mysql_select_db($database_quizroo, $quizroo);
// insert into the quiz table
$insertSQL = sprintf("INSERT INTO q_quizzes(`quiz_name`, `quiz_description`, `fk_quiz_cat`, `quiz_picture`, `fk_member_id`) VALUES (%s, %s, %d, %s, %d)",
				   GetSQLValueString($quiz_title, "text"),
				   GetSQLValueString($quiz_description, "text"),
				   GetSQLValueString($quiz_cat, "int"),
				   GetSQLValueString($quiz_picture, "text"),
				   GetSQLValueString($quiz_member_id, "int"));
mysql_query($insertSQL, $quizroo) or die(mysql_error());

// find the quiz id
$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
$row_resultID = mysql_fetch_assoc($resultID);
$currentQuizID = $row_resultID['insertID'];
mysql_free_result($resultID);

// Insert the results
$insertSQL = "INSERT INTO q_results(`result_title`, `result_description`, `result_picture`, `fk_quiz_id`) VALUES ";
for($i = 0; $i < $_POST['resultCount']; $i++){
	$insertSQL .= sprintf(" (%s, %s, %s, %d),",
				   GetSQLValueString($result_title[$i], "text"),
				   GetSQLValueString($result_description[$i], "text"),
				   GetSQLValueString($result_picture[$i], "text"),
				   GetSQLValueString($currentQuizID, "int"));
}
mysql_query(substr($insertSQL, 0, strlen($insertSQL)-1), $quizroo) or die(mysql_error());

// find the result id
$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
$row_resultID = mysql_fetch_assoc($resultID);
$lastResultID = $row_resultID['insertID'];
mysql_free_result($resultID);

// Insert the questions
for($i = 0; $i < $_POST['questionCount']; $i++){
	$insertSQL = sprintf("INSERT INTO q_questions(`question`, `fk_quiz_id`) VALUES (%s, %d)",
				   GetSQLValueString($question[$i][0], "text"),
				   GetSQLValueString($currentQuizID, "int"));
	mysql_query($insertSQL, $quizroo) or die(mysql_error());

	// find the question id
	$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
	$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
	$row_resultID = mysql_fetch_assoc($resultID);
	$currentQuestionID = $row_resultID['insertID'];
	mysql_free_result($resultID);

	// Insert the Quiz Options
	for($j = 0; $j < $questionArray[$i]; $j++){
		$resultValue = $lastResultID + $question[$i][1][$j][1] - 1;
		$insertSQL = sprintf("INSERT INTO q_options(`option`, `fk_result`, `option_weightage`, `fk_question_id`) VALUES (%s, %d, %d, %d)",
					   GetSQLValueString($question[$i][1][$j][0], "text"),
					   GetSQLValueString($resultValue, "int"),
					   GetSQLValueString($question[$i][1][$j][2], "int"),
					   GetSQLValueString($currentQuestionID, "int"));
		mysql_query($insertSQL, $quizroo) or die(mysql_error());
	}
}
*/
?>
