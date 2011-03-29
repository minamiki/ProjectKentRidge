<?php
// populate the topics panel
$query_getTopics = "SELECT * FROM q_quiz_cat";
$getTopics = mysql_query($query_getTopics, $quizroo) or die(mysql_error());
$row_getTopics = mysql_fetch_assoc($getTopics);
$totalRows_getTopics = mysql_num_rows($getTopics);

if(isset($_GET['topic'])){
	$topic = $_GET['topic'];
}else{
	$topic = 0;
}
?>
<div id="dialog-message" style="display:none;" title="Feature Unavailable"> Oops, this feature is currently unavailable yet. Please hang in there while we work on it. </div>
<div id="statusbar-container">
  <div id="statusbar"> <a href="index.php" id="statusbar-logo" title="Quizroo"><span>Quizroo</span></a>
    <div id="statusbar-divider"></div>
    <div id="statusbar-notification">
      <div id="notification-system"  class="statusbar-element" title="Notifications">
        <div id="notification-system-count" class="notification-count"></div>
      </div>
    </div>
    <div id="statusbar-divider"></div>
    <div id="statusbar-achievements">
      <div id="statusbar-scores" class="statusbar-element">
        <div id="statusbar-quizcreator">
          <div id="statusbar-quizcreator-logo" title="Quiz Creator"></div>
          <div id="statusbar-quizcreator-count-total" class="statusbar-achievements-quizcount-top" title="Quiz Creator Popularity Score"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quizcreator_score; ?></span></div></div>
          <div id="statusbar-quizcreator-count-today" class="statusbar-achievements-quizcount-bottom" title="Quiz Creator Score for Today"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quizcreator_score_today; ?></span></div>
          </div>
        </div>
        <div id="statusbar-quiztaker">
          <div id="statusbar-quiztaker-logo" title="Quiz Taker"></div>
          <div id="statusbar-quiztaker-count-total" class="statusbar-achievements-quizcount-top" title="Quiz Taker Score"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;"><?php echo $member->quiztaker_score; ?></span></div></div>
          <div id="statusbar-quiztaker-count-today" class="statusbar-achievements-quizcount-bottom"  title="Quiz Taker Score for Today"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 13px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->quiztaker_score_today; ?></span></div>
          </div>
        </div>
      </div>
      <div id="statusbar-achievements-logo" class="statusbar-element" title="Achievements">
        <div id="statusbar-achievements-count"><div class="stretch--resizer" style="margin: 0pt; padding: 0pt; white-space: nowrap; overflow: hidden; font-size: 17px; word-spacing: 0px;" type="statusbar"><span class="stretch--handle" style="margin: 0pt; padding: 0pt;" type="statusbar"><?php echo $member->getStats('achievements'); ?></span></div>
        </div>
      </div>
    </div>
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
    <?php do { ?>
      <li><a href="topics.php?topic=<?php echo $row_getTopics['cat_id']; ?>" class="topicTitle<?php echo ($topic == $row_getTopics['cat_id']) ? " current" : ""; ?>"><?php echo $row_getTopics['cat_name']; ?></a></li>
      <?php } while ($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
  </ul>
</div>
<?php
mysql_free_result($getTopics);
?>
