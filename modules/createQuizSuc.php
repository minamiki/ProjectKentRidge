<?php require('../modules/quizrooDB.php'); ?>
<?php require("../modules/quiz.php");
$quiz = new Quiz($_GET['id']);
?>
<div id="created-preamble" class="framePanel rounded">
  <h2 id="title_quiz_created">Quiz Created</h2>
  <div class="content-container">
  <p>Yay! Your quiz has been created! Your newly created quiz is <strong>unpublished</strong>. Unpublished quizzes will not appear in the quiz listings or searches. You can let your friends have a sneak preview of your unpublished quiz  by sharing the link below:</p>
  <p class="center"><a href="http://apps.facebook.com/quizroo/previewQuiz.php?id=<?php echo $_GET['id']; ?>" target="_blank">http://apps.facebook.com/quizroo/previewQuiz.php?id=<?php echo $_GET['id']; ?></a></p>
  <p>Do note that you <em>will not</em> receive points from users taking your unpublished quiz. You can publish your quiz anytime by clicking the &quot;Publish Quiz&quot; button at the preview URL, or through the quiz manager.</p>
  </div>
</div>
