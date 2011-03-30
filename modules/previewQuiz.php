<?php require('../modules/quizrooDB.php'); ?>
<?php if($quiz->exists()){ ?>
<?php if($quiz->isPublished()){ ?>
<?php if($quiz->hasTaken($member->id)){ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Re-take a Quiz</h2>
  <div class="content-container">
  <p>Hey! It seems that you've already taken this quiz before. In order to keep things fair, retaking quizzes won't earn you any points : / You can 'like' this quiz below if you didn't the last time you took it!</p>
  <p>Again, here's some information about the quiz! You can decide whether to retake this quiz. This quiz contains <strong><?php echo $quiz->numQuestions(); ?> questions</strong>.</p>
  </div>
</div>
<?php }else{ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Take a Quiz</h2>
  <div class="content-container">
  <p>Here's some information about the quiz! You can decide whether to take this quiz. This quiz contains <strong><?php echo $quiz->numQuestions(); ?> questions</strong>.</p>
  </div>
</div>
<?php } ?>
<?php }else{ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Preview Quiz</h2>
  <div class="content-container">
  <?php if($quiz->numQuestions() > 0){ ?>
  <p>This is an <strong>unpublished</strong> quiz! You can still try out the quiz and get results, but you <em>will not</em> receive any points for it! Here's some information about the quiz! This quiz contains <strong><?php echo $quiz->numQuestions(); ?> questions</strong>.</p>
  <?php }else{ ?>
   <p>This is an <strong>unpublished or draft</strong> quiz! Unfortunately, no questions have been created for it yet :( We can't let you take a quiz with no questions!
  <?php } ?>
  </div>
</div>
<?php } ?>
<div id="takequiz-preview" class="frame rounded">
  <h2><?php echo $quiz->quiz_name; ?></h2>
  <?php if($quiz->quiz_picture != "none.gif"){ ?>
  <img src="../quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $quiz->quiz_picture; ?>" width="320" height="213" alt="" />
  <?php } ?>
  <p class="description"><?php echo $quiz->quiz_description; ?></p>
  <p class="info">by <em><a href="viewMember.php?id=<?php echo $quiz->fk_member_id; ?>"><?php echo $quiz->creator(); ?></a></em> on <?php echo date("F j, Y g:ia", strtotime($quiz->creation_date)); ?> in the topic <a href="topics.php?topic=<?php echo $quiz->fk_quiz_cat; ?>"><em><?php echo $quiz->category(); ?></em></a></p>
  <?php if($quiz->numQuestions() > 0){ ?>
  <?php if($quiz->hasTaken($member->id)){ ?>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Re-take this Quiz!" />
  <?php }else{ ?>
  <input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="goToURL('takeQuiz.php?id=<?php echo $_GET['id']; ?>');" value="Take Quiz now!" />
  <?php }}else{ ?>
  <input name="takeQuizBtn" type="button" class="btnDisabled" id="takeQuizBtn" value="This quiz has no questions!" />
  <?php } ?>
<?php if($quiz->hasTaken($member->id)){ ?>
<!-- Include user sharing interface for liking, posting feed and recommending to friends -->
<?php include('sharingInterface.php') ?>
<?php } ?>
</div>
<?php if($quiz->isOwner($member->id) && !$quiz->isPublished()){ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Publish this quiz!</h2>
  <div class="content-container">
  <p>Hey! You're the owner of this unpublished quiz! If you feel that your quiz is ready, click the &quot;Publish Quiz&quot; button. Once your quiz is published, it will be listed on Quizroo. You will receive points when a user takes your quiz. You can get more points when they &quot;like&quot; your quiz, or no points when they &quot;dislike&quot; your quiz.</p>
  <p class="center">
  <input type="button" name="button" id="button" onclick="goToURL('publishQuiz.php?id=<?php echo $_GET['id']; ?>')" value="Publish Quiz!" />
  </p>
  </div>
</div>
<?php }}else{ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Opps, quiz not found!</h2>
  <div class="content-container">
  <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
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
