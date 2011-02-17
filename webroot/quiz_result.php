<?php include("inc/header-php.php"); ?>
<?php // populate the quiz information for facebook
require("../modules/quiz.php");
require('../modules/variables.php');
$quiz = new Quiz($_POST['quiz_id']);
if(!$quiz->exists()){
	header("Location: previewQuiz.php");
}else{ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: <?php echo $quiz->quiz_name; ?></title>
<meta property="og:title" content="<?php echo $quiz->quiz_name; ?>"/>
<meta property="og:type" content="game"/>
<meta property="og:image" content="<?php echo $VAR_URL."quiz_images/imgcrop.php?w=50&amp;h=50&amp;f=".$quiz->quiz_picture; ?>"/> 
<meta property="og:url" content="<?php echo $FB_CANVAS."previewQuiz.php?id=".$quiz->quiz_id; ?>"/>
<meta property="og:site_name" content="Quizroo"/>
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>"/> 
<?php include("inc/header-css.php");?>
</head>

<body>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/resultEngine.php"); ?>
<?php include("inc/footer-js.php"); ?>
<script type="text/javascript" src="js/Splash.js"></script>
<script type="text/javascript">
	Splash.display(<?php echo $achievement_details?>);
</script>
<script type="text/javascript" src="js/Share.js"></script>
<script>
	Share.recommend($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>});
	Share.results($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'result_id':<?php echo $row_getResults['fk_result'] ?>});
	//Share.checkLike(<?php $quiz->isPublished() ?>);
</script>
<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<script type="text/javascript">
	/*
	 * Subscribe to Facebook Like event to handle it for our own data. 
	 */
	FB.Event.subscribe('edge.create', function(response) {
		  Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'type':1});
	});
	
	/*
	 * Subscribe to Facebook Unlike event to handle it for our own data. 
	 */
	FB.Event.subscribe('edge.remove', function(response) {
		  Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'type':0});
	});
</script>
</body>
</html>
<?php } ?>