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

$maxRows_get_pQuiz = 3;
$pageNum_get_pQuiz = 0;
if (isset($_GET['pageNum_get_pQuiz'])) {
  $pageNum_get_pQuiz = $_GET['pageNum_get_pQuiz'];
}
$startRow_get_pQuiz = $pageNum_get_pQuiz * $maxRows_get_pQuiz;

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_get_pQuiz = "SELECT quiz_id, quiz_name, quiz_description, quiz_picture FROM qb_quizzes";
$query_limit_get_pQuiz = sprintf("%s LIMIT %d, %d", $query_get_pQuiz, $startRow_get_pQuiz, $maxRows_get_pQuiz);
$get_pQuiz = mysql_query($query_limit_get_pQuiz, $kuizzroo) or die(mysql_error());
$row_get_pQuiz = mysql_fetch_assoc($get_pQuiz);

if (isset($_GET['totalRows_get_pQuiz'])) {
  $totalRows_get_pQuiz = $_GET['totalRows_get_pQuiz'];
} else {
  $all_get_pQuiz = mysql_query($query_get_pQuiz);
  $totalRows_get_pQuiz = mysql_num_rows($all_get_pQuiz);
}
$totalPages_get_pQuiz = ceil($totalRows_get_pQuiz/$maxRows_get_pQuiz)-1;

$colname_listTopics = "-1";
if (isset($_GET['id'])) {
  $colname_listTopics = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_listTopics = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture FROM qb_quizzes WHERE fk_quiz_cat = %s ORDER BY creation_date DESC", GetSQLValueString($colname_listTopics, "int"));
$listTopics = mysql_query($query_listTopics, $kuizzroo) or die(mysql_error());
$row_listTopics = mysql_fetch_assoc($listTopics);
$totalRows_listTopics = mysql_num_rows($listTopics);

$colname_getTopicInfo = "-1";
if (isset($_GET['id'])) {
  $colname_getTopicInfo = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getTopicInfo = sprintf("SELECT * FROM qb_quiz_cat WHERE cat_id = %s", GetSQLValueString($colname_getTopicInfo, "int"));
$getTopicInfo = mysql_query($query_getTopicInfo, $kuizzroo) or die(mysql_error());
$row_getTopicInfo = mysql_fetch_assoc($getTopicInfo);
$totalRows_getTopicInfo = mysql_num_rows($getTopicInfo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - create, publish and share quizzes online!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/queryLoader.css" type="text/css" />
</head>

<body>
<?php include("header.php"); ?>
<div id="pBarContainer">
  <div id="pBar">
    <h3>most popular</h3>
    <div id="slide_box">
      <?php do { ?>
        <div class="quiz_box">
          <div class="quiz_title"><?php echo $row_get_pQuiz['quiz_name']; ?></div>
          <a href="previewQuiz.php?id=<?php echo $row_get_pQuiz['quiz_id']; ?>"><img src="quiz_images/imgcrop.php?w=239&h=159&f=<?php echo $row_get_pQuiz['quiz_picture']; ?>" alt="<?php echo $row_get_pQuiz['quiz_description']; ?>" width="239" height="159" border="0" title="<?php echo $row_get_pQuiz['quiz_description']; ?>" /></a> </div>
        <?php } while ($row_get_pQuiz = mysql_fetch_assoc($get_pQuiz)); ?>
    </div>
  </div>
</div>
<div id="contentContainer">
  <div id="content">
    <h4 id="topicName"><?php echo $row_getTopicInfo['cat_name']; ?></h4>
    <p class="topicDescription"><?php echo $row_getTopicInfo['cat_desc']; ?></p>
    <?php if ($totalRows_listTopics > 0) { // Show if recordset not empty ?>
  <div id="newContainer">
    <?php do { ?>
      <div class="quiz_box">
        <div class="quiz_title"><?php echo $row_listTopics['quiz_name']; ?></div>
        <a href="previewQuiz.php?id=<?php echo $row_listTopics['quiz_id']; ?>"><img src="quiz_images/imgcrop.php?w=200&h=150&f=<?php echo $row_listTopics['quiz_picture']; ?>" alt="<?php echo $row_listTopics['quiz_description']; ?>" width="200" height="150" border="0" title="<?php echo $row_get_pQuiz['quiz_description']; ?>" /></a></div>
      <?php } while ($row_listTopics = mysql_fetch_assoc($listTopics)); ?>
    <div class="clear"></div>
  </div>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_listTopics == 0) { // Show if recordset empty ?>
  <div id="no_quiz">There are no quizzes for this topic!</div>
  <?php } // Show if recordset empty ?>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($get_pQuiz);

mysql_free_result($listTopics);

mysql_free_result($getTopicInfo);
?>
