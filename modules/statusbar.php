<?php
//retrieve categories from db as a string, fetch the result row as an associative array, return num rows
//get topic id from address bar
//display feature unavailable msg
//display for quizroo logo and link and mouseover msg
//display for statusbar, including wordings on bar and mouse over msg, achievements/quiz taker and creator's scores
//mouse over info on topic bar, retrived from db, total #quiz and #undone per topic, print categories names

// populate the topics panel
$query_getTopics = sprintf("SELECT cat_id, cat_name, undone, 
							(SELECT COUNT(quiz_id) FROM `q_quizzes` WHERE isPublished = 1 AND fk_quiz_cat = cat_id) as total 						
							FROM (SELECT COUNT(fk_quiz_cat) as undone, fk_quiz_cat FROM q_quizzes q 
							WHERE q.quiz_id NOT IN (SELECT DISTINCT(fk_quiz_id) FROM `q_store_result` WHERE `fk_member_id` = %s) 
							AND isPublished = 1 
							GROUP BY fk_quiz_cat) t RIGHT JOIN q_quiz_cat r ON t.fk_quiz_cat = r.cat_id", $member->id);
$getTopics = mysql_query($query_getTopics, $quizroo) or die(mysql_error());
$row_getTopics = mysql_fetch_assoc($getTopics);
$totalRows_getTopics = mysql_num_rows($getTopics);

//isset-> determine if a var is not NULL
//get topic id from address bar
if(isset($_GET['topic'])){
	$topic = $_GET['topic'];
}else{
	$topic = 0;
}
?> <!-- display feature unavailable msg-->
<div id="dialog-message" style="display:none;" title="Feature Unavailable"> Oops, this feature is currently unavailable yet. Please hang in there while we work on it. </div>
<div id="statusbar-container"> <!--display for quizroo logo and link and mouseover msg-->
  <div id="statusbar"> <a href="index.php" id="statusbar-logo" title="Quizroo"><span>Quizroo</span></a>
    <div id="statusbar-divider"></div>
    <div id="statusbar-notification"> <!--display for notification-->
      <div id="notification-system"  class="statusbar-element" title="Notifications">
        <div id="notification-system-count" class="notification-count"></div>
      </div>
    </div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-game">
      <div id="statusbar-scores" class="statusbar-element">
        <div id="statusbar-quizcreator"> <!-- display for quiz creator, including total and daily scores -->
          <div id="statusbar-quizcreator-logo" title="Quiz Creator"></div>
          <div id="statusbar-quizcreator-count-total" class="statusbar-achievements-quizcount-top" title="Quiz Creator Popularity Score"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quizcreator_score; ?></span></div></div>
          <div id="statusbar-quizcreator-count-today" class="statusbar-achievements-quizcount-bottom" title="Quiz Creator Score for Today"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quizcreator_score_today; ?></span></div>
          </div>
        </div>
        <div id="statusbar-quiztaker"> <!-- display for quiz taker-->
          <div id="statusbar-quiztaker-logo" title="Quiz Taker"></div>
          <div id="statusbar-quiztaker-count-total" class="statusbar-achievements-quizcount-top" title="Quiz Taker Score"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;"><?php echo $member->quiztaker_score; ?></span></div></div>
          <div id="statusbar-quiztaker-count-today" class="statusbar-achievements-quizcount-bottom"  title="Quiz Taker Score for Today"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quiztaker_score_today; ?></span></div>
          </div>
        </div>
      </div> <!-- display for achievements-->
      <div id="statusbar-achievements" class="statusbar-element" title="Achievements">
        <div id="statusbar-achievements-count"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 17px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->getStats('achievements'); ?></span></div>
        </div>
        <div id="statusbar-achievements-logo"></div>
      </div>
    </div> <!--wordings for drop down menus of quiz, friends, profile, about, search-->
    <div id="statusbar-divider"></div>
    <div id="statusbar-quiz" class="statusbar-text statusbar-element" title="Create, Manage or Browse quizzes">Quiz</div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-friends" class="statusbar-text statusbar-element" title="Find out about your friends or Invite your friends to Quizroo">Friends</div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-profile" class="statusbar-text statusbar-element" title="Configure Quizroo, View usage statistics and usage history">Profile</div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-about" class="statusbar-text statusbar-element" title="Get help or Find out more about Quizroo">About</div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-search" class="statusbar-searchmenu-button" title="Search"><span>Search</span></div>
  </div>
  <div id="statusbar-info"></div>
  <div id="statusbar-sidemenu"></div>
</div>
<div id="topics-bar" class="clear">
  <ul>
      <li><a href="index.php" class="icon"><span>#</span></a></li>  		
    <?php do { ?> <!-- display for topics bar and wordings for mouse over -->
      <li><a href="topics.php?topic=<?php echo $row_getTopics['cat_id']; ?>" class="topicTitle<?php echo ($topic == $row_getTopics['cat_id']) ? " current" : ""; ?>" title="Total <?php echo $row_getTopics['total']; ?> quizzes, <?php echo ($row_getTopics['undone'] != NULL) ? $row_getTopics['undone'] : 0; ?> undone"><?php echo $row_getTopics['cat_name']; ?></a></li>
      <?php } while ($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
  </ul>
</div>
<?php
mysql_free_result($getTopics);
?>
