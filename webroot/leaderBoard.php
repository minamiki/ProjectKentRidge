<!-- This page is merely an UI which includes other pages to display the page with some statistical information such as fun facts and member ranking-->

<?php include("inc/header-php.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo Leaderboard</title>
<?php include("inc/header-css.php");?>
<link href="css/leaderboard.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/leaderBoardDisplay.php"); ?>
<?php include("inc/footer-js.php"); ?>
</body>
</html>