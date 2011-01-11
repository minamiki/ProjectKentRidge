<?php require_once('../Connections/quizroo.php'); ?>
<?php
$colname_getQuiz = "-1";
if (isset($_GET['id'])) {
  $colname_getQuiz = $_GET['id'];
}
mysql_select_db($database_quizroo, $quizroo);
$query_getQuiz = sprintf("SELECT quiz_name, quiz_description, quiz_picture, creation_date,  q_quiz_cat.cat_name, members.nickname FROM q_quizzes, q_quiz_cat, members WHERE quiz_id = %s AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat AND q_quizzes.fk_member_id = members.member_id", GetSQLValueString($colname_getQuiz, "int"));
$getQuiz = mysql_query($query_getQuiz, $quizroo) or die(mysql_error());
$row_getQuiz = mysql_fetch_assoc($getQuiz);
$totalRows_getQuiz = mysql_num_rows($getQuiz);
?>
<div id="created-preamble" class="frame rounded">
  <h3 id="title_quiz_created">Quiz Created</h3>
  Yay! Your quiz has been created! You can preview your quiz below, or go to manage quizzes to view statistics on your quiz.</div>
<div id="create-quiz" class="frame rounded">
  <h2><?php echo $row_getQuiz['quiz_name']; ?></h2>
  <p><img src="quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getQuiz['quiz_picture']; ?>" width="320" height="213" alt="" /></p>
  <p class="description"><?php echo $row_getQuiz['quiz_description']; ?></p>
  <p class="info">by <em><?php echo $row_getQuiz['nickname']; ?></em> on <?php echo date("F j, Y g:ia", strtotime($row_getQuiz['creation_date'])); ?> in the topic '<?php echo $row_getQuiz['cat_name']; ?>'</p>
</div>
<?php
mysql_free_result($getQuiz);
?>
