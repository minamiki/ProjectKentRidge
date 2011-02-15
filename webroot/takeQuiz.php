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