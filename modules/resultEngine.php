<?php require_once('../Connections/quizroo.php'); ?>
<?php
// TODO: Indentifying quiz takers with from facebook API
$member_id = 0;

//----------------------------------------
// Process the quiz results
//----------------------------------------

// get the quiz id
$quiz_id = $_POST['quiz_id'];

// find out the number of questions
mysql_select_db($database_quizroo, $quizroo);
$query_getQuestionCount = "SELECT question_id FROM q_questions WHERE fk_quiz_id = ".GetSQLValueString($quiz_id, "int");
$getQuestionCount = mysql_query($query_getQuestionCount, $quizroo) or die(mysql_error());
$row_getQuestionCount = mysql_fetch_assoc($getQuestionCount);
$totalRows_getQuestionCount = mysql_num_rows($getQuestionCount);

// iterate and collect the final answers for each question
$answers = "";
for($i = 0; $i < $totalRows_getQuestionCount; $i++){
	$answers .= $_POST['q'.($i+1)].",";
}

// caculate and order the final result from the sum of options and their weightage
$query_getResults = "SELECT fk_result, SUM(option_weightage) AS count FROM q_options WHERE option_id IN (".substr($answers, 0, strlen($answers)-1).") GROUP BY fk_result ORDER BY count DESC LIMIT 0,1";
$getResults = mysql_query($query_getResults, $quizroo) or die(mysql_error());
$row_getResults = mysql_fetch_assoc($getResults);
$totalRows_getResults = mysql_num_rows($getResults);

// select the result data
$query_getResultInfo = "SELECT * FROM q_results WHERE result_id = ".$row_getResults['fk_result'];
$getResultInfo = mysql_query($query_getResultInfo, $quizroo) or die(mysql_error());
$row_getResultInfo = mysql_fetch_assoc($getResultInfo);
$totalRows_getResultInfo = mysql_num_rows($getResultInfo);

// store the final result into the database
$query_saveResult = sprintf("INSERT INTO q_store_result(fk_quiz_id, fk_result_id, fk_member_id) VALUES (%d, %d, %d)", GetSQLValueString($quiz_id, "int"), $row_getResults['fk_result'], $member_id);
mysql_query($query_saveResult, $quizroo) or die(mysql_error());

// get results to build the pie chart
$query_getResultChart = sprintf("SELECT COUNT(*) AS count, result_title FROM q_store_result, q_results WHERE q_store_result.fk_quiz_id = %d AND result_id = fk_result_id GROUP BY fk_result_id", GetSQLValueString($quiz_id, "int"));
$getResultChart = mysql_query($query_getResultChart, $quizroo) or die(mysql_error());
$row_getResultChart = mysql_fetch_assoc($getResultChart);
$totalRows_getResultChart = mysql_num_rows($getResultChart);

// get the attempt timings
$logtime = explode(',', $_POST['logtime']);
$logArray = array();
for($i = 0, $j = 3; $i < sizeof($logtime)/3 - 1; $i++, $j+=3){
	$logArray[$i] = array($logtime[$j], $logtime[$j+1], $logtime[$j+2]);
}
$PHPstartTime = $logtime[1] * 1000;
$JSstartTime = $logtime[2];

// TODO: Insert attempt timings into database
?>
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
<div class="frame rounded">
<h3 id="title_quiz_result">Quiz Results</h3>
Here's the result of the quiz! Do remember to rate the quiz below. You can also see how others have fared while taking this quiz.</div>
<div id="result-panel" class="frame rounded">
<h2><?php echo $row_getResultInfo['result_title']; ?></h2>
<img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getResultInfo['result_picture']; ?>" width="320" height="213" alt="" />
  <p class="description"><?php echo $row_getResultInfo['result_description']; ?></p>
</div>
<div class="frame rounded">
<h3>Result Details</h3>
<div id="result_chart"><img src="../webroot/images/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div>
<table border="0" align="center" cellpadding="3" cellspacing="0">
  <tr>
    <th scope="col">Question</th>
    <th scope="col">Option</th>
    <th scope="col">Time</th>
  </tr>
  <?php foreach($logArray as $attempt){ ?>
  <tr>
    <td><?php echo $attempt[0]; ?></td>
    <td><?php echo $attempt[1]; ?></td>
    <td><?php echo date("F j, Y H:i:s", ($PHPstartTime + $attempt[2] - $JSstartTime)/1000); ?></td>
  </tr>
  <?php } ?>
</table>

</div>
<?php
mysql_free_result($getResults);
mysql_free_result($getResultChart);
mysql_free_result($getResultInfo);
?>
