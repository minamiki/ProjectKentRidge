<?php require('quizrooDB.php'); ?>
<?php if($quiz->exists()){ ?>
<div id="takequiz-preview" class="frame rounded"><div class="logo"> <img src="../webroot/img/quizroo-logo.png" width="130" height="50" /></div>
<h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" alt="" width="320" height="213" class="previewImage" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><?php echo $quiz->creator(); ?></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic '<?php echo $quiz->category(); ?>'</p>
<input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('previewQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Take this Quiz @ Quizroo" />
  <p class="info">(You will be directed to the Facebook Appliction, <a href="http://apps.facebook.com/quizroo">Quizroo</a>)</p>
</div>
<?php if($quiz->isOwner($member->id) && !$quiz->isPublished()){ ?>
<div id="takequiz-preamble" class="frame rounded">
  <div class="logo"> <img src="../webroot/img/quizroo-logo.png" alt="" width="130" height="50" /></div>
  <h3>Publish this quiz!</h3>
  <p>Hey! You're the owner of this unpublished quiz! If you feel that your quiz is ready, click the &quot;Publish Quiz&quot; button. Once your quiz is published, it will be listed on Quizroo. You will receive points when a user takes your quiz. You can get more points when they &quot;like&quot; your quiz, or no points when they &quot;dislike&quot; your quiz.</p>
<input type="button" name="button" id="button" onclick="goToURL('publishQuiz.php?id=<?php echo $_GET['id']; ?>')" value="Publish Quiz!" />
 </div>
<?php }}else{ ?>
<div id="takequiz-preamble" class="frame rounded">
  <div class="logo"> <img src="../webroot/img/quizroo-logo.png" alt="" width="130" height="50" /></div>
  <h3>Opps, quiz not found!</h3>
  <p>Sorry! The quiz that you're looking for may no be available. Please check the ID of the quiz again.</p>
  <div class="bullets">
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
