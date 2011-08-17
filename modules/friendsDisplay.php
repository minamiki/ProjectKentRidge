<!-- This page is for displaying all the statistic information of the user's friends
*Ranking of the friends
*Details of the activity of the user's friends: creating and taking quizzes 
-->
<?php
require('../modules/quizrooDB.php'); // require database connection
require('../modules/variables.php');
require('../modules/system.php'); // require quiz fact class

$friends_list = implode(",", $member->getFriendsArray());

$systemStats = new System();
$systemStats->getMemberStats($friends_list);

/******************************************************* 
 *Inner select ranking in descending order. Outer select filter user's friends among the result of the inner select.
 *******************************************************/
$query_getRanking = sprintf("SELECT * FROM (SELECT @rownum:=@rownum+1 ranking, member_id, member_name, level, g_achievements.name as rank_name, quiztaker_score+quizcreator_score AS score, quiztaker_score, quizcreator_score FROM s_members, g_achievements, (SELECT @rownum:=0) numbering WHERE s_members.rank = g_achievements.id ORDER BY score DESC) t WHERE member_id IN (%s)", $friends_list);
$getRanking = mysql_query($query_getRanking, $quizroo) or die(mysql_error());
$row_getRanking = mysql_fetch_assoc($getRanking);
$totalRows_getRanking = mysql_num_rows($getRanking);
?>
<div id="leaderboard-preamble" class="frame rounded">
  <h2>Friends</h2>
  <p>Find out how your friends fare against each other! You can also get to know interesting facts about your friends' quizzes on Quizzroo!</p>
</div>
<!-- display the fact based on information extracted from the above query-->
<div class="clear">
  <div id="fun-facts" class="framePanel rounded left">
    <h2>Fun Facts</h2>
    <!-- display the average score of user's friends-->
    <div class="content-container">
    <p class="fact">Your friends have</p>
    <div class="factbox rounded">
      <p class="unit">an average score of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_score')) ?></div>
      <p class="factDesc">Points</p>
    </div>
    <!-- display the average number of quizzes taken by user's friends-->
    <div class="factbox rounded">
      <p class="unit">taken an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_take_quiz')) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>
    <!-- display the average number of quizzes created by user's friends-->
    <div class="factbox rounded">
      <p class="unit">created and average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_create_quiz')) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>
	<!-- display the average number of questions per quiz created by users' friends-->
    <p class="fact">Their  quizzes</p>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
	  <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('questions')); ?></div>
      <p class="factDesc">Questions</p></div>
    <!-- display the average number of options per quiz created by users' friends-->
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('options')); ?></div>
      <p class="factDesc">Options</p>
    </div>
    <!-- display the average number of likes per quiz created by users' friends-->
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('likes')); ?></div>
      <p class="factDesc">Likes
    </p></div>
    <!-- display the average number of points per quiz created by users' friends-->
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('quiz_score')); ?></div>
    <p class="factDesc">Points</p></div>
    <div class="factbox rounded">
    <!-- display the average number of time that the quiz created by user' friends has been taken-->
      <p class="unit">was taken</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('quiz_taken')); ?></div>
      <p class="factDesc">Times</p>
    </div>
    </div>
  </div>
  <!-- display rank and score of user's friends-->
  <div id="ranking" class="framePanel rounded right">
    <h2>Friends Rankings</h2>
    <div class="content-container">
    <table width="100%" border="0" cellpadding="4" cellspacing="0" id="rankTable">
      <tr>
        <th width="55" scope="col">Rank</th>
        <th width="60" scope="col">&nbsp;</th>
        <th align="left" scope="col">Member</th>
        <th scope="col">Score</th>
      </tr>
      <!-- do loop: get the data from the query to display-->
      <?php do{ ?>
      <tr>
        <td width="55" align="center" scope="row" class="ranking"><?php echo $row_getRanking['ranking']; ?></td>
        <td width="60" align="center" scope="row"><a href="../webroot/viewMember.php?id=<?php echo $row_getRanking['member_id']; ?>"><img src="http://graph.facebook.com/<?php echo $row_getRanking['member_id']; ?>/picture" alt="<?php echo $row_getRanking['member_name']; ?>" width="50" height="50" border="0" title="<?php echo $row_getRanking['member_name']; ?>" /></a></td>
        <td><p class="member-name"><?php echo $row_getRanking['member_name']; ?></p>
        <p class="member-level"><?php echo $row_getRanking['rank_name']; ?> (Level <?php echo $row_getRanking['level']; ?>)</p></td>
        <td align="center"><p class="score" title="Psss.. extra credit of <?php echo sprintf("%.2f", ($row_getRanking['quizcreator_score'] > 0) ? log($row_getRanking['quizcreator_score'])/5 : 0); ?>"><?php echo $row_getRanking['score']; ?></p>
        <p class="breakdown-score">Taker: <?php echo $row_getRanking['quiztaker_score']; ?>, Creator: <?php echo $row_getRanking['quizcreator_score']; ?></p></td>
      </tr>
      <?php }while($row_getRanking = mysql_fetch_assoc($getRanking)); ?>
    </table>
    </div>
  </div>
</div>
<?php
mysql_free_result($getRanking);
?>
