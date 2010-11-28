<?php require_once('Connections/kuizzroo.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_getQuizInfo = "-1";
if (isset($_GET['id'])) {
  $colname_getQuizInfo = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuizInfo = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, creation_date,  qb_members.nickname, qb_quiz_cat.cat_name, (SELECT COUNT(question_id) FROM qb_questions WHERE fk_quiz_id = %s) AS question_count FROM qb_quizzes, qb_members, qb_quiz_cat WHERE quiz_id = %s AND qb_members.member_id = qb_quizzes.fk_member_id AND qb_quiz_cat.cat_id = qb_quizzes.fk_quiz_cat", GetSQLValueString($colname_getQuizInfo, "int"),GetSQLValueString($colname_getQuizInfo, "int"));
$getQuizInfo = mysql_query($query_getQuizInfo, $kuizzroo) or die(mysql_error());
$row_getQuizInfo = mysql_fetch_assoc($getQuizInfo);
$totalRows_getQuizInfo = mysql_num_rows($getQuizInfo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - Take a Quiz!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript"></script>
<script type="text/javascript">
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_take_a_quiz">Take a quiz</h3>
    Here's some information about the quiz! You can decide whether you want to take it or not. This quiz contains 
    <strong><?php echo $row_getQuizInfo['question_count']; ?> questions</strong>. </div></div>
<div id="contentContainer">
  <div id="content">
    <div id="quizPreview">
      <h2><?php echo $row_getQuizInfo['quiz_name']; ?></h2>
      <p><img src="quiz_images/imgcrop.php?w=320&amp;h=213&amp;f=<?php echo $row_getQuizInfo['quiz_picture']; ?>" width="320" height="213" alt="" /></p>
      <p class="description"><?php echo $row_getQuizInfo['quiz_description']; ?></p>
      <p class="info">by <em><?php echo $row_getQuizInfo['nickname']; ?></em> on <?php echo date("F j, Y g:ia", strtotime($row_getQuizInfo['creation_date'])); ?> in the topic '<?php echo $row_getQuizInfo['cat_name']; ?>'</p>
		<input name="takeQuizBtn" type="button" class="styleBtn" id="takeQuizBtn" onclick="MM_goToURL('parent','takeQuiz.php?id=<?php echo $row_getQuizInfo['quiz_id']; ?>');return document.MM_returnValue" value="Take Quiz now!" />
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($getQuizInfo);
?>
