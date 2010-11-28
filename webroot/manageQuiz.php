<?php require_once('Connections/kuizzroo.php'); ?>
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

$colname_getQuizzes = "-1";
if (isset($_SESSION['MM_MemberID'])) {
  $colname_getQuizzes = $_SESSION['MM_MemberID'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuizzes = sprintf("SELECT quiz_id, quiz_name, quiz_picture, creation_date FROM qb_quizzes WHERE fk_member_id = %s", GetSQLValueString($colname_getQuizzes, "int"));
$getQuizzes = mysql_query($query_getQuizzes, $kuizzroo) or die(mysql_error());
$row_getQuizzes = mysql_fetch_assoc($getQuizzes);
$totalRows_getQuizzes = mysql_num_rows($getQuizzes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - Manage Quizzes</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript"></script>
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_manage_quizzes">Manage Quizzes</h3>
    You can modify your quiz data here, as well as view statistics on the response on your quizes from quiz takers.</div></div>
<div id="contentContainer">
  <div id="content">
    
    <?php if(isset($_GET['msg'])){ ?>
    <div id="alertGreen"><?php echo urldecode($_GET['msg']); ?></div>
    <?php } ?>
    <div class="styleForm">
      <fieldset class="manage">
        <h4>My Quizzes</h4>
        <p>Here's the list of all the <span class="list_quizzes"><strong><?php echo $totalRows_getQuizzes ?></strong></span> quizzes you have created on kuizzroo. Click on <img src="images/edit.png" alt="Edit" width="16" height="16" border="0" align="absmiddle" /> to modify a quiz or <img src="images/delete.png" alt="Delete" width="16" height="16" border="0" align="absmiddle" /> to delete it.</p>
        <?php if ($totalRows_getQuizzes > 0) { // Show if recordset not empty ?>
          <div class="list_quizzes">
            <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
              <tr>
                <th>Picture</th>
                <th>Quiz Title</th>
                <th>Creation Date</th>
                <th>Action</th>
              </tr>
              <?php do { ?>
                <tr>
                  <th align="center"><img name="" src="quiz_images/imgcrop.php?w=90&h=60&f=<?php echo $row_getQuizzes['quiz_picture']; ?>" width="90" height="60" alt="" /></th>
                  <td><?php echo $row_getQuizzes['quiz_name']; ?></td>
                  <td align="center" class="date"><?php echo date("F j, Y g:ia", strtotime($row_getQuizzes['creation_date'])); ?></td>
                  <td align="center" valign="middle"><a href="modifyQuiz.php?id=<?php echo $row_getQuizzes['quiz_id']; ?>"><img src="images/edit.png" alt="Edit" width="16" height="16" border="0" align="absmiddle" /></a> <a href="deleteQuiz.php?id=<?php echo $row_getQuizzes['quiz_id']; ?>"> <img src="images/delete.png" alt="Delete" width="16" height="16" border="0" align="absmiddle" onclick="return confirm('Are you sure you want to delete the quiz \'<?php echo $row_getQuizzes['quiz_name']; ?>\'? ALL question, results, options and images belonging to this quiz will be deleted');" /></a></td>
                </tr>
                <?php } while ($row_getQuizzes = mysql_fetch_assoc($getQuizzes)); ?>
            </table>
          </div>
          <?php } // Show if recordset not empty ?>
        <?php if ($totalRows_getQuizzes == 0) { // Show if recordset empty ?>
  <div class="no_quizzes">You do not have any quizzes to manage. Create one now!</div>
  <?php } // Show if recordset empty ?>
      </fieldset>
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($getQuizzes);

mysql_free_result($getQuizzes);
?>
