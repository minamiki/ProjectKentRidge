<?php require('../modules/quizrooDB.php'); ?>
<?php
$query_getQuizInfo = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, creation_date, s_members.member_name, q_quiz_cat.cat_name, (SELECT COUNT(question_id) FROM q_questions WHERE fk_quiz_id = %s) AS question_count FROM q_quizzes, s_members, q_quiz_cat WHERE quiz_id = %s AND s_members.member_id = q_quizzes.fk_member_id AND q_quiz_cat.cat_id = q_quizzes.fk_quiz_cat", GetSQLValueString($url_id, "int"),GetSQLValueString($url_id, "int"));
$getQuizInfo = mysql_query($query_getQuizInfo, $quizroo) or die(mysql_error());
$row_getQuizInfo = mysql_fetch_assoc($getQuizInfo);
$totalRows_getQuizInfo = mysql_num_rows($getQuizInfo);

$query_getQuizQuestions = sprintf("SELECT * FROM q_questions WHERE fk_quiz_id = %s", GetSQLValueString($url_id, "int"));
$getQuizQuestions = mysql_query($query_getQuizQuestions, $quizroo) or die(mysql_error());
$row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions);
$totalRows_getQuizQuestions = mysql_num_rows($getQuizQuestions);

$question_count = 1;
$page_count = 1;
$total_pages = ceil( $row_getQuizInfo['question_count'] / 5) ;
$total_questions =  $row_getQuizInfo['question_count'];?>

<script type="text/javascript">
var numQuestions = <?php echo $total_questions ?>;
</script>


<!-- <h4>Total Questions in this quiz:  <?php //echo $total_questions; ?> </h4> -->

<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Take a quiz</h2>
  <div class="content-container">
  <p>You're now taking the quiz,<em> &quot;<?php echo $row_getQuizInfo['quiz_name']; ?>&quot;</em> by <?php echo $row_getQuizInfo['member_name']; ?>. You may stop taking the quiz anytime by navigating away from this page. No data will be collected unless you complete the quiz.</p>
  <div id="progress_panel">
      <div id="question_paging">
        <?php for($i = 0; $i < $total_pages; $i++) { ?>
        <a href="javascript:;" title="Jump to Page <?php echo ($i+1); ?>" rel="<?php echo ($i+1); ?>"><?php echo ($i+1); ?></a>
        <?php } ?>
      </div>
 
      <span id="final-bulb">&#10003;</span>
      <!--<p id="progress_text">Overall Progress (<span id="progress_percentage">0</span>%)</p>-->
      <div id="progress_bar">
        <div id="progress"></div>
      </div>
  </div>
  <div id="incomplete" class="rounded">
    <p>Pages marked with a white circle has question/s that  is/are not answered!</p>
    <p>Use the &quot;Previous&quot; and &quot;Next&quot; buttons to navigate between questions.</p>
  </div>
  </div>
</div>
<div id="takequiz-main">
  <form name="takeQuiz" id="takeQuiz" action="quiz_result.php?id=<?php echo $row_getQuizInfo['quiz_id']; ?>" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $row_getQuizInfo['quiz_id']; ?>" />
    <input type="hidden" name="logtime" id="logtime" value="<?php date_default_timezone_set("Asia/Singapore"); echo time(); ?>" />
    <div id="questionContainer">
      <div id="question_reel">
        <?php do {  
		    if ($total_questions > 5) {
				if (($total_questions - $question_count) < 5) $limit = $total_questions - $question_count + 1;
				else $limit = 5; 
			}
			else $limit = $total_questions;?>
            
           		        
			<div class="question_slide">
			<fieldset>  
			<?php
			for ($questionOnPage = 0; $questionOnPage < $limit; $questionOnPage ++) 
			{
			$query_getOptions = "SELECT * FROM q_options WHERE fk_question_id = ".$row_getQuizQuestions['question_id'];
			$getOptions = mysql_query($query_getOptions, $quizroo) or die(mysql_error());
			$row_getOptions = mysql_fetch_assoc($getOptions);
			$totalRows_getOptions = mysql_num_rows($getOptions);	
			
			$option_count = 1;	  
		  ?>
 		
    
            
             <h4>Question<?php echo $question_count; ?> </h4>
            
            
 
            <?php if($row_getQuizQuestions['question_image'] != NULL){ ?>
            <span id="question-image"><img src="../quiz_images/imgcrop.php?w=500&h=375&f=<?php echo $row_getQuizQuestions['question_image']; ?>" width="500" height="375" /></span>
            <?php } ?>
                 
            <p><?php echo $row_getQuizQuestions['question']; ?></p>
            
            <?php if ($questionOnPage != 4) $row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions); ?>
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
              <?php do { ?>
                <tr>
                  <th width="30" scope="row"><input type="radio" name="q<?php echo $question_count; ?>" id="q<?php echo $question_count; ?>o<?php echo $option_count; ?>" value="<?php echo $row_getOptions['option_id']; ?>" /></th>
                  <td><label for="q<?php echo $question_count; ?>o<?php echo $option_count; ?>"><?php echo $row_getOptions['option']; ?></label></td>
                </tr>
                <?php $option_count++; } while ($row_getOptions = mysql_fetch_assoc($getOptions)); ?>
                <?php $question_count++; mysql_free_result($getOptions); ?>
            </table>
           <?php } ?>
           
             
            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0" id="question_navigation">
              <?php if($page_count != $total_pages){ if($page_count == 1){ ?>
              <tr>
                <td align="left" scope="row">&nbsp;</td>
                <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
              </tr>
              <?php }else{ ?>
              <tr>
                <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
              </tr>
              <?php }}else{ if ($total_pages == 1) {?>
               <tr>
               <td align="left" scope="row">&nbsp;</td>
               <td align="right"><input name="finishQuiz" type="submit" class="btnDisabled" id="finishQuiz" value="Complete Quiz" /></td>
              </tr>
              <?php } else { ?>
              <tr>
                <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                <td align="right"><input name="finishQuiz" type="submit" class="btnDisabled" id="finishQuiz" value="Complete Quiz" /></td>
              </tr>
              <?php }} ?>
            </table>
          </fieldset>
        </div>
        <?php 
		$page_count ++;
		
		} while ($row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions) ); ?>
      </div>
    </div>
  </form>
</div>
<?php
mysql_free_result($getQuizInfo);
mysql_free_result($getQuizQuestions);
?>
