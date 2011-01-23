<?php require('../Connections/quizroo.php'); ?>
<?php
require('quiz.php');
require('member.php');

$quiz = new Quiz($_GET['id']);
?>
<div id="created-preamble" class="frame rounded">
  <h3 id="title_quiz_created">Quiz Created</h3>
  <p>Yay! Your quiz has been created! You can choose to preview your quiz first, or publish your quiz now.</p>
  <p>Your newly created quiz is <strong>unpublished</strong>. Unpublished quizzes will not appear in the quiz listings or searches. You can let your friends have a sneak prebiew of your unpublished quiz  by sharing the link below:</p>
  <p><a href="http://apps.facebook.com/quizroo/previewQuiz.php?id=<?php echo $_GET['id']; ?>">http://apps.facebook.com/quizroo/previewQuiz.php?id=<?php echo $_GET['id']; ?></a></p>
  <p>Do note that you <em>will not</em> receive points from users taking your unpublished quiz. You can published your quiz anytime by clicking the &quot;Publish Quiz&quot; button below. The button is also avaliable in the preview link above.</p>
  
  <span class="center">
  <input name="publishBtn2" type="button" class="orangeBtn" id="previewBtn" onclick="goToURL('http://apps.facebook.com/quizroo/previewQuiz.php?id=<?php echo $_GET['id']; ?>')" value="Preview Quiz" />&nbsp;
  <input name="publishBtn" type="button" id="publishBtn" onclick="goToURL('../modules/publishEngine.php')" value="Publish Quiz!" />
</span></div>
