<?php include("inc/header-php.php"); ?>
<?php require('../modules/quiz.php');
require('../modules/variables.php');
if(isset($_GET['id'])){
	// check if id is empty
	if($_GET['id'] == ""){
		// attempt to extract the other id parameter
		$url_vars = explode('&', $_SERVER["QUERY_STRING"]);
		$url_id = 0;
		foreach($url_vars as $test_id){
			if(is_numeric($test_id)){
				$url_id = $test_id;
				break;
			}
		}
	}else{
		// give the real id
		$url_id = $_GET['id'];	
	}
}else{
	// attempt to extract the other id parameter
	$url_vars = explode('&', $_SERVER["QUERY_STRING"]);
	$url_id = 0;
	foreach($url_vars as $test_id){
		if(is_numeric($test_id)){
			$url_id = $test_id;
			break;
		}
	}
}
$quiz = new Quiz($url_id);
if($quiz->exists()){
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Quizroo: Preview Quiz</title>
<meta property="og:title" content="<?php echo $quiz->quiz_name; ?>"/>
<meta property="og:type" content="game"/>
<meta property="og:image" content="<?php echo $VAR_URL."quiz_images/imgcrop.php?w=50&amp;h=50&amp;f=".$quiz->quiz_picture; ?>"/> 
<meta property="og:url" content="<?php echo $FB_CANVAS."previewQuiz.php?id=".$quiz->quiz_id; ?>"/>
<meta property="og:site_name" content="Quizroo"/>
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>"/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php }else{ ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta property="og:title" content="Quizroo Quiz"/> 
<meta property="og:type" content="game"/>
<meta property="og:image" content=""/> 
<meta property="og:url" content=""/>
<meta property="og:site_name" content="Quizroo"/>
<meta property="fb:app_id" content="<?php echo $FB_APPID; ?>"/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo Quiz</title>
<?php } ?>
<?php include("inc/header-css.php");?>
</head>

<body>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/previewQuiz.php"); ?>
<?php include("inc/footer-js.php"); ?>
<?php if($quiz->hasTaken($member->id)){ ?>
<!-- Include user sharing interface for liking, posting feed and recommending to friends -->
<script type="text/javascript" src="js/Share.js"></script>
<script>
	Share.recommend($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>});
	//Share.checkLike(<?php $quiz->isPublished() ?>);
</script>
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
		Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'type':-1});
	});
</script>
<?php } ?>
</body>
</html>