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


// Ignore who this is coming from for now. Quiz takers are anonoymous

// get the quiz id
$quiz_id = $_POST['quiz_id'];

// find out the number of questions
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuestionCount = "SELECT question_id FROM qb_questions WHERE fk_quiz_id = ".GetSQLValueString($quiz_id, "int");
$getQuestionCount = mysql_query($query_getQuestionCount, $kuizzroo) or die(mysql_error());
$row_getQuestionCount = mysql_fetch_assoc($getQuestionCount);
$totalRows_getQuestionCount = mysql_num_rows($getQuestionCount);

$answers = "";
for($i = 0; $i < $totalRows_getQuestionCount; $i++){
	$answers .= $_POST['q'.($i+1)].",";
}

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getResults = "SELECT fk_result, SUM(option_weightage) AS count FROM qb_options WHERE option_id IN (".substr($answers, 0, strlen($answers)-1).") GROUP BY fk_result ORDER BY count DESC LIMIT 0,1";
$getResults = mysql_query($query_getResults, $kuizzroo) or die(mysql_error());
$row_getResults = mysql_fetch_assoc($getResults);
$totalRows_getResults = mysql_num_rows($getResults);

echo $row_getResults['fk_result'];
?>
