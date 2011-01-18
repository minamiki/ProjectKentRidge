<?php // get result number
$question = $_GET['questionNumber'];
$option = $_GET['optionNumber']+1;
$checkTextField = $_GET['checkTextField'];

// prepare result options
$results = explode("_", urldecode($_GET['results']));
?>
<div id="q<?php echo $question; ?>r<?php echo $option; ?>">
<table border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th width="25" scope="row"><a href="javascript:;" onclick="removeField('q<?php echo $question; ?>r<?php echo $option; ?>', 'o', <?php echo $question; ?>);"><img src="images/delete.png" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a></th>
    <th width="80" scope="row"><label for="q<?php echo $question; ?>o<?php echo $option; ?>">Option <?php echo $option; ?></label></th>
    <td width="270"><span id="sprytextfield<?php echo $checkTextField; ?>">
      <input name="q<?php echo $question; ?>o<?php echo $option; ?>" type="text" class="optionField" id="q<?php echo $question; ?>o<?php echo $option; ?>" />
    <span class="textfieldRequiredMsg">Enter a value for this option!</span></span></td>
    <td width="120"><select name="q<?php echo $question; ?>r<?php echo $option; ?>" class="optionSelect" id="q<?php echo $question; ?>r<?php echo $option; ?>">
	  <?php for($i = 0; $i < sizeof($results); $i++){ ?>
      <option value="<?php echo ($i+1); ?>"><?php echo $results[$i]; ?></option>
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