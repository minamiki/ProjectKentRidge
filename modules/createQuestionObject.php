<?php // get result number
$question = $_GET['questionNumber']+1;
$checkTextField = $_GET['checkTextField'];

// prepare result options
$results = explode("_", urldecode($_GET['results']));
?>
<div id="q<?php echo $question; ?>">
<table width="660" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="30" scope="row"><a href="javascript:;" onclick="removeField('q<?php echo $question; ?>', 'q', <?php echo $question; ?>);"><img src="images/delete.png" alt="" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
    <th width="100" scope="row"><label for="question_<?php echo $question; ?>">Question <?php echo $question; ?></label></th>
    <td><span id="sprytextfield<?php echo $checkTextField++; ?>">
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
      <th width="80" scope="row"><label for="q<?php echo $question; ?>o1">Option 1</label></th>
      <td width="270"><span id="sprytextfield<?php echo $checkTextField++; ?>">
        <input name="q<?php echo $question; ?>o1" type="text" class="optionField" id="q<?php echo $question; ?>o1" />
        <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
      <td width="120"><select name="q<?php echo $question; ?>r1" class="optionSelect" id="q<?php echo $question; ?>r1">
          <?php for($i = 0; $i < sizeof($results); $i++){ ?>
          <option value="<?php echo ($i+1); ?>"><?php echo $results[$i]; ?></option>
          <?php } ?>
      </select></td>
      <td width="100"><select name="q<?php echo $question; ?>w1" id="q<?php echo $question; ?>w1">
          <option value="1">A little</option>
          <option value="2">Somewhat</option>
          <option value="3">A lot</option>
      </select></td>
    </tr>
    <tr>
      <th width="25" scope="row">&nbsp;</th>
      <th width="80" scope="row"><label for="q<?php echo $question; ?>o2">Option 2</label></th>
      <td width="270"><span id="sprytextfield<?php echo $checkTextField++; ?>">
        <input name="q<?php echo $question; ?>o2" type="text" class="optionField" id="q<?php echo $question; ?>o2" />
        <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
      <td width="120"><select name="q<?php echo $question; ?>r2" class="optionSelect" id="q<?php echo $question; ?>r2">
          <?php for($i = 0; $i < sizeof($results); $i++){ ?>
          <option value="<?php echo ($i+1); ?>"><?php echo $results[$i]; ?></option>
          <?php } ?>
      </select></td>
      <td width="100"><select name="q<?php echo $question; ?>w2" id="q<?php echo $question; ?>w2">
          <option value="1">A little</option>
          <option value="2">Somewhat</option>
          <option value="3">A lot</option>
      </select></td>
    </tr>
  </table>
</div>
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <th valign="top" scope="row"><input type="button" name="addOptionBtn<?php echo $question; ?>" id="addOptionBtn<?php echo $question; ?>" value="Add new option" onClick="addOption(<?php echo $question; ?>)" /></th>
    </tr>
    <tr>
      <td valign="top" class="desc" scope="row">Create a new result for this quiz</td>
    </tr>
  </table>
</div>
