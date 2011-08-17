<!-- 
This page is to display information of the member that the user want to view.
Information includes statistic pie chart, taking quiz history, taking quiz topic break down and ranking
If the member is user's friend, detailed information will be display.
-->
<?php
<<<<<<< HEAD
//Display profile page of a particular member - includes rank, fun facts, quizzes created
//If member is a friend, it will also include a chart of the quizzes taken by him/her
//http://localhost/quizroo/webroot/viewMember.php
require('../modules/quizrooDB.php'); 
=======
require('../modules/quizrooDB.php'); //database connection
>>>>>>> 849f69f6d3b766ea1983e93d8922133b33cbd7ac

// check whether such a member exists
$view_member = new Member($_GET['id']);
// if the member exists, retrieve quizzes and get member data
if($view_member->memExist){
	// retrieve recommended quizzes
	$query_latest = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, dislikes FROM q_quizzes, q_quiz_cat, s_members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 AND fk_member_id = %s ORDER BY creation_date DESC LIMIT 0, %d", $view_member->id, 3);
	$latest = mysql_query($query_latest, $quizroo) or die(mysql_error());
	$row_latest = mysql_fetch_assoc($latest);
	$totalRows_latest = mysql_num_rows($latest);
	
	// retrieve popular quizzes
	$query_popular = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, dislikes FROM q_quizzes, q_quiz_cat, s_members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 AND fk_member_id = %s ORDER BY quiz_score DESC LIMIT 0, %d", $view_member->id, 3);
	$popular = mysql_query($query_popular, $quizroo) or die(mysql_error());
	$row_popular = mysql_fetch_assoc($popular);
	$totalRows_popular = mysql_num_rows($popular);
	
	// get the member rank
	$member_rank = $view_member->getRanking();
	
	// get the gender of the member
	$member_graph = getCURL('http://graph.facebook.com/'.$view_member->id);
	$member_dump = json_decode($member_graph['content']);
	
	// switch pronoun according to gender
	switch($member_dump->gender){
		case "male":
		$gender = array("he", "his"); break;
		case "female":
		$gender = array("she", "her"); break;
		default:
		$gender = "";
	}
	
	// check if this member is a friend or self
	$self = false;
	if($member->isFriend($view_member->id) || $member->id == $view_member->id){
		$friend = true;
		if($member->id == $view_member->id){
			$self = true;
		}
	}else{
		$friend = false;
	}

	// view member of the rank above
	$row_getAboveRank = $view_member->getLeaderBoardStat($member_rank-1);
	// member's rank
	$row_getMemRank = $view_member->getLeaderBoardStat($member_rank);
	// view member of the rank below
	$row_getBelowRank = $view_member->getLeaderBoardStat($member_rank+1);

	// display total quizzes
	$quiz_total = $view_member->getStats('quizzes_total');
	
	if($quiz_total != 0){
		$question_avg = sprintf("%.2f", $view_member->getStats('questions')/$quiz_total);
		$option_avg = sprintf("%.2f", $view_member->getStats('options')/$quiz_total);
		$like_avg = sprintf("%.2f", $view_member->getStats('likes')/$quiz_total);
	}else{
		$question_avg = 0;
		$option_avg = 0;
		$like_avg = 0;
	}
	
	// if the member is friend, display topic pie chart and take quiz history of the friend (view_member)
	if($friend){
		// topic pie chart
		$topicQuery = sprintf("SELECT COUNT(fk_quiz_cat) AS count, cat_name FROM (SELECT store_id, fk_quiz_id, fk_quiz_cat, cat_name FROM q_store_result, q_quizzes, q_quiz_cat WHERE q_quizzes.quiz_id = q_store_result.fk_quiz_id AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat AND q_store_result.fk_member_id = %s GROUP BY q_store_result.fk_quiz_id) t GROUP BY fk_quiz_cat", $view_member->id);
		$getTopics = mysql_query($topicQuery, $quizroo) or die(mysql_error());
		$row_getTopics = mysql_fetch_assoc($getTopics);
		$totalRows_getTopics = mysql_num_rows($getTopics);
		
		// take quiz history
		$takeQuizQuery = sprintf("SELECT count, r.takeDate FROM (SELECT takeDate FROM (SELECT DATE(timestamp) AS takeDate FROM `q_store_result` GROUP BY DATE(timestamp) ORDER BY takeDate DESC LIMIT 0, 7) t ORDER BY takeDate) r LEFT JOIN (SELECT COUNT(store_id) AS count, DATE(timestamp) As takeDate FROM `q_store_result` WHERE `fk_member_id` = %s GROUP BY DATE(timestamp)
		) a ON r.takedate = a.takeDate", $view_member->id);
		$getTakeQuiz = mysql_query($takeQuizQuery, $quizroo) or die(mysql_error());
		$row_getTakeQuiz = mysql_fetch_assoc($getTakeQuiz);
		$totalRows_getTakeQuiz = mysql_num_rows($getTakeQuiz);
		$count = 0;
	}
}
?>
<<<<<<< HEAD
<!-- If Friend - Draws Quiz Topic Breakdown Chart and Quiz Taking History Chart-->
<?php if($friend){ ?>
=======
<?php if($friend){ // javascript to draw chart if the member is friend ?>
>>>>>>> 849f69f6d3b766ea1983e93d8922133b33cbd7ac
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {'packages':['corechart']});
$(document).ready(function(){
	google.setOnLoadCallback(function(){
		drawCharts();
	});
	
	/************************
	* This function calls the main drawing functions for topic and take quiz history
	************************/
	function drawCharts() {
		drawTopicBreakdownChart();
		drawTakeQuizHistoryChart();
	}
	/************************
	* Draw chart for topic
	************************/
	function drawTopicBreakdownChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Topic');
		data.addColumn('number', 'Attempts');
		data.addRows([
			<?php
			$chartData = "";
			do{
				$chartData .= "['".$row_getTopics['cat_name']."', ".$row_getTopics['count']."],";
			}while($row_getTopics = mysql_fetch_assoc($getTopics));
			echo substr($chartData, 0, -1);
			 ?>
		]);
		
		var chart = new google.visualization.PieChart(document.getElementById('topic_chart'));
		chart.draw(data, {width: 540, height: 250, title: 'Attempts per Topic', backgroundColor:'transparent'});
	}
	/************************
	* Draw chart for information on take quiz history of the member
	************************/
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
<?php } ?>
<?php 
//if the member does not exist, display the message for user
if($view_member->memExist == false){ ?>
<div id="viewMember-preamble" class="framePanel rounded">
  <h2>Quizroo Member not found!</h2>
  <div class="content-container">
  <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
  <p>Sorry! The member you are looking for does not seem to be in our system! Are you sure you have followed the right link?</p>
  </div>
</div>
<?php }else{ ?>
<div id="viewMember-preamble" class="frame rounded">
  <h2><?php echo $view_member->qname; ?></h2>
  <?php if($friend){ ?>
  <?php if($self){ //display this message when the member is self ?>
  <p>Hey.. you're looking at yourself! In this case, this page is the same as your profile page! On top of that, you get to see your latest and popular quizzes though :)</p>  
  <?php }else{ // if the member is friend, not self ?>
  <p>Here's some information about your friend, <?php echo $view_member->qname; ?>. You can see the latest quizzes created by <?php echo $gender[1]; ?>. You can also see <?php echo $gender[1]; ?> quiz taking habits and history, just like your profile page!</p>
  <?php }}else{ // if the member is not friend, inform the user which information they can see ?>
  <p>Here's some information about this member. You can see the latest quizzes created by this member. You can see more information if this member is your friend! (In facebook)</p>  
  <?php } ?>
</div>
<<<<<<< HEAD
<!-- Latest & Popular quizzes that member created -->
=======
<!-- This division is to display the latest quizzes panel-->
>>>>>>> 849f69f6d3b766ea1983e93d8922133b33cbd7ac
<div id="member-quizzes" class="clear">
  <div id="latest-quizzes" class="framePanel rounded left-right">
    <h2>Latest Quizzes</h2>
    <div class="content-container">
      <?php if($totalRows_latest != 0){ do { ?>
        <div class="quiz_box clear">
          <h3><a href="previewQuiz.php?id=<?php echo $row_latest['quiz_id']; ?>"><?php echo $row_latest['quiz_name']; ?></a></h3>
          <div class="thumb_box">
            <a href="previewQuiz.php?id=<?php echo $row_latest['quiz_id']; ?>"><img src="../quiz_images/imgcrop.php?w=90&amp;h=68&amp;f=<?php echo $row_latest['quiz_picture']; ?>" alt="<?php echo $row_latest['quiz_description']; ?>" width="90" height="68" border="0" title="<?php echo $row_latest['quiz_description']; ?>" /></a></div>
          <div class="quiz_details">
            <p class="description"><?php echo substr($row_latest['quiz_description'], 0, 120).((strlen($row_latest['quiz_description']) < 120)? "" : "..."); ?></p>
            <p class="source">from <a href="topics.php?topic=<?php echo $row_latest['fk_quiz_cat']; ?>"><?php echo $row_latest['cat_name']; ?></a>  by <?php echo $row_latest['member_name']; ?></p>
			<?php if(!$GAME_ALLOW_DISLIKES){ if($row_latest['likes'] > 0){ ?>
            <p class="rating"><span class="like"><?php echo $row_latest['likes']; ?></span> <?php echo ($row_latest['likes'] > 1) ? "people like" : "person likes"; ?> this</p>
			<?php }}else{ ?><p class="rating"><span class="like"><?php echo $row_latest['likes']; ?></span> likes, <span class="dislike"><?php echo $row_latest['dislikes']; ?></span> dislikes</p><?php } ?>
          </div>
        </div>
        <?php } while ($row_latest = mysql_fetch_assoc($latest)); }else{ ?>
        <p>There are no latest quizzes!</p>
        <?php } ?>
      </div>
  </div>
  <!-- This division is to display the popular quizzes panel-->
  <div id="popular-quizzes" class="framePanel rounded left-right clear">
    <h2>Popular Quizzes</h2>
    <div class="content-container">
      <?php if($totalRows_popular != 0){ do { ?>
        <div class="quiz_box clear">
          <h3><a href="previewQuiz.php?id=<?php echo $row_popular['quiz_id']; ?>"><?php echo $row_popular['quiz_name']; ?></a></h3>
          <div class="thumb_box">
            <a href="previewQuiz.php?id=<?php echo $row_popular['quiz_id']; ?>"><img src="../quiz_images/imgcrop.php?w=90&amp;h=68&amp;f=<?php echo $row_popular['quiz_picture']; ?>" alt="<?php echo $row_popular['quiz_description']; ?>" width="90" height="68" border="0" title="<?php echo $row_popular['quiz_description']; ?>" /></a></div>
          <div class="quiz_details">
            <p class="description"><?php echo substr($row_popular['quiz_description'], 0, 120).((strlen($row_popular['quiz_description']) < 120)? "" : "..."); ?></p>
            <p class="source">from <a href="topics.php?topic=<?php echo $row_popular['fk_quiz_cat']; ?>"><?php echo $row_popular['cat_name']; ?></a>  by <?php echo $row_popular['member_name']; ?></p>
			<?php if(!$GAME_ALLOW_DISLIKES){ if($row_popular['likes'] > 0){ ?>
            <p class="rating"><span class="like"><?php echo $row_popular['likes']; ?></span> <?php echo ($row_popular['likes'] > 1) ? "people like" : "person likes"; ?> this</p>
			<?php }}else{ ?><p class="rating"><span class="like"><?php echo $row_popular['likes']; ?></span> likes, <span class="dislike"><?php echo $row_popular['dislikes']; ?></span> dislikes</p><?php } ?>
          </div>
        </div>
        <?php } while ($row_popular = mysql_fetch_assoc($popular)); }else{ ?>
        <p>There are no popular quizzes!</p>
        <?php } ?>
     </div>
  </div>
</div>
<<<<<<< HEAD
<!-- Fun facts of member -->
=======
<!-- This division is to display the fun fact of the view-member-->
>>>>>>> 849f69f6d3b766ea1983e93d8922133b33cbd7ac
<div class="clear">
  <div id="fun-facts" class="framePanel rounded left">
    <h2>Fun Facts</h2>
    <!-- display the total number of quizzes created by the member-->
    <div class="content-container">
    <!-- if the member is self -> "You have", if not use pronoun according to gender-->
    <p class="fact"><?php echo ($self) ? "You have" : ucfirst($gender[0])." has"; ?> created</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
      <div class="factValue"><?php echo sprintf("%d", $quiz_total) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>
    <!-- only display those detailed information if the member is friend-->
    <?php if($friend){ ?>
    <div class="factbox rounded">
      <p class="unit">with </p>
      <!-- number of published quizzes-->
      <div class="factValue"><?php echo sprintf("%d", $view_member->getStats('quizzes_published')) ?></div>
      <p class="factDesc">Published</p>
    </div>
    <!-- The total number of quiz attempt-->
    <p class="fact"><?php echo ($self) ? "You have" : ucfirst($gender[0])." has"; ?></p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
	  <div class="factValue"><?php echo sprintf("%d", $view_member->getStats('taken_quizzes_total')) ?></div>
    <p class="factDesc">Quiz Attempts</p></div>
    <div class="factbox rounded">
      <p class="unit">with </p>
      <!-- The total number of unique attempt-->
      <div class="factValue"><?php echo sprintf("%d", $view_member->getStats('taken_quizzes_unique')) ?></div>
      <p class="factDesc">Unique Attempts</p>
    </div>
    <?php } ?>
    <p class="fact"><?php echo ($self) ? "Your" : ucfirst($gender[1]); ?> quizzes</p>
    <!-- only display those detailed information if the member is friend-->
    <?php if($friend){ ?>
    <div class="factbox rounded">
    <!-- Average number of questions per quiz-->
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo $question_avg; ?></div>
      <p class="factDesc">Questions</p></div>
    <div class="factbox rounded">
    <!-- Average number of options per quiz-->
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo $option_avg; ?></div>
    <p class="factDesc">Options</p></div>
    <?php } ?>
    <!-- Average number of likes per quiz-->
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo $like_avg; ?></div>
      <p class="factDesc">Likes</p>
    </div>
    </div>
  </div>
  <!-- Display the member's ranking-->
  <div id="ranking" class="framePanel rounded right">
  <!-- Rank of member -->
    <h2><?php echo $view_member->qname; ?>'s Ranking</h2>
    <div class="content-container">
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
        <!-- Display information of taking quiz and creating quiz of the member-->
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
  </div>
  <!-- Display Quiz taking topic and quiz taking history only if the member is friend-->
  <?php if($friend){ ?>
  <div id="topic-breakdown" class="framePanel rounded right">
    <h2>Quiz Taking Topic Breakdown</h2>
    <div class="content-container">
    <div id="topic_chart"><div id="loader-box"><img src="../webroot/img/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div></div>
    </div>
  </div>
  <div id="taking-history" class="framePanel rounded right">
    <h2>Quiz Taking History</h2>
    <div class="content-container">
    <div id="takeHistory_chart"><div id="loader-box"><img src="../webroot/img/loader.gif" alt="Loading.." width="16" height="16" border="0" align="absmiddle" class="noborder" /> Loading</div></div>
    </div>
  </div>
  <?php } ?>
</div>
<?php } ?>
