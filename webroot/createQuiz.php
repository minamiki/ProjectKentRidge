<?php include("inc/header-php.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Create Quiz</title>
<?php include("inc/header-css.php");?>
<link href="css/uploader.css" rel="stylesheet" type="text/css" />
<link href="css/createQuiz.css" rel="stylesheet" type="text/css" />
<link href="css/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="css/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/variables.php");?>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/createQuizMain.php"); ?>
<?php include("inc/footer-js.php"); ?>
<script src="js/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="js/SpryValidationTextField.js" type="text/javascript"></script>
<script src="js/swf.upload.js"type="text/javascript"></script>
<script src="js/jquery.swfupload.js" type="text/javascript"></script>
<script type="text/javascript">
var unikey = "<?php echo $unikey ?>";
</script>
<script src="js/Quiz.create.js" type="text/javascript"></script>
<script src="js/swf.multi-uploader.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	// init the validators
	QuizValidate.init();
	<?php if(isset($_GET['step'])){ ?>
	QuizInfo.init(<?php echo $quiz_id; ?>, '<?php echo $unikey; ?>');
	<?php switch($_GET['step']){ case 1: ?>
	scanInitUploader();
	<?php break; case 2: ?>
	QuizResult.init();
	<?php break; case 3: ?>
	QuizQuestion.init();	
	<?php break; case 4: ?>
	<?php }}else{ ?>
	initUploader(0);
	<?php } ?>
});
</script>
</body>
</html>