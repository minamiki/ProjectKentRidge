<?php require('../Connections/quizroo.php'); ?>
<?php // additional requires
require('uploadFunctions.php');
require('quiz.php');

if(isset($_GET['step'])){
?>
<?php // THE SECOND STEP: Quiz Results
switch($_GET['step']){ case 2:
	// get the unikey and quiz id
	if(isset($_GET['key']) && isset($_GET['id'])){
		$unikey = $_GET['key'];
		$quiz_id = $_GET['id'];
		// now check whether this quiz actually belongs to this user
		$quiz = new Quiz($quiz_id);
		if(!$quiz->isOwner($member->id)){
			die("Authentication Failure");
		}
	}else{
		// find a way to post and error
		die("session has expired");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz</h3>
  <p>You're just <strong>3</strong> steps away from creating your own quiz! <em>Step 2</em> allows you to define the results of your quiz. Quiz takers will arrive at different results depending on how the questions are answered. </p>
  <ul class="rounded">
    <li><strong>Step 1</strong> Quiz Information</li>
    <li class="current"><strong>Step 2</strong> Results</li>
    <li><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=2" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="unikey" value="<?php echo $unikey; ?>" />
<input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
<h4>Quiz Results</h4>
    <p>Quiz results appear at the end of each quiz. Depending on what options the quiz taker has chosen, the result which carries the most weightage from the options will be the final quiz result. You can add as many results as you like! </p>
    <div id="createResultContainer"></div>
    <div class="add_container">
      <input type="button" name="addResultBtn" id="addResultBtn" value="Add new result" onclick="QuizResult.add()" />&nbsp;<input type="submit" name="button" id="button" value="Next Step!" />
    </div>
    <input type="hidden" name="resultCount" id="resultCount" value="0" />
  </form>
</div>
<?php // THE THIRD STEP: Quiz Questions
break; case 3:
	// get the unikey and quiz id
	if(isset($_GET['key']) && $_GET['id']){
		$unikey = $_GET['key'];
		$quiz_id = $_GET['id'];
		// now check whether this quiz actually belongs to this user
		$quiz = new Quiz($quiz_id);
		if(!$quiz->isOwner($member->id)){
			die("Authentication Failure");
		}
	}else{
		// find a way to post and error
		die("session has expired");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz</h3>
  <p>You're just <strong>2</strong> steps away from creating your own quiz! <em>Step 3</em> requires you to define the questions for your quiz. </p>
  <ul class="rounded">
    <li><strong>Step 1</strong> Quiz Information</li>
    <li><strong>Step 2</strong> Results</li>
    <li class="current"><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=3" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="unikey" value="<?php echo $unikey; ?>" />
<input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
<input type="hidden" name="optionCounts" id="optionCounts" value="" />
<input type="hidden" name="questionCount" id="questionCount" value="" />
<h4>Quiz Questions</h4>
    <p>The following section allows you to populate your quiz with question. You can provide several options for quiz takers to choose for each question. You should also specify the weightage of each option - how each option contributes to a result.</p>
    <div id="createQuestionContainer"> </div>
    <div class="add_container">
      <input type="button" name="addQuestionBtn" id="addQuestionBtn" value="Add new question" onclick="QuizQuestion.add()" />&nbsp;
      <input type="submit" name="button" id="button" value="Next Step!" />
    </div>
  </form>
</div>
<?php // THE FOURTH STEP: Confirm and publish
break; case 4:
	// get the unikey and quiz id
	if(isset($_GET['key']) && $_GET['id']){
		$unikey = $_GET['key'];
		$quiz_id = $_GET['id'];
		// now check whether this quiz actually belongs to this user
		$quiz = new Quiz($quiz_id);
		if(!$quiz->isOwner($member->id)){
			die("Authentication Failure");
		}
	}else{
		// find a way to post and error
		die("session has expired");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz</h3>
  <p>You're just <strong>1</strong> step away from creating your own quiz!</p>
  <ul class="rounded">
    <li><strong>Step 1</strong> Quiz Information</li>
    <li><strong>Step 2</strong> Results</li>
    <li><strong>Step 3</strong> Question</li>
    <li class="current"><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form>
<h4>Create Quiz</h4>
    <p>You are just one step away from creating your quiz! Click the Create Quiz button below, and if everything goes well, your quiz will be created!</p>
    <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <th scope="row"><input type="submit" name="submitBtn" id="submitBtn" value="Create Quiz!" /></th>
      </tr>
    </table>
    <input type="hidden" name="resultCount" id="resultCount" value="" />
    <input type="hidden" name="questionCount" id="questionCount" value="" />
    <input type="hidden" name="optionCounts" id="optionCounts" value="" />
  </form>
</div>
<?php // THE FIRST STEP
break;} }else{ 
// generate a one time hash key for the upload, (this hash key will stay with the quiz throughout the entire creation process)
$unikey = get_rand_id(8);

// populate the categories
mysql_select_db($database_quizroo, $quizroo);
$query_listCat = "SELECT cat_id, cat_name FROM q_quiz_cat";
$listCat = mysql_query($query_listCat, $quizroo) or die(mysql_error());
$row_listCat = mysql_fetch_assoc($listCat);
$totalRows_listCat = mysql_num_rows($listCat);

// create a session for quiz creation in progress
$_SESSION['unikey'] = $unikey;
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz</h3>
  <p>You're just <strong>4</strong> steps away from creating your own quiz! <em>Step 1</em> contains all the basic information we need to help you setup your quiz. If you have prepared several images for quiz, you can upload them all at once! You can choose which images to use at every step of the creation process.</p>
  <ul class="rounded">
    <li class="current"><strong>Step 1</strong> Quiz Information</li>
    <li><strong>Step 2</strong> Results</li>
    <li><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=1" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
    <input type="hidden" name="unikey" value="<?php echo $unikey; ?>" />
    <h4>Quiz Information</h4>
      <p>The Quiz Information allows you to tell a potential quiz taker what insights your quiz intends to deliver.</p>
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_title">Title</label></th>
          <td><span id="sprytextfield0" class="sprytextfield">
            <input type="text" name="quiz_title" id="quiz_title" />
          <span class="textfieldRequiredMsg">A title is required.</span></span> <span class="desc">Give your Quiz a meaningful title! Your title will be the first thing that catches a reader's attention.</span></td>
        </tr>
        <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_description">Description</label></th>
          <td><span id="sprytextarea0" class="sprytextarea">
            <textarea name="quiz_description" id="quiz_description" cols="45" rows="5"></textarea>
            <span class="textareaRequiredMsg">Description should not be blank!</span></span><span class="desc">Provide a short description on what your quiz is about.</span></td>
        </tr>
        <tr>
          <th valign="middle" scope="row"><label for="quiz_cat">Topic</label></th>
          <td><select name="quiz_cat" id="quiz_cat">
              <?php do { ?>
              <option value="<?php echo $row_listCat['cat_id']?>"><?php echo $row_listCat['cat_name']?></option>
              <?php } while ($row_listCat = mysql_fetch_assoc($listCat));
			  $rows = mysql_num_rows($listCat);
			  if($rows > 0) {
				  mysql_data_seek($listCat, 0);
				  $row_listCat = mysql_fetch_assoc($listCat);
			  } ?>
            </select></td>
        </tr>
        <tr>
          <th valign="top" scope="row"><label>Quiz Picture</label>
          <input type="hidden" name="result_picture_0" id="result_picture_0" val="" /></th>
          <td class="desc"><div id="swfupload-control-0" class="swfupload-control">
              <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                  <td><input name="uploader-0" type="button" id="uploader-0" /></td>
                  <td valign="middle" class="formDesc">Upload a picture (jpg, gif only)</td>
                </tr>
              </table>
              <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                  <td><div id="selected-image-0" class="selected-image"></div></td>
                  <td><p id="queuestatus-0"></p></td>
                </tr>
              </table>
              <ol id="log-0" class="log">
              </ol>
            </div>
            <div id="pictureChoser_0"></div></td>
        </tr>
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
          <td align="right" class="desc"><input type="submit" name="next" id="next" value="Next Step!" /></td>
        </tr>
      </table>
  </form>
</div>
<?php mysql_free_result($listCat); } ?>
