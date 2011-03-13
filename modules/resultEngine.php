<?php
require('../modules/quizrooDB.php'); 
require('../modules/variables.php');
require('../modules/checkAchievements.php');

// get the member's facebook id
$facebookID = $member->id;

//----------------------------------------
// Process the quiz results
//----------------------------------------

// find out the number of questions
$totalQuestionCount = $quiz->numQuestions();

// iterate and collect the final answers for each question
$validate = true;
$answers = "";
for($i = 0; $i < $totalQuestionCount; $i++){
	if(isset($_POST['q'.($i+1)])){
		$answers .= $_POST['q'.($i+1)].",";
	}else{
		$validate = false;
	}
}
// check if post data is invalid
if(!$validate){
	// invalid data, redirect to home
	header("Location: index.php");
}

// caculate and order the final result from the sum of options and their weightage
$query_getResults = "SELECT fk_result, SUM(option_weightage) AS count FROM q_options WHERE option_id IN (".substr($answers, 0, strlen($answers)-1).") GROUP BY fk_result ORDER BY count DESC LIMIT 0,1";
$getResults = mysql_query($query_getResults, $quizroo) or die(mysql_error());
$row_getResults = mysql_fetch_assoc($getResults);
$totalRows_getResults = mysql_num_rows($getResults);

// check if the quiz is published
if($quiz->isPublished()){
	// award the quiz creator the base points
	if(!$quiz->hasTaken($member->id)){
		$quiz->awardPoints(0, $member->id);
	}
	
	// log the quiz taking attempt
	$query_saveResult = sprintf("INSERT INTO q_store_result(fk_quiz_id, fk_result_id, fk_member_id) VALUES (%d, %d, %d)", GetSQLValueString($quiz->quiz_id, "int"), $row_getResults['fk_result'], $facebookID);
	mysql_query($query_saveResult, $quizroo) or die(mysql_error());
}

//----------------------------------------
// Insert attempt timings into database
// - TODO: What to do with the timings
//----------------------------------------

// get the attempt timings
$logtime = explode(',', $_POST['logtime']);
$PHPstartTime = $logtime[1] * 1000;	// the logged PHP server timestamp
$JSstartTime = $logtime[2];			// the logged Javascript timestamp
// prepare the log array
$logArray = array();				// to be stored
for($i = 0, $j = 3; $i < sizeof($logtime)/3 - 1; $i++, $j+=3){
	$logArray[$i] = array($logtime[$j], $logtime[$j+1], ($PHPstartTime + $logtime[$j+2] - $JSstartTime)/1000);
}

//----------------------------------------
// Calculate points and achievements
//----------------------------------------

// prepare the achievement array for possible multiple achievements
$achievement_array = array();

// Calculate Points to award
$achievement_array = $member->calculatePoints($quiz->quiz_id, $quiz->isPublished(), $achievement_array);

// Check for achievements
$achievement_array = checkAchievements($facebookID, $achievement_array);

//----------------------------------------
// Retrieve Quiz results for display
//----------------------------------------

// select the result data
$query_getResultInfo = "SELECT * FROM q_results WHERE result_id = ".$row_getResults['fk_result'];
$getResultInfo = mysql_query($query_getResultInfo, $quizroo) or die(mysql_error());
$row_getResultInfo = mysql_fetch_assoc($getResultInfo);
$totalRows_getResultInfo = mysql_num_rows($getResultInfo);

// get results to build the pie chart
$query_getResultChart = sprintf("SELECT COUNT(*) AS count, result_title FROM q_store_result, q_results WHERE q_store_result.fk_quiz_id = %d AND result_id = fk_result_id GROUP BY fk_result_id", $quiz->quiz_id);
$getResultChart = mysql_query($query_getResultChart, $quizroo) or die(mysql_error());
$row_getResultChart = mysql_fetch_assoc($getResultChart);
$totalRows_getResultChart = mysql_num_rows($getResultChart);
?>
<?php if($quiz->isPublished() && $totalRows_getResultChart != 0){ ?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1', {'packages':['corechart']});
	google.load("jquery", "1.4.2");
	google.load("jqueryui", "1.8.5");
	
	google.setOnLoadCallback(function(){
		drawCharts();
		
		// Added here to account for charts
		$('#splash').height($('body').height());
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
			['<?php echo str_replace("'", "\\'", $row_getResultChart['result_title']); ?>', <?php echo $row_getResultChart['count']; ?>],
			<?php }while($row_getResultChart = mysql_fetch_assoc($getResultChart)); ?>
			
		]);
		
		var chart = new google.visualization.PieChart(document.getElementById('result_chart'));
		chart.draw(data, {width: 700, height: 300, title: 'People per Result', backgroundColor:'transparent'});
	}
</script>
<?php } ?>
<div class="frame rounded">
<h3 id="title_quiz_result">Quiz Results</h3>
Here's the result of the quiz! Do remember to rate the quiz below. You can also see how others have fared while taking this quiz.</div>
<div id="result-panel" class="frame rounded">
<h2><?php echo $row_getResultInfo['result_title']; ?></h2>
<?php if($row_getResultInfo['result_picture'] != "none.gif"){ ?>
<img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getResultInfo['result_picture']; ?>" width="320" height="213" alt="" /><?php } ?>
  <p class="description"><?php echo $row_getResultInfo['result_description']; ?></p>
</div>

<!-- Include user sharing interface for liking, posting feed and recommending to friends -->
<?php include('sharingInterface.php') ?>
<?php if($quiz->isPublished() && $totalRows_getResultChart != 0){ ?>
<div class="frame rounded">
<h3>Result Details</h3>
<div id="result_chart"><img src="../webroot/img/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div>
<?php if($VAR_SYSTEM_DEBUG){ ?>
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
    <td><?php echo $attempt[2]; ?></td>
  </tr>
  <?php } ?>
</table>
<?php } ?>
<?php } ?>

</div>
<?php
//----------------------------------------
// Display splash screen with results
//----------------------------------------
$achievement_details = retrieveAchievements($achievement_array);
?>
