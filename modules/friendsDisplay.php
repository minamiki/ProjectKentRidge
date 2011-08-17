<?php
//This file concerns the overview of one's friends- Their ranking, and fun facts. 
//http://localhost/quizroo/webroot/friends.php
require('../modules/quizrooDB.php');
require('../modules/variables.php');
require('../modules/system.php');

$friends_list = implode(",", $member->getFriendsArray());

$systemStats = new System();
$systemStats->getMemberStats($friends_list);

// fetch the topic information
$query_getRanking = sprintf("SELECT * FROM (SELECT @rownum:=@rownum+1 ranking, member_id, member_name, level, g_achievements.name as rank_name, quiztaker_score+quizcreator_score AS score, quiztaker_score, quizcreator_score FROM s_members, g_achievements, (SELECT @rownum:=0) numbering WHERE s_members.rank = g_achievements.id ORDER BY score DESC) t WHERE member_id IN (%s)", $friends_list);
$getRanking = mysql_query($query_getRanking, $quizroo) or die(mysql_error());
$row_getRanking = mysql_fetch_assoc($getRanking);
$totalRows_getRanking = mysql_num_rows($getRanking);
?>
<!-- Get Fun Facts -->
<div id="leaderboard-preamble" class="frame rounded">
  <h2>Friends</h2>
  <p>Find out how your friends fare against each other! You can also get to know interesting facts about your friends' quizzes on Quizzroo!</p>
</div>
<div class="clear">
  <div id="fun-facts" class="framePanel rounded left">
    <h2>Fun Facts</h2>
    <div class="content-container">
    <p class="fact">Your friends have</p>
    <div class="factbox rounded">
      <p class="unit">an average score of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_score')) ?></div>
      <p class="factDesc">Points</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">taken an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_take_quiz')) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">created and average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('member_create_quiz')) ?></div>
      <p class="factDesc">Quizzes</p>
    </div>

    <p class="fact">Their  quizzes</p>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
	  <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('questions')); ?></div>
      <p class="factDesc">Questions</p></div>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('options')); ?></div>
      <p class="factDesc">Options</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('likes')); ?></div>
      <p class="factDesc">Likes
    </p></div>
    <div class="factbox rounded">
      <p class="unit">has an average of</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('quiz_score')); ?></div>
    <p class="factDesc">Points</p></div>
    <div class="factbox rounded">
      <p class="unit">was taken</p>
      <div class="factValue"><?php echo sprintf("%.2f", $systemStats->getAverageStat('quiz_taken')); ?></div>
      <p class="factDesc">Times</p>
    </div>
    </div>
  </div>
  
  <!-- Get ranking of all friends -->
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
