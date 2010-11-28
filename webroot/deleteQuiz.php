<?php require_once('Connections/kuizzroo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
	// find out the questions belonging to this quiz
	mysql_select_db($database_kuizzroo, $kuizzroo);
	$query_getQuestions = sprintf("SELECT question_id FROM qb_questions WHERE fk_quiz_id = %s", GetSQLValueString($_GET['id'], "int"));
	$getQuestions = mysql_query($query_getQuestions, $kuizzroo) or die(mysql_error());
	$row_getQuestions = mysql_fetch_assoc($getQuestions);
	$totalRows_getQuestions = mysql_num_rows($getQuestions);
	
	// loop and remove options belonging to this question
	do{
		$deleteSQL = sprintf("DELETE FROM qb_options WHERE fk_question_id=%s", GetSQLValueString($row_getQuestions['question_id'], "int"));
		//echo "<br>".$deleteSQL;
		mysql_select_db($database_kuizzroo, $kuizzroo);
		mysql_query($deleteSQL, $kuizzroo) or die(mysql_error());
	}while($row_getQuestions = mysql_fetch_assoc($getQuestions));
	
	
	// select results
	mysql_select_db($database_kuizzroo, $kuizzroo);
	$query_getResults = sprintf("SELECT result_picture FROM qb_results WHERE fk_quiz_id = %s", GetSQLValueString($_GET['id'], "int"));
	$getResults = mysql_query($query_getResults, $kuizzroo) or die(mysql_error());
	$row_getResults = mysql_fetch_assoc($getResults);
	$totalRows_getResults = mysql_num_rows($getResults);
	
	// loop and remove pictures belonging to results
	do{
		if($row_getResults['result_picture'] != "none.gif"){
			unlink("quiz_images/".$row_getResults['result_picture']);
		}
	}while($row_getQuestions = mysql_fetch_assoc($getResults));
	mysql_free_result($getResults);
	
	// delete results
	$deleteSQL = sprintf("DELETE FROM qb_results WHERE fk_quiz_id=%s", GetSQLValueString($_GET['id'], "int"));
	//echo "<br>".$deleteSQL;
	mysql_select_db($database_kuizzroo, $kuizzroo);
	mysql_query($deleteSQL, $kuizzroo) or die(mysql_error());
	
	// delete question
	$deleteSQL = sprintf("DELETE FROM qb_questions WHERE fk_quiz_id=%s", GetSQLValueString($_GET['id'], "int"));
	//echo "<br>".$deleteSQL;
	mysql_select_db($database_kuizzroo, $kuizzroo);
	mysql_query($deleteSQL, $kuizzroo) or die(mysql_error());
	
	// select pictures belonging to this quiz
	mysql_select_db($database_kuizzroo, $kuizzroo);
	$query_getQuizPictures = sprintf("SELECT quiz_picture FROM qb_quizzes WHERE quiz_id = %s", GetSQLValueString($_GET['id'], "int"));
	$getQuizPictures = mysql_query($query_getQuizPictures, $kuizzroo) or die(mysql_error());
	$row_getQuizPictures = mysql_fetch_assoc($getQuizPictures);
	$totalRows_getQuizPictures = mysql_num_rows($getQuizPictures);
	
	// loop and remove pictures belonging to this quiz
	do{
		if($row_getQuizPictures['quiz_picture'] != "none.gif"){
			unlink("quiz_images/".$row_getQuizPictures['quiz_picture']);
		}
	}while($row_getQuizPictures = mysql_fetch_assoc($getQuizPictures));
	mysql_free_result($getQuizPictures);
	
	// delete question
	$deleteSQL = sprintf("DELETE FROM qb_questions WHERE fk_quiz_id=%s", GetSQLValueString($_GET['id'], "int"));
	//echo "<br>".$deleteSQL;
	mysql_select_db($database_kuizzroo, $kuizzroo);
	mysql_query($deleteSQL, $kuizzroo) or die(mysql_error());
	
	// delete question
	$deleteSQL = sprintf("DELETE FROM qb_quizzes WHERE quiz_id=%s", GetSQLValueString($_GET['id'], "int"));
	//echo "<br>".$deleteSQL;
	mysql_select_db($database_kuizzroo, $kuizzroo);
	mysql_query($deleteSQL, $kuizzroo) or die(mysql_error());
	
	mysql_free_result($getQuestions);

	$deleteGoTo = "manageQuiz.php?msg=".urlencode("You have deleted a quiz");
	header(sprintf("Location: %s", $deleteGoTo));
}
?>
