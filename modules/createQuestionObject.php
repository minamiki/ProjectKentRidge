<?php require("../Connections/quizroo.php");
// prepare result options
mysql_select_db($database_quizroo, $quizroo);
$querySQL = "SELECT result_id, result_title FROM q_results WHERE fk_quiz_id = ".GetSQLValueString($_GET['id'], "int");
$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
$row_resultID = mysql_fetch_assoc($resultID);

$results = array();

do{
	$results[] = array($row_resultID['result_id'], $row_resultID['result_title']);
}while($row_resultID = mysql_fetch_assoc($resultID));

mysql_free_result($resultID);

// check what to do
if(isset($_GET['load'])){
	require('../Connections/quizroo.php');
	mysql_select_db($database_quizroo, $quizroo);
	$query = sprintf("SELECT question_id, question, question_image, question_order FROM q_questions WHERE fk_quiz_id = %d", GetSQLValueString($_GET['id'], "int"));
	$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
	$row_getQuery = mysql_fetch_assoc($getQuery);
	$totalRows_getQuery = mysql_num_rows($getQuery);
	
	$question = 0;
	if($totalRows_getQuery > 0){
		do{
?>
<div id="q<?php echo $question; ?>" class="questionWidget">
<input type="hidden" name="uq<?php echo $question; ?>" id="uq<?php echo $question; ?>" value="<?php echo $row_getQuery['question_id']; ?>" />
<table width="660" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="30" scope="row"><a href="javascript:;" onclick="QuizQuestion.remove(<?php echo $question; ?>);"><img src="img/delete.png" alt="" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
    <th width="100" scope="row"><label for="question_<?php echo $question; ?>">Question <?php echo $question+1; ?></label></th>
    <td><span id="sprytextfield-q<?php echo $question; ?>">
      <input type="text" name="question_<?php echo $question; ?>" id="question_<?php echo $question; ?>" value="<?php echo $row_getQuery['question']; ?>" />
      <span class="textfieldRequiredMsg">A value is required.</span></span></td>
  </tr>
</table>
<div id="optionContainer_<?php echo $question; ?>">
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr class="optionTable">
      <th width="25">&nbsp;</th>
      <th width="80">&nbsp;</th>
      <th align="left">Option Value</th>
      <th width="120" align="center">Contributes to</th>
      <th width="100" align="center">Weightage</th>
    </tr>
  </table>
    <?php 
	$queryOption = sprintf("SELECT `option_id`, `option`, `fk_result`, `option_weightage` FROM q_options WHERE fk_question_id = %d ORDER BY option_id", GetSQLValueString($row_getQuery['question_id'], "int"));
	$getOption = mysql_query($queryOption, $quizroo) or die(mysql_error());
	$row_getOption = mysql_fetch_assoc($getOption);
	$totalRows_getOption = mysql_num_rows($getOption);
	
	$option = 0;
	
	if($totalRows_getOption > 0){
		do{
	?>
    <div id="cq<?php echo $question; ?>o<?php echo $option; ?>">
    <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <th width="25" scope="row"><input type="hidden" name="uq<?php echo $question; ?>o<?php echo $option; ?>" id="uq<?php echo $question; ?>o<?php echo $option; ?>" value="<?php echo $row_getOption['option_id']; ?>" /><a href="javascript:;" onclick="QuizQuestion.removeOption(<?php echo $question; ?>, <?php echo $option; ?>);"><img src="img/delete.png" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
      <th width="80" scope="row"><label for="q<?php echo $question; ?>o<?php echo $option; ?>" class="optionWidget-<?php echo $question; ?>">Option <?php echo $option+1; ?></label></th>
      <td width="270"><span id="sprytextfield-q<?php echo $question; ?>o<?php echo $option; ?>" class="sprytextfield">
        <input name="q<?php echo $question; ?>o<?php echo $option; ?>" type="text" class="optionField" id="q<?php echo $question; ?>o<?php echo $option; ?>" value="<?php echo $row_getOption['option']; ?>" />
        <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
      <td width="120"><select name="q<?php echo $question; ?>r<?php echo $option; ?>" class="optionSelect" id="q<?php echo $question; ?>r<?php echo $option; ?>">
          <?php foreach($results as $item){ ?>
          <option value="<?php echo $item[0]; ?>" <?php if($item[0] == $row_getOption['fk_result']){ echo "selected"; }; ?>><?php echo $item[1]; ?></option>
          <?php } ?>
      </select></td>
      <td width="100"><select name="q<?php echo $question; ?>w<?php echo $option; ?>" id="q<?php echo $question; ?>w<?php echo $option; ?>">
          <option value="1" <?php if(1 == $row_getOption['option_weightage']){ echo "selected"; }; ?>>A little</option>
          <option value="2" <?php if(2 == $row_getOption['option_weightage']){ echo "selected"; }; ?>>Somewhat</option>
          <option value="3" <?php if(3 == $row_getOption['option_weightage']){ echo "selected"; }; ?>>A lot</option>
      </select></td>
    </tr>
    </table>
    </div>
    <?php $option++; }while($row_getOption = mysql_fetch_assoc($getOption)); }?>
</div>
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <th valign="top" scope="row"><input type="button" name="addOptionBtn<?php echo $question; ?>" id="addOptionBtn<?php echo $question; ?>" value="Add new option" onClick="QuizQuestion.addOption(<?php echo $question; ?>)" /></th>
    </tr>
    <tr>
      <td valign="top" class="desc" scope="row">Create a new option for this question</td>
    </tr>
  </table>
</div>
<?php 		$question++;
		}while($row_getQuery = mysql_fetch_assoc($getQuery));
	}
}elseif(isset($_GET['delete'])){
	// delete the question
	require('member.php');
	require('quiz.php');
	
	// also pass in the member id for security check
	$quiz = new Quiz($_GET['id']);
	$member = new Member();
	if(!$quiz->removeQuestion($_GET['question'], $member->id)){
		echo "Delete not authorized";
	}else{
		echo "delete okie";
	}
}else{
// get result number
$question = $_GET['questionNumber'];
$quiz = $_GET['id'];
?>
<div id="q<?php echo $question; ?>" class="questionWidget">
<table width="660" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="30" scope="row"><a href="javascript:;" onclick="QuizQuestion.remove(<?php echo $question; ?>);"><img src="img/delete.png" alt="" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
    <th width="100" scope="row"><label for="question_<?php echo $question; ?>">Question <?php echo $question+1; ?></label></th>
    <td><span id="sprytextfield-q<?php echo $question; ?>">
      <input type="text" name="question_<?php echo $question; ?>" id="question_<?php echo $question; ?>" />
      <span class="textfieldRequiredMsg">A value is required.</span></span></td>
  </tr>
</table>
<div id="optionContainer_<?php echo $question; ?>">
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr class="optionTable">
      <th width="25">&nbsp;</th>
      <th width="80">&nbsp;</th>
      <th align="left">Option Value</th>
      <th width="120" align="center">Contributes to</th>
      <th width="100" align="center">Weightage</th>
    </tr>
    <tr>
      <th width="25" scope="row">&nbsp;</th>
      <th width="80" scope="row"><label for="q<?php echo $question; ?>o0" class="optionWidget-<?php echo $question; ?>">Option 1</label></th>
      <td width="270"><span id="sprytextfield-q<?php echo $question; ?>o0" class="sprytextfield">
        <input name="q<?php echo $question; ?>o0" type="text" class="optionField" id="q<?php echo $question; ?>o0" />
        <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
      <td width="120"><select name="q<?php echo $question; ?>r0" class="optionSelect" id="q<?php echo $question; ?>r0">
          <?php foreach($results as $item){ ?>
          <option value="<?php echo $item[0]; ?>"><?php echo $item[1]; ?></option>
          <?php } ?>
      </select></td>
      <td width="100"><select name="q<?php echo $question; ?>w0" id="q<?php echo $question; ?>w0">
          <option value="1">A little</option>
          <option value="2">Somewhat</option>
          <option value="3">A lot</option>
      </select></td>
    </tr>
    <tr>
      <th width="25" scope="row">&nbsp;</th>
      <th width="80" scope="row"><label for="q<?php echo $question; ?>o1" class="optionWidget-<?php echo $question; ?>">Option 2</label></th>
      <td width="270"><span id="sprytextfield-q<?php echo $question; ?>o1" class="sprytextfield">
        <input name="q<?php echo $question; ?>o1" type="text" class="optionField" id="q<?php echo $question; ?>o1" />
        <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
      <td width="120"><select name="q<?php echo $question; ?>r1" class="optionSelect" id="q<?php echo $question; ?>r1">
          <?php foreach($results as $item){ ?>
          <option value="<?php echo $item[0]; ?>"><?php echo $item[1]; ?></option>
          <?php } ?>
      </select></td>
      <td width="100"><select name="q<?php echo $question; ?>w1" id="q<?php echo $question; ?>w1">
          <option value="1">A little</option>
          <option value="2">Somewhat</option>
          <option value="3">A lot</option>
      </select></td>
    </tr>
  </table>
</div>
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <th valign="top" scope="row"><input type="button" name="addOptionBtn<?php echo $question; ?>" id="addOptionBtn<?php echo $question; ?>" value="Add new option" onClick="QuizQuestion.addOption(<?php echo $question; ?>)" /></th>
    </tr>
    <tr>
      <td valign="top" class="desc" scope="row">Create a new option for this question</td>
    </tr>
  </table>
</div>
<?php } ?>
