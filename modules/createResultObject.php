<?php // get result number
if(isset($_GET['load'])){
	$unikey = $_GET['unikey'];
	require('quizrooDB.php');
	
	$query = sprintf("SELECT result_id, result_title, result_description, result_picture FROM q_results WHERE fk_quiz_id = %d", GetSQLValueString($_GET['id'], "int"));
	$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
	$row_getQuery = mysql_fetch_assoc($getQuery);
	$totalRows_getQuery = mysql_num_rows($getQuery);
	
	$result = 0;
	if($totalRows_getQuery > 0){
		do{ $count = 1;
?>
<div id="r<?php echo $result; ?>" class="resultWidget">
<input type="hidden" name="ur<?php echo $result; ?>" id="ur<?php echo $result; ?>" value="<?php echo $row_getQuery['result_id']; ?>" />
<table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th colspan="2" valign="top" scope="row"><a href="javascript:;" onclick="QuizResult.remove(<?php echo $result; ?>);"><img src="img/delete.png" alt="" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a> Result</th>
  </tr>
  <tr>
    <th width="120" valign="top" scope="row"><label for="result_title_<?php echo $result; ?>">Title</label></th>
    <td><span id="sprytextfield-result_title_<?php echo $result; ?>" class="sprytextfield"><input type="text" name="result_title_<?php echo $result; ?>" id="result_title_<?php echo $result; ?>" value="<?php echo $row_getQuery['result_title']; ?>" /><span class="textfieldRequiredMsg">A value is required.</span></span>
    <span class="desc">Provide a title for this result!</span></td>
  </tr>
  <tr>
    <th width="120" valign="top" scope="row"><label for="result_description_<?php echo $result; ?>">Description</label></th>
    <td><span id="sprytextarea-result_description_<?php echo $result; ?>" class="sprytextarea"><textarea name="result_description_<?php echo $result; ?>" id="result_description_<?php echo $result; ?>" cols="45" rows="5"><?php echo $row_getQuery['result_description']; ?></textarea><span class="textareaRequiredMsg">Description should not be blank!</span></span>
    <span class="desc">Tell the quiz taker what this result means</span></td>
  </tr>
  <tr>
    <th width="120" rowspan="4" valign="top" scope="row"><label>Picture</label>
      <input name="result_picture_<?php echo $result; ?>" type="hidden" id="result_picture_<?php echo $result; ?>" value="<?php echo $row_getQuery['result_picture']; ?>" /></th>
    <td><div id="swfupload-control-<?php echo $result; ?>" class="swfupload-control">
      <script>initUploader("result_picture_<?php echo $result; ?>")</script>
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="uploader-<?php echo $result; ?>" type="button" id="uploader-<?php echo $result; ?>" /></td>
          <td valign="middle" class="formDesc">Upload a new picture (jpg, gif or png); You can select more than 1 file!</td>
          </tr>
    </table>
<table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td><div id="selected-image-<?php echo $result; ?>" class="selected-image"></div></td>
    <td><p id="queuestatus-<?php echo $result; ?>"></p></td>
  </tr>
</table>
      <ol id="log-<?php echo $result; ?>" class="log">
      </ol>
    </div></td>
  </tr>
  <tr>
    <td><div id="pictureChoser_<?php echo $result; ?>"><?php if(sizeof(glob("../quiz_images/".$unikey."*")) > 0){ ?><table border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><span class="formDesc">OR click on a picture below to use it as the result picture</span></td>
  </tr>
  <tr>
    <td><?php // return uploaded images
	if($unikey != ""){ foreach(glob("../quiz_images/".$unikey."*") as $filename){ ?>
		<a href="javascript:;" onClick="selectImage(<?php echo $result; ?>, '<?php echo str_replace("'", "\\'", basename($filename)); ?>')"><img src="../quiz_images/imgcrop.php?w=80&h=60&f=<?php echo basename($filename); ?>" width="80" height="60" id="r<?php echo $result; ?>i<?php echo $count; ?>" class="selectImage"></a>
	<?php $count++; }} ?>
	</td>
  </tr>
</table><?php } ?></div></td>
  </tr>
</table>
</div>			
<?php 	$result++;
		}while($row_getQuery = mysql_fetch_assoc($getQuery));
	}
}elseif(isset($_GET['delete'])){
	// delete the result
	require('member.php');
	require('quiz.php');
	
	// also pass in the member id for security check
	$quiz = new Quiz($_GET['id']);
	$member = new Member();
	if(!$quiz->removeResult($_GET['result'], $member->id)){
		echo "Delete not authorized";
	}
}else{
$result = $_GET['resultNumber'];
$unikey = $_GET['unikey'];
$count = 1;
?>
<div id="r<?php echo $result; ?>" class="resultWidget">
<table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <th colspan="2" valign="top" scope="row"><a href="javascript:;" onclick="QuizResult.remove(<?php echo $result; ?>);"><img src="img/delete.png" alt="" width="16" height="16" border="0" align="absmiddle" title="Remove" /></a> Result</th>
  </tr>
  <tr>
    <th width="120" valign="top" scope="row"><label for="result_title_<?php echo $result; ?>">Title</label></th>
    <td><span id="sprytextfield-result_title_<?php echo $result; ?>" class="sprytextfield"><input type="text" name="result_title_<?php echo $result; ?>" id="result_title_<?php echo $result; ?>" /><span class="textfieldRequiredMsg">A value is required.</span></span>
    <span class="desc">Provide a title for this result!</span></td>
  </tr>
  <tr>
    <th width="120" valign="top" scope="row"><label for="result_description_<?php echo $result; ?>">Description</label></th>
    <td><span id="sprytextarea-result_description_<?php echo $result; ?>" class="sprytextarea"><textarea name="result_description_<?php echo $result; ?>" id="result_description_<?php echo $result; ?>" cols="45" rows="5"></textarea><span class="textareaRequiredMsg">Description should not be blank!</span></span>
    <span class="desc">Tell the quiz taker what this result means</span></td>
  </tr>
  <tr>
    <th width="120" rowspan="4" valign="top" scope="row"><label>Picture</label>
      <input name="result_picture_<?php echo $result; ?>" type="hidden" id="result_picture_<?php echo $result; ?>" value="" /></th>
    <td><div id="swfupload-control-<?php echo $result; ?>" class="swfupload-control">
      <script>initUploader("result_picture_<?php echo $result; ?>")</script>
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="uploader-<?php echo $result; ?>" type="button" id="uploader-<?php echo $result; ?>" /></td>
          <td valign="middle" class="formDesc">Upload a new picture (jpg, gif or png); You can select more than 1 file!</td>
          </tr>
    </table>
<table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td><div id="selected-image-<?php echo $result; ?>" class="selected-image"></div></td>
    <td><p id="queuestatus-<?php echo $result; ?>"></p></td>
  </tr>
</table>
      <ol id="log-<?php echo $result; ?>" class="log">
      </ol>
    </div></td>
  </tr>
  <tr>
    <td><div id="pictureChoser_<?php echo $result; ?>"><?php if(sizeof(glob("../quiz_images/".$unikey."*")) > 0){ ?><table border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><span class="formDesc">OR click on a picture below to use it as the result picture</span></td>
  </tr>
  <tr>
    <td><?php // return uploaded images
	if($unikey != ""){ foreach(glob("../quiz_images/".$unikey."*") as $filename){ ?>
		<a href="javascript:;" onClick="selectImage(<?php echo $result; ?>, '<?php echo basename($filename); ?>')"><img src="../quiz_images/imgcrop.php?w=80&h=60&f=<?php echo basename($filename); ?>" width="80" height="60" id="r<?php echo $result; ?>i<?php echo $count; ?>" class="selectImage"></a>
	<?php $count++; }} ?>
	</td>
  </tr>
</table><?php } ?></div></td>
  </tr>
</table>
</div>
<?php } ?>