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

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getResultInfo = "SELECT * FROM qb_results WHERE result_id = ".$row_getResults['fk_result'];
$getResultInfo = mysql_query($query_getResultInfo, $kuizzroo) or die(mysql_error());
$row_getResultInfo = mysql_fetch_assoc($getResultInfo);
$totalRows_getResultInfo = mysql_num_rows($getResultInfo);

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_saveResult = "INSERT INTO qb_store_result(fk_quiz_id, fk_result_id) VALUES (".GetSQLValueString($quiz_id, "int").", ".$row_getResults['fk_result'].")";
mysql_query($query_saveResult, $kuizzroo) or die(mysql_error());

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getResultChart = sprintf("SELECT COUNT(*) AS count, result_title FROM qb_store_result, qb_results WHERE qb_store_result.fk_quiz_id = %d AND result_id = fk_result_id GROUP BY fk_result_id", GetSQLValueString($quiz_id, "int"));
$getResultChart = mysql_query($query_getResultChart, $kuizzroo) or die(mysql_error());
$row_getResultChart = mysql_fetch_assoc($getResultChart);
$totalRows_getResultChart = mysql_num_rows($getResultChart);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - create, publish and share quizzes online!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {'packages':['corechart']});
	google.load("jquery", "1.4.2");
	google.load("jqueryui", "1.8.5");
	
	google.setOnLoadCallback(function(){
		drawCharts();
	});
	
	function drawCharts() {
		drawDeviceChart();
	}
	
	function drawDeviceChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Result');
		data.addColumn('number', 'People');
		data.addRows([
			
			<?php do{ ?>			
			['<?php echo $row_getResultChart['result_title']; ?>', <?php echo $row_getResultChart['count']; ?>],
			<?php }while($row_getResultChart = mysql_fetch_assoc($getResultChart)); ?>
			
		]);
		
		var chart = new google.visualization.PieChart(document.getElementById('result_chart'));
		chart.draw(data, {width: 700, height: 300, title: 'People per Result'});
	}
</script>
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_quiz_result">Create a new quiz</h3>
    Here's the result of the quiz! Do remember to rate the quiz below. You can also see how others have fared while taking this quiz.</div></div>
<div id="contentContainer">
  <div id="content">
    <div id="quizPreview">
      <h2><?php echo $row_getResultInfo['result_title']; ?></h2>
      <p><img src="quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getResultInfo['result_picture']; ?>" width="320" height="213" alt="" /></p>
      <p class="description"><?php echo $row_getResultInfo['result_description']; ?></p>
      <div id="result_chart"><img src="images/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div>
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($getResults);

mysql_free_result($getResultChart);

mysql_free_result($getResultInfo);
?>
