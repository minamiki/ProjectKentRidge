<?php if($quiz_exist){ ?>
<?php if(isset($_GET['unlist'])){ ?>
<div class="framePanel rounded">
<h2 id="title_quiz_result">Quiz Unpublished!</h2> 
<div class="content-container"><p>
You have successfully unpublished your quiz. Your quiz will not be listed in topics or search queries.</p></div></div>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic '<?php echo $quiz->category(); ?>'</p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('previewQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Preview Quiz" />
</div>
<?php }else{ ?>
<div class="framePanel rounded">
<h2 id="title_quiz_result">Quiz Published!</h2> 
<div class="content-container"><p>
You have successfully published your quiz. Your quiz will now be available under the topic <?php echo $quiz->category(); ?>. It will also turn up in search queries. You will start receiving points when users take your quiz! Good luck!</p>
</div></div>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic <em><?php echo $quiz->category(); ?></em></p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Take Quiz now!" />
</div>
<?php } ?>
<?php }else{ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Opps, quiz not found!</h2>
  <div class="content-container"> <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
    <p>Sorry! The quiz that you're looking for may no be available. Please check the ID of the quiz again.</p>
    <p>The reason you're seeing this error could be due to:</p>
    <ul>
      <li>The URL is incorrect or doesn't  contain the ID of the quiz</li>
      <li>No quiz with this ID exists</li>
      <li>The owner could have removed the quiz</li>
      <li>The quiz was taken down due to violations of  rules at Quizroo</li>
    </ul>
  </div>
</div>
<?php } ?>
