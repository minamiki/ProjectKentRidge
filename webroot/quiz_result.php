<?php
require("inc/header-php.php");
require("../modules/quiz.php");
require('../modules/variables.php');
$quiz = new Quiz($_POST['quiz_id']);
if(!$quiz->exists()){
	header("Location: previewQuiz.php");
}else{ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![if !IE]><html xmlns="http://www.w3.org/1999/xhtml"><![endif]>
<!--[if lt IE 9]><html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: <?php echo $quiz->quiz_name; ?></title>
<?php include("inc/header-css.php");?>
<link href="css/quiz.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/resultEngine.php"); ?>
<?php include("inc/footer-js.php"); ?>
<script type="text/javascript" src="js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details?>);
</script>
<script type="text/javascript">
$(document).ready(function(){
	// Enable recommendation of quiz
	Share.recommend($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>});
	// Enable sharing of quiz results on facebook wall
	Share.results($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'result_id':<?php echo $row_getResults['fk_result'] ?>});
});
</script>
</body>
</html>
<?php } ?>