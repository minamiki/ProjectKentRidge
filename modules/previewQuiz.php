<?php require('../Connections/quizroo.php'); ?>
<?php
require('quiz.php');
$quiz = new Quiz($_GET['id']);
?>
<?php if($quiz->isPublished()){ ?>
<div id="takequiz-preamble" class="frame rounded">
  <h3>Take a quiz</h3>
  <p>Here's some information about the quiz! You can decide whether to take this quiz. This quiz contains <strong><?php echo $quiz->numQuestions(); ?> questions</strong>.</p>
</div>
<?php }else{ ?>
<div id="takequiz-preamble" class="frame rounded">
  <h3>Preview Quiz</h3>
  <p>This is an <strong>unpublished</strong> quiz! You can still try out the quiz and get results, but you <em>will not</em> receive any points for it! Here's some information about the quiz! This quiz contains <strong><?php echo $quiz->numQuestions(); ?> questions</strong>.</p></div>
<?php } ?>
<?php if($quiz->isOwner($member->id) && !$quiz->isPublished()){ ?>
<div id="takequiz-preamble" class="frame rounded">
  <h3>Publish Quiz</h3>
  <p>If you feel that your quiz is ready, click the &quot;Publish Quiz&quot; button. Once your quiz is published, it will be listed on Quizroo. You will receive points when a user takes your quiz. You can get more points when they &quot;like&quot; your quiz, or no points when they &quot;dislike&quot; your quiz.</p>
  <span class="center">
  <input type="button" name="button" id="button" onclick="goToURL('publishQuiz.php?id=<?php echo $_GET['id']; ?>')" value="Publish Quiz!" />
</span> </div>
<?php } ?>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic '<?php echo $quiz->category(); ?>'</p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Take Quiz now!" />
</div>