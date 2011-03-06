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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php include("inc/header-css.php");?>
<link href="css/quiz.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/takeQuiz.php"); ?>
<?php include("inc/footer-js.php"); ?>
<script type="text/javascript" src="js/Quiz.js"></script>
<script>
// update the slider height
$(document).ready(function(){
    var question_slides = $(".question_slide");
	var maxHeight = 600;
     
    //Loop all the slides
    question_slides.each(function() {       
        //Store the highest value
        if($(this).height() > maxHeight){
            maxHeight = $(this).height();
        }
    });
	 
    //Set the height
    $("#questionContainer").height(maxHeight);
	FB.Canvas.setAutoResize();
});
</script>
</body>
</html>
<?php } ?>