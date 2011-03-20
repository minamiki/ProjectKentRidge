<?php
require('../modules/quizrooDB.php'); 

// get the member rank
$member_rank = $member->getRanking();

// the rank above
$row_getAboveRank = $member->getLeaderBoardStat($member_rank-1);
// member's rank
$row_getMemRank = $member->getLeaderBoardStat($member_rank);
// the rank below
$row_getBelowRank = $member->getLeaderBoardStat($member_rank+1);

// total quizzes
$quiz_total = $member->getStats('quizzes_total');

// topic pie chart
$topicQuery = sprintf("SELECT COUNT(fk_quiz_cat) AS count, cat_name FROM (SELECT store_id, fk_quiz_id, fk_quiz_cat, cat_name FROM q_store_result, q_quizzes, q_quiz_cat WHERE q_quizzes.quiz_id = q_store_result.fk_quiz_id AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat AND q_store_result.fk_member_id = %s GROUP BY q_store_result.fk_quiz_id) t GROUP BY fk_quiz_cat", $member->id);
$getTopics = mysql_query($topicQuery, $quizroo) or die(mysql_error());
$row_getTopics = mysql_fetch_assoc($getTopics);
$totalRows_getTopics = mysql_num_rows($getTopics);

// take quiz history
$takeQuizQuery = sprintf("SELECT count, r.takeDate FROM (SELECT takeDate FROM (SELECT DATE(timestamp) AS takeDate FROM `q_store_result` GROUP BY DATE(timestamp) ORDER BY takeDate DESC LIMIT 0, 7) t ORDER BY takeDate) r LEFT JOIN (SELECT COUNT(store_id) AS count, DATE(timestamp) As takeDate FROM `q_store_result` WHERE `fk_member_id` = %s GROUP BY DATE(timestamp)
) a ON r.takedate = a.takeDate", $member->id);
$getTakeQuiz = mysql_query($takeQuizQuery, $quizroo) or die(mysql_error());
$row_getTakeQuiz = mysql_fetch_assoc($getTakeQuiz);
$totalRows_getTakeQuiz = mysql_num_rows($getTakeQuiz);
$count = 0;
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
		drawTopicBreakdownChart();
		drawTakeQuizHistoryChart();
	}
	
	function drawTopicBreakdownChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Topic');
		data.addColumn('number', 'Attempts');
		data.addRows([
			
			<?php do{ ?>			
			['<?php echo $row_getTopics['cat_name']; ?>', <?php echo $row_getTopics['count']; ?>],
			<?php }while($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
			
		]);
		
		var chart = new google.visualization.PieChart(document.getElementById('topic_chart'));
		chart.draw(data, {width: 540, height: 250, title: 'Attempts per Topic', backgroundColor:'transparent'});
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
        chart.draw(data, {width: 540, height: 240, title: 'Quizzes Taken over the past week', legend: 'none', backgroundColor:'transparent'});
	}
</script>
<div id="statistics-preamble" class="frame rounded">
  <h2>Statistics</h2>
  <p>Get detail reports on your activity on Quizroo! Your ranking shows your rank on the Quizroo Leaderboard. Try to overtake the player just above you! The quiz topic and history charts provides interesting information on your quiz taking habits.</p>
</div>
<div class="clear">
  <div id="fun-facts" class="frame rounded left">
    <h2>Fun Facts</h2>
    <p class="fact">You have created</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
      <div class="factValue"><?php echo sprintf("%d", $quiz_total) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">with </p>
      <div class="factValue"><?php echo sprintf("%d", $member->getStats('quizzes_published')) ?></div>
      <p class="factDesc">Published</p>
    </div>
    <p class="fact">You have</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
	  <div class="factValue"><?php echo sprintf("%d", $member->getStats('taken_quizzes_total')) ?></div>
      <p class="factDesc">Quiz Attempts</p></div>
    <div class="factbox rounded">
      <p class="unit">with </p>
      <div class="factValue"><?php echo sprintf("%d", $member->getStats('taken_quizzes_unique')) ?></div>
      <p class="factDesc">Unique Attempts</p>
    </div>
    <p class="fact">Your quizzes
    </p><div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $member->getStats('questions')/$quiz_total) ?></div>
      <p class="factDesc">Questions</p></div>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $member->getStats('options')/$quiz_total) ?></div>
    <p class="factDesc">Options</p></div>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $member->getStats('likes')/$quiz_total) ?></div>
      <p class="factDesc">Likes</p>
    </div>
  </div>
  <div id="ranking" class="frame rounded right">
    <h2>Your Ranking</h2>
    <table width="100%" border="0" cellpadding="4" cellspacing="0" id="rankTable">
      <tr>
        <th width="55" scope="col">Rank</th>
        <th width="60" scope="col">&nbsp;</th>
        <th align="left" scope="col">Member</th>
        <th scope="col">Score</th>
      </tr>
      <?php if($member_rank != 1){ ?>
      <tr class="compare-box">
        <td align="center" scope="row" class="ranking compare"><?php echo $row_getAboveRank['ranking']; ?></td>
        <td align="center" scope="row"><img src="http://graph.facebook.com/<?php echo $row_getAboveRank['member_id']; ?>/picture" alt="<?php echo $row_getAboveRank['member_name']; ?>" width="50" height="50" class="compareImg" title="<?php echo $row_getAboveRank['member_name']; ?>" /></td>
        <td><p class="member-name compare"><?php echo $row_getAboveRank['member_name']; ?></p>
        <p class="member-level compare"><?php echo $row_getAboveRank['rank_name']; ?> (Level <?php echo $row_getAboveRank['level']; ?>)</p></td>
        <td align="center"><p class="score compare" title="Psss.. extra credit of <?php echo sprintf("%.2f", ($row_getAboveRank['quizcreator_score'] > 0) ? log($row_getAboveRank['quizcreator_score'])/5 : 0); ?>"><?php echo $row_getAboveRank['score']; ?></p>
        <p class="breakdown-score compare">Taker: <?php echo $row_getAboveRank['quiztaker_score']; ?>, Creator: <?php echo $row_getAboveRank['quizcreator_score']; ?></p></td>
      </tr>
      <?php } ?>
      <tr>
        <td width="55" align="center" scope="row" class="ranking"><?php echo $row_getMemRank['ranking']; ?></td>
        <td width="60" align="center" scope="row"><img src="http://graph.facebook.com/<?php echo $row_getMemRank['member_id']; ?>/picture" width="50" height="50" alt="<?php echo $row_getMemRank['member_name']; ?>" title="<?php echo $row_getMemRank['member_name']; ?>" /></td>
        <td><p class="member-name"><?php echo $row_getMemRank['member_name']; ?></p>
        <p class="member-level"><?php echo $row_getMemRank['rank_name']; ?> (Level <?php echo $row_getMemRank['level']; ?>)</p></td>
        <td align="center"><p class="score" title="Psss.. extra credit of <?php echo sprintf("%.2f", ($row_getMemRank['quizcreator_score'] > 0) ? log($row_getMemRank['quizcreator_score'])/5 : 0); ?>"><?php echo $row_getMemRank['score']; ?></p>
        <p class="breakdown-score">Taker: <?php echo $row_getMemRank['quiztaker_score']; ?>, Creator: <?php echo $row_getMemRank['quizcreator_score']; ?></p></td>
      </tr>
      <?php if($row_getBelowRank != NULL){ ?>
      <tr class="compare-box">
        <td align="center" scope="row" class="ranking compare"><?php echo $row_getBelowRank['ranking']; ?></td>
        <td align="center" scope="row"><img src="http://graph.facebook.com/<?php echo $row_getBelowRank['member_id']; ?>/picture" alt="<?php echo $row_getBelowRank['member_name']; ?>" width="50" height="50" class="compareImg" title="<?php echo $row_getBelowRank['member_name']; ?>" /></td>
        <td><p class="member-name compare"><?php echo $row_getBelowRank['member_name']; ?></p>
          <p class="member-level compare"><?php echo $row_getBelowRank['rank_name']; ?> (Level <?php echo $row_getBelowRank['level']; ?>)</p></td>
        <td align="center"><p class="score compare" title="Psss.. extra credit of <?php echo sprintf("%.2f", ($row_getBelowRank['quizcreator_score'] > 0) ? log($row_getBelowRank['quizcreator_score'])/5 : 0); ?>"><?php echo $row_getBelowRank['score']; ?></p>
          <p class="breakdown-score compare">Taker: <?php echo $row_getBelowRank['quiztaker_score']; ?>, Creator: <?php echo $row_getBelowRank['quizcreator_score']; ?></p></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div class="frame rounded right">
    <h2>Quiz Taking Topic Breakdown</h2>
    <div id="topic_chart"></div>
  </div>
  <div class="frame rounded right">
    <h2>Quiz Taking History</h2>
    <div id="takeHistory_chart"></div>
  </div>
</div>
