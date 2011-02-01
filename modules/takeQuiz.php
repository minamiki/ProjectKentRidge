<?php require('../Connections/quizroo.php'); ?>
<?php
$colname_getQuizInfo = "-1";
if (isset($_GET['id'])) {
  $colname_getQuizInfo = $_GET['id'];
}
mysql_select_db($database_quizroo, $quizroo);
$query_getQuizInfo = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, creation_date, members.member_name, q_quiz_cat.cat_name, (SELECT COUNT(question_id) FROM q_questions WHERE fk_quiz_id = %s) AS question_count FROM q_quizzes, members, q_quiz_cat WHERE quiz_id = %s AND members.member_id = q_quizzes.fk_member_id AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat", GetSQLValueString($colname_getQuizInfo, "int"),GetSQLValueString($colname_getQuizInfo, "int"));
$getQuizInfo = mysql_query($query_getQuizInfo, $quizroo) or die(mysql_error());
$row_getQuizInfo = mysql_fetch_assoc($getQuizInfo);
$totalRows_getQuizInfo = mysql_num_rows($getQuizInfo);

$colname_getQuizQuestions = "-1";
if (isset($_GET['id'])) {
  $colname_getQuizQuestions = $_GET['id'];
}
mysql_select_db($database_quizroo, $quizroo);
$query_getQuizQuestions = sprintf("SELECT * FROM q_questions WHERE fk_quiz_id = %s", GetSQLValueString($colname_getQuizQuestions, "int"));
$getQuizQuestions = mysql_query($query_getQuizQuestions, $quizroo) or die(mysql_error());
$row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions);
$totalRows_getQuizQuestions = mysql_num_rows($getQuizQuestions);

$question_count = 1;
?>

<div id="takequiz-preamble" class="frame rounded">
  <h3>Take a quiz</h3>
  <p>You're now taking the quiz,<em> &quot;<?php echo $row_getQuizInfo['quiz_name']; ?>&quot;</em> by <?php echo $row_getQuizInfo['member_name']; ?>. You may stop taking the quiz anytime by navigating away from this page. No data will be collected unless you complete the quiz.</p>
  <div id="progress_panel">
      <div id="question_paging">
        <?php for($i = 0; $i < $totalRows_getQuizQuestions; $i++) { ?>
        <!--<a href="#" rel="<?php echo ($i+1); ?>"><?php echo ($i+1); ?></a>--> 
        <span title="<?php echo ($i+1); ?>"><?php echo ($i+1); ?></span>
        <?php } ?>
      </div>
      <span id="final-bulb">&#10003;</span>
      <!--<p id="progress_text">Overall Progress (<span id="progress_percentage">0</span>%)</p>-->
      <div id="progress_bar">
        <div id="progress"></div>
      </div>
  </div>
  <div id="incomplete" class="rounded">
    <p>Questions marked with a white circle are not answered!</p>
    <p>Use the &quot;Previous&quot; and &quot;Next&quot; buttons to navigate between questions.</p>
  </div>
</div>
<div id="takequiz-main">
  <form name="takeQuiz" id="takeQuiz" action="quiz_result.php" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $row_getQuizInfo['quiz_id']; ?>" />
    <input type="hidden" name="logtime" id="logtime" value="<?php date_default_timezone_set("Asia/Singapore"); echo time(); ?>" />
    <div id="questionContainer">
      <div id="question_reel">
        <?php do { 
			mysql_select_db($database_quizroo, $quizroo);
			$query_getOptions = "SELECT * FROM q_options WHERE fk_question_id = ".$row_getQuizQuestions['question_id'];
			$getOptions = mysql_query($query_getOptions, $quizroo) or die(mysql_error());
			$row_getOptions = mysql_fetch_assoc($getOptions);
			$totalRows_getOptions = mysql_num_rows($getOptions);	
			
			$option_count = 1;	  
		  ?>
        <div class="question_slide">
          <fieldset>
            <h4>Question <?php echo $question_count; ?></h4>
            <?php if($row_getQuizQuestions['question_image'] != NULL){ ?>
            <span id="question-image"><img src="../quiz_images/imgcrop.php?w=500&h=375&f=<?php echo $row_getQuizQuestions['question_image']; ?>" width="500" height="375" /></span>
            <?php } ?>
            <p><?php echo $row_getQuizQuestions['question']; ?></p>
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
              <?php do { ?>
                <tr>
                  <th width="30" scope="row"><input type="radio" name="q<?php echo $question_count; ?>" id="q<?php echo $question_count; ?>o<?php echo $option_count; ?>" value="<?php echo $row_getOptions['option_id']; ?>" /></th>
                  <td><label for="q<?php echo $question_count; ?>o<?php echo $option_count; ?>"><?php echo $row_getOptions['option']; ?></label></td>
                </tr>
                <?php $option_count++; } while ($row_getOptions = mysql_fetch_assoc($getOptions)); ?>
            </table>
            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0" id="question_navigation">
              <?php if($question_count != $totalRows_getQuizQuestions){ if($question_count == 1){ ?>
              <tr>
                <td align="left" scope="row">&nbsp;</td>
                <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
              </tr>
              <?php }else{ ?>
              <tr>
                <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
              </tr>
              <?php }}else{ ?>
              <tr>
                <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                <td align="right"><input name="finishQuiz" type="submit" class="btnDisabled" id="finishQuiz" value="Complete Quiz" /></td>
              </tr>
              <?php } ?>
            </table>
          </fieldset>
        </div>
        <?php $question_count++; mysql_free_result($getOptions); } while ($row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions)); ?>
      </div>
    </div>
  </form>
</div>
<?php
mysql_free_result($getQuizInfo);
mysql_free_result($getQuizQuestions);
?>
