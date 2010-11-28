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


// find out values

// Quiz Information
$quiz_id = $_POST['quiz_id'];
$quiz_title = $_POST['quiz_title'];
$quiz_description = $_POST['quiz_description'];
$quiz_cat = $_POST['quiz_cat'];

// Quiz Results
for($i = 0; $i < $_POST['resultCount']; $i++){
	$result_title[] = $_POST['result_title_'.($i+1)];
	$result_description[] = $_POST['result_description_'.($i+1)];
	$result_id[] = $_POST['id_r'.($i+1)];
}
// Quiz Questions
$questionArray = explode("_", $_POST['optionCounts']);
$question = array();
for($i = 0; $i < $_POST['questionCount']; $i++){
	$question_id[] = $_POST['q'.($i+1)];
	$question[][0] = $_POST['question_'.($i+1)];
	// Quiz Options
	for($j = 0; $j < $questionArray[$i]; $j++){
		$question[$i][1][$j][] = $_POST['q'.($i+1).'o'.($j+1)];
		$question[$i][1][$j][] = $_POST['q'.($i+1).'r'.($j+1)];
		$question[$i][1][$j][] = $_POST['q'.($i+1).'w'.($j+1)];
		$question[$i][1][$j][] = $_POST['id_q'.($i+1).'o'.($j+1)];
		
	}
}

$result_convertor = array();

/*
// debug
echo $quiz_title."<br>".$quiz_description."<br>".$quiz_cat;
echo "<br><br>";
print_r($result_title);
echo "<br>";
print_r($result_description);
echo "<br>";
print_r($result_id);
echo "<br><br>";
print_r($question);
echo "<br><br>";
print_r($question_id);
*/

mysql_select_db($database_kuizzroo, $kuizzroo);
// insert into the quiz table
$insertSQL = sprintf("UPDATE qb_quizzes SET `quiz_name` = %s, `quiz_description` = %s, `fk_quiz_cat` = %d WHERE `quiz_id` = %d",
				   GetSQLValueString($quiz_title, "text"),
				   GetSQLValueString($quiz_description, "text"),
				   GetSQLValueString($quiz_cat, "int"),
				   GetSQLValueString($quiz_id, "int"));
//echo "<br>".$insertSQL;
mysql_query($insertSQL, $kuizzroo) or die(mysql_error());

// Insert the results
for($i = 0; $i < $_POST['resultCount']; $i++){
	$insertSQL = sprintf("UPDATE qb_results SET `result_title` = %s, `result_description` = %s WHERE `result_id` = %d",
				   GetSQLValueString($result_title[$i], "text"),
				   GetSQLValueString($result_description[$i], "text"),
				   GetSQLValueString($result_id[$i], "int"));
	//echo "<br>".$insertSQL;
	mysql_query($insertSQL, $kuizzroo) or die(mysql_error());
}

// Insert the questions
for($i = 0; $i < $_POST['questionCount']; $i++){
	$insertSQL = sprintf("UPDATE qb_questions SET `question` = %s WHERE `question_id` = %d",
				   GetSQLValueString($question[$i][0], "text"),
				   GetSQLValueString($question_id[$i], "int"));
	//echo "<br>".$insertSQL;
	mysql_query($insertSQL, $kuizzroo) or die(mysql_error());

	// Insert the Quiz Options
	for($j = 0; $j < $questionArray[$i]; $j++){
		$insertSQL = sprintf("UPDATE qb_options SET `option` = %s, `fk_result` = %d, `option_weightage` = %d WHERE `option_id` = %d",
					   GetSQLValueString($question[$i][1][$j][0], "text"),
					   GetSQLValueString($question[$i][1][$j][1], "int"),
					   GetSQLValueString($question[$i][1][$j][2], "int"),
					   GetSQLValueString($question[$i][1][$j][3], "int"));
		//echo "<br>".$insertSQL;
		mysql_query($insertSQL, $kuizzroo) or die(mysql_error());
	}
}

$insertGoTo = "manageQuiz.php?msg=".urlencode("Quiz updated successfully");
header(sprintf("Location: %s", $insertGoTo));

?>