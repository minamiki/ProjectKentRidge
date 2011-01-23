<?php require_once('../Connections/quizroo.php'); ?>
<?php
$colname_getQuizInfo = "-1";
if (isset($_GET['id'])) {
  $colname_getQuizInfo = $_GET['id'];
}
mysql_select_db($database_quizroo, $quizroo);
$query_getQuizInfo = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, creation_date,  members.member_name, q_quiz_cat.cat_name, (SELECT COUNT(question_id) FROM q_questions WHERE fk_quiz_id = %s) AS question_count FROM q_quizzes, members, q_quiz_cat WHERE quiz_id = %s AND members.member_id = q_quizzes.fk_member_id AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat", GetSQLValueString($colname_getQuizInfo, "int"),GetSQLValueString($colname_getQuizInfo, "int"));
$getQuizInfo = mysql_query($query_getQuizInfo, $quizroo) or die(mysql_error());
$row_getQuizInfo = mysql_fetch_assoc($getQuizInfo);
$totalRows_getQuizInfo = mysql_num_rows($getQuizInfo);
?>
<div id="takequiz-preamble" class="frame rounded">
  <h3>Take a quiz</h3>
  Here's some information about the quiz! You can decide whether to take this quiz. This quiz contains <strong><?php echo $row_getQuizInfo['question_count']; ?> questions</strong>. </div>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $row_getQuizInfo['quiz_name']; ?></h2>
<img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getQuizInfo['quiz_picture']; ?>" width="320" height="213" alt="" />
  <p class="description"><?php echo $row_getQuizInfo['quiz_description']; ?></p>
  <p class="info">by <em><?php echo $row_getQuizInfo['member_name']; ?></em> on <?php echo date("F j, Y g:ia", strtotime($row_getQuizInfo['creation_date'])); ?> in the topic '<?php echo $row_getQuizInfo['cat_name']; ?>'</p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $row_getQuizInfo['quiz_id']; ?>');" value="Take Quiz now!" />
</div>
<?php
mysql_free_result($getQuizInfo);
?>
