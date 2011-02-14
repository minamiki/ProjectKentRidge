<?php // delete temp
require("../modules/member.php");
require("../modules/quiz.php");

$member = new Member();
$quiz = new Quiz($_GET['id']);
//$quiz->delete($member->id);
$quiz->archive($_GET['id']);

header("Location: ../webroot/manageQuiz.php");
?>