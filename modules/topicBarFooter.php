<!-- Add on 26 Jul, for moving the topic bar to the bottom of the page: Add php code for retrieving database topics and div for topic bar-->
<?php
// populate the topics panel
$query_getTopics = sprintf("SELECT cat_id, cat_name, undone, (SELECT COUNT(quiz_id) FROM `q_quizzes` WHERE isPublished = 1 AND fk_quiz_cat = cat_id) as total FROM (SELECT COUNT(fk_quiz_cat) as undone, fk_quiz_cat FROM q_quizzes q WHERE q.quiz_id NOT IN (SELECT DISTINCT(fk_quiz_id) FROM `q_store_result` WHERE `fk_member_id` = %s) AND isPublished = 1 GROUP BY fk_quiz_cat) t RIGHT JOIN q_quiz_cat r ON t.fk_quiz_cat = r.cat_id", $member->id);
$getTopics = mysql_query($query_getTopics, $quizroo) or die(mysql_error());
$row_getTopics = mysql_fetch_assoc($getTopics);
$totalRows_getTopics = mysql_num_rows($getTopics);

if(isset($_GET['topic'])){
	$topic = $_GET['topic'];
}else{
	$topic = 0;
}
?>
<div id="topics-bar" class="clear">
  <ul>
  <!-- Modify 26 July: display or not display admin page based on member id-->
  <?php //check if member is admin
  $isAdmin =  sprintf("SELECT isAdmin FROM `s_members` WHERE member_id = " + $facebookID);
  if( isAdmin == 1)
  {
  ?>
      <li><a href="index.php" class="icon"><span>#</span></a></li>  
      		
    <?php ;}
	do { ?>
      <li><a href="topics.php?topic=<?php echo $row_getTopics['cat_id']; ?>" class="topicTitle<?php echo ($topic == $row_getTopics['cat_id']) ? " current" : ""; ?>" title="Total <?php echo $row_getTopics['total']; ?> quizzes, <?php echo ($row_getTopics['undone'] != NULL) ? $row_getTopics['undone'] : 0; ?> undone"><?php echo $row_getTopics['cat_name']; ?></a></li>
      <?php } while ($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
  </ul>
</div>
<?php
mysql_free_result($getTopics);
?>