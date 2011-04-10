<?php // delete temp
require("../modules/member.php");
require("../modules/quiz.php");

$member = new Member();
$quiz = new Quiz($_GET['id']);
$quiz->archive($member->id);

header("Location: ../webroot/manageQuiz.php?msg=".urlencode("You have deleted the quiz '".$quiz->quiz_name."'"));
?>