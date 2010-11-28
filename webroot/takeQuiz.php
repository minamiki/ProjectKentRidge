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

$colname_getQuizQuestions = "-1";
if (isset($_GET['id'])) {
  $colname_getQuizQuestions = $_GET['id'];
}
mysql_select_db($database_kuizzroo, $kuizzroo);
$query_getQuizQuestions = sprintf("SELECT * FROM qb_questions WHERE fk_quiz_id = %s", GetSQLValueString($colname_getQuizQuestions, "int"));
$getQuizQuestions = mysql_query($query_getQuizQuestions, $kuizzroo) or die(mysql_error());
$row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions);
$totalRows_getQuizQuestions = mysql_num_rows($getQuizQuestions);

$question_count = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - Quiz: <?php echo $row_getQuizInfo['quiz_name']; ?></title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript" ></script>
<script src="scripts/sw.slideshow.js" type="text/javascript" ></script>
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_take_a_quiz">Take a quiz</h3>
    You're now taking the quiz,<em> &quot;<?php echo $row_getQuizInfo['quiz_name']; ?>&quot;</em> by <?php echo $row_getQuizInfo['nickname']; ?>. You may stop taking the quiz anytime by navigating away from this page. No data will be collected unless you complete the quiz.</div>
</div>
<div id="contentContainer">
  <div id="content">
<div id="progress_bar">
  <div id="progress"></div>
</div>
<form name="takeQuiz" id="takeQuiz" action="quiz_result.php" method="post">
      <input type="hidden" name="quiz_id" value="<?php echo $row_getQuizInfo['quiz_id']; ?>" />
      <div id="questionContainer">
        <div id="question_reel">
          <?php do { 
			mysql_select_db($database_kuizzroo, $kuizzroo);
			$query_getOptions = "SELECT * FROM qb_options WHERE fk_question_id = ".$row_getQuizQuestions['question_id'];
			$getOptions = mysql_query($query_getOptions, $kuizzroo) or die(mysql_error());
			$row_getOptions = mysql_fetch_assoc($getOptions);
			$totalRows_getOptions = mysql_num_rows($getOptions);	
			
			$option_count = 1;	  
		  ?>
            <div class="question_slide">
              <fieldset>
                <h4>Question <?php echo $question_count; ?></h4>
                <p><?php echo $row_getQuizQuestions['question']; ?></p>
                <table width="100%" border="0" cellpadding="5" cellspacing="0">
                  <?php do { ?>
                    <tr>
                      <th width="30" scope="row"><input type="radio" name="q<?php echo $question_count; ?>" id="q<?php echo $question_count; ?>o<?php echo $option_count; ?>" value="<?php echo $row_getOptions['option_id']; ?>" /></th>
                      <td><label for="q<?php echo $question_count; ?>o<?php echo $option_count; ?>"><?php echo $row_getOptions['option']; ?></label></td>
                    </tr>
                  <?php $option_count++; } while ($row_getOptions = mysql_fetch_assoc($getOptions)); ?>
                </table>  
                <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0" id="question_navigation">
                  <?php if($question_count != $totalRows_getQuizQuestions){ if($question_count == 1){ ?>
                    <tr>
                      <td align="left" scope="row">&nbsp;</td>
                      <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
                  </tr>
                  <?php }else{ ?>
                    <tr>
                      <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                      <td align="right"><input name="nextBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="nextBtn<?php echo $question_count; ?>" value="Next" /></td>
                  </tr>
                  <?php }}else{ ?>
                    <tr>
                      <td align="left" scope="row"><input name="prevBtn<?php echo $question_count; ?>" type="button" class="styleBtn" id="prevBtn<?php echo $question_count; ?>" value="Previous" /></td>
                      <td align="right"><input name="finishQuiz" type="submit" class="styleBtn" id="finishQuiz" value="Complete Quiz" /></td>
                  </tr>
                  <?php } ?>
                </table>
              </fieldset>
            </div>
            <?php $question_count++; mysql_free_result($getOptions); } while ($row_getQuizQuestions = mysql_fetch_assoc($getQuizQuestions)); ?>
        </div>
        <div id="question_paging">
        	 <?php for($i = 0; $i < $totalRows_getQuizQuestions; $i++) { ?>
             <!--<a href="#" rel="<?php echo ($i+1); ?>"><?php echo ($i+1); ?></a>-->
             <span title="<?php echo ($i+1); ?>"><?php echo ($i+1); ?></span>
             <?php } ?>
        </div>
      </div>
    </form>
  </div>
</div>
<?php include("footer.php"); ?>
</body>
</html>
<?php
mysql_free_result($getQuizInfo);

mysql_free_result($getQuizQuestions);
?>
