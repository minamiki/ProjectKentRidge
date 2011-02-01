<?php include("inc/header-php.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Take Quiz</title>
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
	Share.recommend($('#user-actions-container'),{'quiz_id': <?php echo $quiz_id ?>});
	Share.results($('#user-actions-container'),{'quiz_id': <?php echo $quiz_id ?>,'result_id':<?php echo $row_getResults['fk_result'] ?>});
	//Share.checkLike(<?php $quiz->isPublished() ?>);
</script>
<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<script type="text/javascript">
	/*
	 * Subscribe to Facebook Like event to handle it for our own data. 
	 */
	FB.Event.subscribe('edge.create', function(response) {
		  Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz_id ?>,'type':1});
	});
	
	/*
	 * Subscribe to Facebook Unlike event to handle it for our own data. 
	 */
	FB.Event.subscribe('edge.remove', function(response) {
		  Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz_id ?>,'type':0});
	});
</script>
</body>
</html>