<?php require_once('Connections/kuizzroo.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

$colname_getQuiz = "-1";
if (isset($_GET['id'])) {
  $colname_getQuiz = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuiz = sprintf("SELECT quiz_name, quiz_description, quiz_picture, creation_date,  qb_quiz_cat.cat_name, qb_members.nickname FROM qb_quizzes, qb_quiz_cat, qb_members WHERE quiz_id = %s AND qb_quiz_cat.cat_id = qb_quizzes.fk_quiz_cat AND qb_quizzes.fk_member_id = qb_members.member_id", GetSQLValueString($colname_getQuiz, "int"));
$getQuiz = mysql_query($query_getQuiz, $kuizzroo) or die(mysql_error());
$row_getQuiz = mysql_fetch_assoc($getQuiz);
$totalRows_getQuiz = mysql_num_rows($getQuiz);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - Quiz Created!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript"></script>
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_quiz_created">Quiz Created</h3>
    Yay! Your quiz has been created! You can preview your quiz below, or go to manage quizzes to view statistics on your quiz.</div></div>
<div id="contentContainer">
  <div id="content">
    <div id="quizPreview">
      <h2><?php echo $row_getQuiz['quiz_name']; ?></h2>
      <p><img src="quiz_images/imgcrop.php?w=320&h=213&f=<?php echo $row_getQuiz['quiz_picture']; ?>" width="320" height="213" alt="" /></p>
      <p class="description"><?php echo $row_getQuiz['quiz_description']; ?></p>
      <p class="info">by <em><?php echo $row_getQuiz['nickname']; ?></em> on <?php echo date("F j, Y g:ia", strtotime($row_getQuiz['creation_date'])); ?> in the topic '<?php echo $row_getQuiz['cat_name']; ?>'</p>
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($getQuiz);
?>
