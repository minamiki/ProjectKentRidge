<?php
//----------------------------------------
// Publish the quiz
//----------------------------------------
include('quiz.php');
$quiz = new Quiz($_GET['id']);

// check if member is owner of the quiz
if($quiz->isOwner($member->id)){
	// set the quiz as published
	$quiz->republish();	
}
?>
<div class="frame rounded">
<h3 id="title_quiz_result">Are you sure?</h3> 
<p>This action is cannot be undone! All results, questions, options and images associated with this quiz will be removed. </p>
<p>However, the points that you have earned through this quiz will not be affected. All your achievements will also be intact.</p>
</div>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic '<?php echo $quiz->category(); ?>'</p>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('../modules/deleteQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Remove this Quiz" />
</div>
<script type="text/javascript" src="../webroot/js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details?>);
</script>
