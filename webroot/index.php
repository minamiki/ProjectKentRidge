<?php include("inc/header-php.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<![if !IE]><html xmlns="http://www.w3.org/1999/xhtml"><![endif]>
<!--[if lt IE 9]><html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"><![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo</title>
<?php include("inc/header-css.php");?>
<link href="css/dashboard.css" rel="stylesheet" type="text/css" />
<link href="css/recent.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="fb-root"></div>
<?php include("../modules/statusbar.php");?>
<?php include("../modules/dashboard.php"); ?>
<?php include("inc/footer-js.php"); ?>
<?php include('../modules/checkAchievements.php')?>
<?php
// get the member's facebook id
$facebookID = $member->id;
$achievement_array = array();
// Check for achievements
$achievement_array = checkAchievements($facebookID, $achievement_array);
// Retrieve details of achievements
$achievement_details = retrieveAchievements($achievement_array);
?>
<script type="text/javascript" src="js/Dashboard.js"></script>
<script type="text/javascript" src="js/Splash.js"></script>
<script type="text/javascript">
Splash.display(<?php echo $achievement_details?>);
</script>
</body>
</html>