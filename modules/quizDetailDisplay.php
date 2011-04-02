<?php
require('../modules/quizrooDB.php'); 
require('../modules/quiz.php');

// check whether such a quiz exists
$quiz = new Quiz($_GET['id']);

if($quiz->exists()){
	// check if member is the owner of this quiz
	if($quiz->isOwner($member->id)){
		// prepare the formatted status
		switch($quiz->isPublished){
			case 0: $status = '<span class="draft">Unpublished Draft</span>'; break;
			case 1: $status = '<span class="published">Published</span>'; break;
			case 2: $status = '<span class="modified">Unpublished Modification</span>'; break;
			case 3: $status = '<span class="archived">Archived</span>'; break;
			default: $status = 'Limbo :/'; break;
		}
		
		// get average options
		// check the number of questions
		$numQuestions = $quiz->getQuestions("count");
		// check the number of options
		$listQuestion = explode(',', $quiz->getQuestions());
		$totalOptions = 0;
		
		if($numQuestions != 0){
			$questionState = true;
			$optionState = true;
			foreach($listQuestion as $question){
				// check the number of options for this question
				$numOptions = $quiz->getOptions($question, "count");
				if($numOptions < $VAR_QUIZ_MIN_OPTIONS){
					$optionState = false;
				}
				$totalOptions += $numOptions;
			}
		}
		
		if($numQuestions != 0){
			$averageOptionCount = $totalOptions / $numQuestions;
		}else{
			$averageOptionCount = 0;
		}
		
		// get results to build the pie chart
		$query_getResultChart = sprintf("SELECT COUNT(*) AS count, result_title FROM q_store_result, q_results WHERE q_store_result.fk_quiz_id = %d AND result_id = fk_result_id GROUP BY fk_result_id", $quiz->quiz_id);
		$getResultChart = mysql_query($query_getResultChart, $quizroo) or die(mysql_error());
		$row_getResultChart = mysql_fetch_assoc($getResultChart);
		$totalRows_getResultChart = mysql_num_rows($getResultChart);
		
		// take quiz history
		$takeQuizQuery = sprintf("SELECT count, r.takeDate FROM (SELECT takeDate FROM (SELECT DATE(timestamp) AS takeDate FROM `q_store_result` GROUP BY DATE(timestamp) ORDER BY takeDate DESC LIMIT 0, 7) t ORDER BY takeDate) r LEFT JOIN (SELECT COUNT(store_id) AS count, DATE(timestamp) As takeDate FROM `q_store_result` WHERE `fk_quiz_id` = %s GROUP BY DATE(timestamp)
		) a ON r.takedate = a.takeDate", $quiz->quiz_id);
		$getTakeQuiz = mysql_query($takeQuizQuery, $quizroo) or die(mysql_error());
		$row_getTakeQuiz = mysql_fetch_assoc($getTakeQuiz);
		$totalRows_getTakeQuiz = mysql_num_rows($getTakeQuiz);
		$count = 0;
		
		// quiz taker's log
		$quizLogQuery = sprintf("SELECT member_id, member_name, result_title, `timestamp` FROM s_members m, q_store_result s, q_results r WHERE s.fk_member_id = m.member_id AND s.fk_result_id = r.result_id AND s.fk_quiz_id = %s GROUP BY member_name ORDER BY `timestamp` DESC LIMIT 0, 10", $quiz->quiz_id);
		$getquizLog = mysql_query($quizLogQuery, $quizroo) or die(mysql_error());
		$row_getquizLog = mysql_fetch_assoc($getquizLog);
		$totalRows_getquizLog = mysql_num_rows($getquizLog);
		$count = 0;
	}
}
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {'packages':['corechart']});
$(document).ready(function(){
	google.setOnLoadCallback(function(){
		drawCharts();
	});
	
	function drawCharts() {
		<?php if($totalRows_getResultChart != 0){ ?>
		drawTopicBreakdownChart();
		<?php } ?>
		drawTakeQuizHistoryChart();
	}
	
	function drawTopicBreakdownChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Result');
		data.addColumn('number', 'People');
		data.addRows([
			<?php
			$chartData = "";
			do{
				$chartData .= "['".str_replace("'", "\\'", $row_getResultChart['result_title'])."', ".$row_getResultChart['count']."],";
			}while($row_getResultChart = mysql_fetch_assoc($getResultChart));
			echo substr($chartData, 0, -1);
			 ?>
		]);
		
		var chart = new google.visualization.PieChart(document.getElementById('topic_chart'));
		chart.draw(data, {width: 540, height: 250, title: 'Attempts per Result', backgroundColor:'transparent'});
	}
	
	function drawTakeQuizHistoryChart(){
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
		data.addColumn('number', 'Quiz Attempts');
		data.addRows(<?php echo $totalRows_getTakeQuiz; ?>);
		
		<?php do{ ?>
		data.setValue(<?php echo $count; ?>, 0, '<?php echo date("F j", strtotime($row_getTakeQuiz['takeDate'])); ?>');
		data.setValue(<?php echo $count; ?>, 1, <?php echo ($row_getTakeQuiz['count']== NULL) ? 0: $row_getTakeQuiz['count'] ; ?>);
		<?php $count++; }while($row_getTakeQuiz = mysql_fetch_assoc($getTakeQuiz)); ?>
	
		var chart = new google.visualization.LineChart(document.getElementById('takeHistory_chart'));
		chart.draw(data, {width: 540, height: 240, title: 'Quiz attempts over the past week', legend: 'none', backgroundColor:'transparent'});
	}
});
</script>
<?php if($quiz->exists() == false){ ?>
<div id="viewMember-preamble" class="framePanel rounded">
  <h2>Quiz not found!</h2>
  <div class="content-container">
  <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
  <p>Sorry! The quiz you are looking for does not seem to be in our system! Are you sure you have followed the right link?</p>
  </div>
</div>
<?php }else{ if($quiz->isOwner($member->id) == false){ ?>
<div id="viewMember-preamble" class="framePanel rounded">
  <h2>You're not the owner!</h2>
  <div class="content-container">
  <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
  <p>Sorry! It seems that you did not create this quiz! Only quiz owners can see their quiz statistics. Are you sure you have followed the right link?</p>
  </div>
</div>
<?php }else{ ?>
<div id="viewMember-preamble" class="framePanel rounded">
  <h2>Quiz Information</h2>
  <div class="content-container">
  <p>Here's some detailed statistics about your quiz, &quot;<strong><?php echo $quiz->quiz_name; ?></strong>&quot;, which was created on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?>. It's current status is <em><?php echo $status; ?></em>.</p>
  </div>
</div>
<div class="clear">
  <div id="fun-facts" class="framePanel rounded left">
    <h2>Statistics</h2>
    <div class="content-container">
    <p class="fact">This quiz has</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
	  <div class="factValue"><?php echo $quiz->getAttempts(); ?></div>
    <p class="factDesc">Quiz Attempts</p></div>
    <div class="factbox rounded">
      <p class="unit">with </p>
      <div class="factValue"><?php echo $quiz->getAttempts(true); ?></div>
      <p class="factDesc">Unique Attempts</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">has gathered</p>
      <div class="factValue"><?php echo $quiz->quiz_score ?></div>
      <p class="factDesc">Points</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">has gathered</p>
      <div class="factValue"><?php echo $quiz->likes ?></div>
      <p class="factDesc">Likes</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">has</p>
      <div class="factValue"><?php echo $quiz->getQuestions('count'); ?></div>
      <p class="factDesc">Questions</p></div>
    <div class="factbox rounded">
      <p class="unit">has a average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $averageOptionCount); ?></div>
    <p class="factDesc">Options</p></div>
    <div class="factbox rounded">
      <p class="unit">has</p>
      <div class="factValue"><?php echo $quiz->getResults('count'); ?></div>
      <p class="factDesc">Results</p>
    </div>
    </div>
  </div>
  <?php if($totalRows_getResultChart != 0){ ?>
  <div id="topic-breakdown" class="framePanel rounded right">
    <h2>Quiz Results Breakdown</h2>
    <div class="content-container">
    <div id="topic_chart"><div id="loader-box"><img src="../webroot/img/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div></div>
    </div>
  </div>
  <?php } ?>
  <div id="taking-history" class="framePanel rounded right">
    <h2>Quiz Activity History</h2>
    <div class="content-container">
    <div id="takeHistory_chart"><div id="loader-box"><img src="../webroot/img/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div></div>
    </div>
  </div>
  <?php if($totalRows_getquizLog != 0){ ?>
  <div id="ranking" class="framePanel rounded right">
    <h2>Latest 10 Quiz Takers</h2>
    <div class="content-container">
    <table width="100%" border="0" cellpadding="4" cellspacing="0" id="rankTable">
      <tr>
        <th scope="col">Taker</th>
        <th width="200" scope="col">Result</th>
        <th width="180" scope="col">Taken on</th>
      </tr>
      <?php do{ ?>
      <tr>
        <td><a href="viewMember.php?id=<?php echo $row_getquizLog['member_id']; ?>"><?php echo $row_getquizLog['member_name']; ?></a></td>
        <td width="200"><?php echo ($row_getquizLog['result_title'] > 80) ? substr($row_getquizLog['result_title'], 0, 80)."..." : $row_getquizLog['result_title']; ?></td>
        <td width="180"><?php echo date("F j, Y g:ia", strtotime($row_getquizLog['timestamp'])); ?></td>
      </tr>
      <? }while($row_getquizLog = mysql_fetch_assoc($getquizLog)); ?>
    </table>
  </div>
  <?php } ?>
  </div>
</div>
<?php }} ?>
