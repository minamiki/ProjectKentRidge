<?php
//----------------------------------------
// Publish the quiz
//----------------------------------------
require("../modules/quiz.php");
$quiz = new Quiz($_GET['id']);

// check if member is owner of the quiz
if($quiz->isOwner($member->id)){
	// set the quiz as published
	$quiz->republish();	
}
?>
<div class="frame rounded">
<h3 id="title_quiz_result">Quiz Updated!</h3> 
You have successfully updated your quiz. Your quiz will now be available under the topic <?php echo $quiz->category(); ?>. It will also turn up in search queries. You will continue to receive points when users take your quiz! Good luck!
</div>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic '<?php echo $quiz->category(); ?>'</p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Take Quiz now!" />
</div>
