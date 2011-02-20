<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quizroo System Report</title>
</head>
<?php require('system.php');
$system = new System(); ?>
<body>
<pre><?php $system->displayStats(); ?></pre>
<pre>Last Updated on <?php echo date("F j, Y g:ia"); ?></pre>
</body>
</html>

