<!-- creates one new option object for particular question of a particular quiz -->

<?php 
require("quizrooDB.php");
if(isset($_GET['delete'])){
	// delete the option
	require('member.php');
	require('quiz.php');
	
	// also pass in the member id for security check
	$quiz = new Quiz($_GET['id']);
	$member = new Member();
	if(!$quiz->removeOption($_GET['option'], $member->id)){
		echo "Delete not authorized";
	}
}else{
// get result number
$question = $_GET['questionNumber'];
$option = $_GET['optionNumber'];
$quiz = $_GET['id'];

// prepare result options
$querySQL = "SELECT result_id, result_title FROM q_results WHERE fk_quiz_id = ".GetSQLValueString($quiz, "int");
$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
$row_resultID = mysql_fetch_assoc($resultID);

$results = array();

do{
	$results[] = array($row_resultID['result_id'], $row_resultID['result_title']);
}while($row_resultID = mysql_fetch_assoc($resultID));

mysql_free_result($resultID);
?>
<!-- Shows that 1 option -->
<div id="cq<?php echo $question; ?>o<?php echo $option; ?>">
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="25" scope="row"><a href="javascript:;" onclick="QuizQuestion.removeOption(<?php echo $question; ?>, <?php echo $option; ?>);"><img src="img/delete.png" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
    <th width="80" scope="row"><label for="q<?php echo $question; ?>o<?php echo $option; ?>">Option</label></th>
    <td><span id="sprytextfield-q<?php echo $question; ?>o<?php echo $option; ?>" class="sprytextfield">
      <input name="q<?php echo $question; ?>o<?php echo $option; ?>" type="text" class="optionField" id="q<?php echo $question; ?>o<?php echo $option; ?>" />
    <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
    <td width="150"><select name="q<?php echo $question; ?>r<?php echo $option; ?>" class="optionSelect" id="q<?php echo $question; ?>r<?php echo $option; ?>">
	  <?php foreach($results as $item){ ?>
      <option value="<?php echo $item[0]; ?>"><?php echo $item[1]; ?></option>
      <?php } ?>
    </select></td>
    <td width="100"><select name="q<?php echo $question; ?>w<?php echo $option; ?>" id="q<?php echo $question; ?>w<?php echo $option; ?>">
      <option value="1">A little</option>
      <option value="2">Somewhat</option>
      <option value="3">A lot</option>
    </select></td>
  </tr>
</table>
</div>
<?php } ?>