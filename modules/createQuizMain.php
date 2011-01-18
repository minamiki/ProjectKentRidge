<?php require_once('../Connections/quizroo.php'); ?>
<?php
mysql_select_db($database_quizroo, $quizroo);
$query_listCat = "SELECT cat_id, cat_name FROM q_quiz_cat";
$listCat = mysql_query($query_listCat, $quizroo) or die(mysql_error());
$row_listCat = mysql_fetch_assoc($listCat);
$totalRows_listCat = mysql_num_rows($listCat);
?><?php require_once('uploadFunctions.php');
// generate a one time hash key for the upload
$unikey = get_rand_id(8);
?>
<div id="create-quiz">
  <form action="../modules/createQuizEngine.php" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
    <fieldset>
      <h4>Quiz Information</h4>
      <p>The Quiz Information allows you to tell a potential quiz taker what insights your quiz intends to deliver.</p>
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_title">Title</label></th>
          <td><span id="sprytextfield0"><input type="text" name="quiz_title" id="quiz_title" /><span class="textfieldRequiredMsg">A title is required.</span></span>
          <span class="desc">Give your Quiz a meaningful title! Your title will be the first thing that catches a reader's attention.</span></td>
        </tr>
        <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_description">Description</label></th>
          <td><span id="sprytextarea0"><textarea name="quiz_description" id="quiz_description" cols="45" rows="5"></textarea>
          <span class="textareaRequiredMsg">Description should not be blank!</span></span><span class="desc">Provide a short description on what your quiz is about.</span></td>
        </tr>
        <tr>
          <th valign="middle" scope="row"><label for="quiz_cat">Topic</label></th>
          <td><select name="quiz_cat" id="quiz_cat">
              <?php
do {  
?>
              <option value="<?php echo $row_listCat['cat_id']?>"><?php echo $row_listCat['cat_name']?></option>
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
        <tr>
          <th valign="top" scope="row"><label>Quiz Picture</label></th>
          <td class="desc"><div id="quizImagePreview">You can select an image to use for the quiz in the upload images section below</div>              
            <input type="hidden" name="quiz_picture" id="quiz_picture" val="" />
          <input name="quiz_member_id" type="hidden" id="quiz_member_id" value="<?php echo $_SESSION['MM_MemberID']; ?>" /></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <h4>Upload Images</h4>
      <p>You can upload <strong>all</strong> the pictures required for this quiz with this upload tool and specify later which images will be used for the main quiz image and the result images. Images can be gif, jpg or png images with a maximum size of 2MB. <strong>Multiple</strong> files can be selected and uploads will be queued accordingly.</p>
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th width="120" valign="top" scope="row"><label>Quiz Images</label></th>
          <td><div id="swfupload-control">
              <input name="uploader" type="button" id="uploader" />
<p id="queuestatus"></p>
              <ol id="log">
              </ol>
          </div></td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <h4>Quiz Results</h4>
      <p>Quiz results appear at the end of each quiz. Depending on what options the quiz taker has chosen, the result which carries the most weightage from the options will be the final quiz result. You can add as many results as you like!        </p>
      <div id="createResultContainer"></div>
      <table border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
        </tr>
        <tr>
          <th valign="top" scope="row"><input type="button" name="addResultBtn" id="addResultBtn" value="Add new result" onclick="addResult()" /></th>
        </tr>
        <tr>
          <td valign="top" class="desc" scope="row">Create a new result for this quiz</td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <h4>Quiz Questions</h4>
      <p>The following section allows you to populate your quiz with question. You can provide several options for quiz takers to choose for each question. You should also specify the weightage of each option - how each option contributes to a result.</p>
      <div id="createQuestionContainer">
      </div>
      <table border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
        </tr>
        <tr>
          <th valign="top" scope="row"><input type="button" name="addQuestionBtn" id="addQuestionBtn" value="Add new question" onclick="addQuestion()" /></th>
        </tr>
        <tr>
          <td valign="top" class="desc" scope="row">Create a new question for this quiz</td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <h4>Create Quiz</h4>
      <p>You are just one step away from creating your quiz! Click the Create Quiz button below, and if everything goes well, your quiz will be created!</p>
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th scope="row"><input type="submit" name="submitBtn" id="submitBtn" value="Create Quiz!" /></th>
        </tr>
      </table>
      <input type="hidden" name="resultCount" id="resultCount" value="" /><input type="hidden" name="questionCount" id="questionCount" value="" /><input type="hidden" name="optionCounts" id="optionCounts" value="" />
    </fieldset>
  </form>
</div>

<?php
mysql_free_result($listCat);
?>
