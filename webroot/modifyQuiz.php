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

mysql_select_db($database_kuizzroo, $kuizzroo);
$query_listCat = "SELECT cat_id, cat_name FROM qb_quiz_cat";
$listCat = mysql_query($query_listCat, $kuizzroo) or die(mysql_error());
$row_listCat = mysql_fetch_assoc($listCat);
$totalRows_listCat = mysql_num_rows($listCat);

$colname_getQuiz = "-1";
if (isset($_GET['id'])) {
  $colname_getQuiz = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuiz = sprintf("SELECT quiz_id, quiz_name, quiz_description, fk_quiz_cat, quiz_picture FROM qb_quizzes WHERE quiz_id = %s", GetSQLValueString($colname_getQuiz, "int"));
$getQuiz = mysql_query($query_getQuiz, $kuizzroo) or die(mysql_error());
$row_getQuiz = mysql_fetch_assoc($getQuiz);
$totalRows_getQuiz = mysql_num_rows($getQuiz);

$colname_getResults = "-1";
if (isset($_GET['id'])) {
  $colname_getResults = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getResults = sprintf("SELECT result_id, result_title, result_description, result_picture FROM qb_results WHERE fk_quiz_id = %s", GetSQLValueString($colname_getResults, "int"));
$getResults = mysql_query($query_getResults, $kuizzroo) or die(mysql_error());
$row_getResults = mysql_fetch_assoc($getResults);
$totalRows_getResults = mysql_num_rows($getResults);

$colname_getQuestions = "-1";
if (isset($_GET['id'])) {
  $colname_getQuestions = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuestions = sprintf("SELECT question_id, question, question_order FROM qb_questions WHERE fk_quiz_id = %s", GetSQLValueString($colname_getQuestions, "int"));
$getQuestions = mysql_query($query_getQuestions, $kuizzroo) or die(mysql_error());
$row_getQuestions = mysql_fetch_assoc($getQuestions);
$totalRows_getQuestions = mysql_num_rows($getQuestions);
?><?php require_once('uploadFunctions.php');
// generate a one time hash key for the upload
$unikey = get_rand_id(8);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - Create a new quiz!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript" ></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
</head>
<?php
$checkTextField = 1;
$checkTextArea = 1;
$optionCounts = "";
?>
<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_modify_quiz">Modify Quiz</h3>
    Make changes to your quiz here! For quiz consistency and integrity purposes, adding of new results, questions or options are disabled. 
</div></div>
<div id="contentContainer">
  <div id="content">
    <form action="updateQuizEngine.php" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
      <fieldset>
        <h4>Quiz Information</h4>
        <p>The Quiz Information allows you to tell a potential quiz taker what insights your quiz intends to deliver.</p>
        <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th width="120" valign="top" scope="row"><label for="quiz_title">Title</label></th>
            <td><span id="sprytextfield0"><input name="quiz_title" type="text" id="quiz_title" value="<?php echo $row_getQuiz['quiz_name']; ?>" /><span class="textfieldRequiredMsg">A title is required.</span></span>
            <span class="desc">Give your Quiz a meaningful title! Your title will be the first thing that catches a reader's attention.</span></td>
          </tr>
          <tr>
            <th width="120" valign="top" scope="row"><label for="quiz_description">Description</label></th>
            <td><span id="sprytextarea0"><textarea name="quiz_description" id="quiz_description" cols="45" rows="5"><?php echo $row_getQuiz['quiz_description']; ?></textarea>
            <span class="textareaRequiredMsg">Description should not be blank!</span></span><span class="desc">Provide a short description on what your quiz is about.</span></td>
          </tr>
          <tr>
            <th valign="middle" scope="row"><label for="quiz_cat">Topic</label>
            <input name="quiz_id" type="hidden" id="quiz_id" value="<?php echo $row_getQuiz['quiz_id']; ?>" /></th>
            <td><select name="quiz_cat" id="quiz_cat">
              <?php
do {  
?>
              <option value="<?php echo $row_listCat['cat_id']?>"<?php if (!(strcmp($row_listCat['cat_id'], $row_getQuiz['fk_quiz_cat']))) {echo "selected=\"selected\"";} ?>><?php echo $row_listCat['cat_name']?></option>
              <?php
} while ($row_listCat = mysql_fetch_assoc($listCat));
  $rows = mysql_num_rows($listCat);
  if($rows > 0) {
      mysql_data_seek($listCat, 0);
	  $row_listCat = mysql_fetch_assoc($listCat);
  }
?>
            </select></td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <h4>Quiz Results</h4>
        <p>Quiz results appear at the end of each quiz. Depending on what options the quiz taker has chosen, the result which carries the most weightage from the options will be the final quiz result. You can add as many results as you like!        </p>
        <div id="createResultContainer">
          <?php $result = 1; do { ?>
            <div id="r<?php echo $result; ?>">
              <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                  <th colspan="2" valign="top" scope="row"><input type="hidden" name="id_r<?php echo $result; ?>" id="r<?php echo $result; ?>" value="<?php echo $row_getResults['result_id']; ?>" />
                  Result <?php echo $result; ?></th>
                </tr>
                <tr>
                  <th width="120" valign="top" scope="row"><label for="result_title_<?php echo $result; ?>">Title</label></th>
                  <td><span id="sprytextfield<?php echo $checkTextField++; ?>">
                    <input name="result_title_<?php echo $result; ?>" type="text" id="result_title_<?php echo $result; ?>" value="<?php echo $row_getResults['result_title']; ?>" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span> <span class="desc">Provide a title for this result!</span></td>
                </tr>
                <tr>
                  <th width="120" valign="top" scope="row"><label for="result_description_<?php echo $result; ?>">Description</label></th>
                  <td><span id="sprytextarea<?php echo $checkTextArea++; ?>">
                    <textarea name="result_description_<?php echo $result; ?>" id="result_description_<?php echo $result; ?>" cols="45" rows="5"><?php echo $row_getResults['result_description']; ?></textarea>
                    <span class="textareaRequiredMsg">Description should not be blank!</span></span> <span class="desc">Tell the quiz taker what this result means</span></td>
                </tr>
              </table>
            </div>
            <?php 
			$result++;
			} while ($row_getResults = mysql_fetch_assoc($getResults));
			  $rows = mysql_num_rows($getResults);
  if($rows > 0) {
      mysql_data_seek($getResults, 0);
	  $row_getResults = mysql_fetch_assoc($getResults);
  } ?>
        </div>
      </fieldset>
      <fieldset>
        <h4>Quiz Questions</h4>
        <p>The following section allows you to populate your quiz with question. You can provide several options for quiz takers to choose for each question. You should also specify the weightage of each option - how each option contributes to a result.</p>
        <div id="createQuestionContainer">
          <?php $question = 1; do { ?>
            <div id="q<?php echo $question; ?>">
              <table width="660" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                  <th width="100" scope="row"><label for="question_<?php echo $question; ?>">Question <?php echo $question; ?></label>
                  <input type="hidden" name="q<?php echo $question; ?>" id="q<?php echo $question; ?>" value="<?php echo $row_getQuestions['question_id']; ?>" /></th>
                  <td><span id="sprytextfield<?php echo $checkTextField++; ?>">
                    <input name="question_<?php echo $question; ?>" type="text" id="question_<?php echo $question; ?>" value="<?php echo $row_getQuestions['question']; ?>" />
                    <span class="textfieldRequiredMsg">A value is required.</span></span></td>
                </tr>
              </table>
              <div id="optionContainer_<?php echo $question; ?>">
                <table border="0" align="center" cellpadding="5" cellspacing="0">
                  <tr class="optionTable">
                    <th width="80">&nbsp;</th>
                    <th align="left">Option Value</th>
                    <th width="120" align="center">Contributes to</th>
                    <th width="100" align="center">Weightage</th>
                  </tr>
                  <?php
					mysql_select_db($database_kuizzroo, $kuizzroo);
					$query_getOptions = "SELECT option_id, `option`, fk_result, option_weightage FROM qb_options WHERE fk_question_id = ".$row_getQuestions['question_id'];
					$getOptions = mysql_query($query_getOptions, $kuizzroo) or die(mysql_error());
					$row_getOptions = mysql_fetch_assoc($getOptions);
					$totalRows_getOptions = mysql_num_rows($getOptions);
					
					$option = 1;
					$optionCounts .= $totalRows_getOptions."_";
				  
				   do{ ?>
                  <tr>
                    <th width="80" scope="row"><label for="q<?php echo $question; ?>o<?php echo $option; ?>">Option <?php echo $option; ?></label>
                      <input type="hidden" name="id_q<?php echo $question; ?>o<?php echo $option; ?>" id="q<?php echo $question; ?>o<?php echo $option; ?>" value="<?php echo $row_getOptions['option_id']; ?>" /></th>
                    <td width="270"><span id="sprytextfield<?php echo $checkTextField++; ?>">
                      <input name="q<?php echo $question; ?>o<?php echo $option; ?>" type="text" class="optionField" id="q<?php echo $question; ?>o<?php echo $option; ?>" value="<?php echo $row_getOptions['option']; ?>" />
                      <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
                    <td width="120"><select name="q<?php echo $question; ?>r<?php echo $option; ?>" class="optionSelect" id="q<?php echo $question; ?>r<?php echo $option; ?>">
                      <?php do {  ?>
                      <option value="<?php echo $row_getResults['result_id']?>"<?php if (!(strcmp($row_getResults['result_id'], $row_getOptions['fk_result']))) {echo "selected=\"selected\"";} ?>><?php echo $row_getResults['result_title']?></option>
                      <?php
} while ($row_getResults = mysql_fetch_assoc($getResults));
  $rows = mysql_num_rows($getResults);
  if($rows > 0) {
      mysql_data_seek($getResults, 0);
	  $row_getResults = mysql_fetch_assoc($getResults);
  }
?>
                    </select></td>
                    <td width="100"><select name="q<?php echo $question; ?>w<?php echo $option; ?>" id="q<?php echo $question; ?>w<?php echo $option; ?>">
                      <option value="1" <?php if (!(strcmp(1, $row_getOptions['option_weightage']))) {echo "selected=\"selected\"";} ?>>A little</option>
                      <option value="2" <?php if (!(strcmp(2, $row_getOptions['option_weightage']))) {echo "selected=\"selected\"";} ?>>Somewhat</option>
                      <option value="3" <?php if (!(strcmp(3, $row_getOptions['option_weightage']))) {echo "selected=\"selected\"";} ?>>A lot</option>
                    </select></td>
                  </tr>
                  <?php $option++; }while($row_getOptions = mysql_fetch_assoc($getOptions)); ?>
                </table>
              </div>
            </div>
            <?php $question++; } while ($row_getQuestions = mysql_fetch_assoc($getQuestions)); ?>
        </div>
      </fieldset>
      <fieldset>
        <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th scope="row"><input type="submit" name="submitBtn" id="submitBtn" value="Save Changes" /></th>
          </tr>
        </table>
        <input type="hidden" name="resultCount" id="resultCount" value="<?php echo $totalRows_getResults; ?>" /><input type="hidden" name="questionCount" id="questionCount" value="<?php echo $totalRows_getQuestions; ?>" /><input type="hidden" name="optionCounts" id="optionCounts" value="<?php echo substr($optionCounts, 0, strlen($optionCounts)-1); ?>" />
      </fieldset>
    </form>
</div>
</div>
<script type="text/javascript">
var sprytextfield = new Array();
var sprytextarea = new Array();
<?php for($i = 0; $i < $checkTextField; $i++){ ?>
sprytextfield[<?php echo $i; ?>] = new Spry.Widget.ValidationTextField("sprytextfield"+<?php echo $i; ?>, "none", {validateOn:["change"]});
<?php } ?>
<?php for($i = 0; $i < $checkTextArea; $i++){ ?>
sprytextarea[<?php echo $i; ?>] = new Spry.Widget.ValidationTextarea("sprytextarea"+<?php echo $i; ?>, {validateOn:["change"]});
<?php } ?>
</script>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($listCat);

mysql_free_result($getQuiz);

mysql_free_result($getResults);

mysql_free_result($getQuestions);

mysql_free_result($getOptions);
?>
