<?php include('inc/header-php.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo: Search</title>
<?php include("inc/header-css.php");?>
<link href="css/search.css" rel="stylesheet" type="text/css" />
<link href="css/paging.css" rel="stylesheet" type="text/css" />
<link href="css/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/searchEngine.php"); ?>
<?php include("inc/footer-js.php"); ?>
<script src="js/SpryValidationTextField.js" type="text/javascript"></script>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");

$(document).ready(function(){
	$("#queryType0").click(function(){
		$("#question_option0").removeAttr("disabled");
		$("#question_option1").attr("disabled", "disabled");
	});
	$("#queryType1").click(function(){
		$("#question_option1").removeAttr("disabled");
		$("#question_option0").attr("disabled", "disabled");
	});
});
</script>
</body>
</html>