<?php require('../Connections/quizroo.php'); ?>
<?php // additional requires
require('uploadFunctions.php');
require('quiz.php');

if(isset($_GET['step'])){
?>
<?php // THE FIRST STEP (Returning): Quiz Information
switch($_GET['step']){ case 1:

// get the unikey and quiz id
if(isset($_GET['id'])){
	$quiz_id = $_GET['id'];
	$quiz = new Quiz($quiz_id);
	
	// now check whether this quiz actually belongs to this user
	if($quiz->isOwner($member->id)){
		$unikey = $quiz->quiz_key;
	}else{
		die("Authentication Failure");
	}
	
	// populate the categories
	mysql_select_db($database_quizroo, $quizroo);
	$query_listCat = "SELECT cat_id, cat_name FROM q_quiz_cat";
	$listCat = mysql_query($query_listCat, $quizroo) or die(mysql_error());
	$row_listCat = mysql_fetch_assoc($listCat);
	$totalRows_listCat = mysql_num_rows($listCat);
}else{
	// find a way to post and error
	die("No quiz was specified");
}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz: Quiz Information</h3>
  <p>You're just <strong>4</strong> steps away from creating your own quiz! <em>Step 1</em> contains all the basic information we need to help you setup your quiz. It allows you to tell a potential quiz taker what insights your quiz intends to deliver.</p>
  <p>If you have prepared several images for quiz, you can upload them all at once! You can choose which images to use at every step of the creation process.</p>
  <ul class="rounded">
    <li class="current start"><strong>Step 1</strong> Quiz Information</li>
    <li><strong>Step 2</strong> Results</li>
    <li><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=1" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
    <input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
    <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_title">Title</label></th>
          <td><span id="sprytextfield0" class="sprytextfield">
            <input type="text" name="quiz_title" id="quiz_title" value="<?php echo $quiz->quiz_name; ?>" />
          <span class="textfieldRequiredMsg">A title is required.</span></span> <span class="desc">Give your Quiz a meaningful title! Your title will be the first thing that catches a reader's attention.</span></td>
        </tr>
        <tr>
          <th width="120" valign="top" scope="row"><label for="quiz_description">Description</label></th>
          <td><span id="sprytextarea0" class="sprytextarea">
            <textarea name="quiz_description" id="quiz_description" cols="45" rows="5"><?php echo $quiz->quiz_description; ?></textarea>
            <span class="textareaRequiredMsg">Description should not be blank!</span></span><span class="desc">Provide a short description on what your quiz is about.</span></td>
        </tr>
        <tr>
          <th valign="middle" scope="row"><label for="quiz_cat">Topic</label></th>
          <td><select name="quiz_cat" id="quiz_cat">
              <?php do { ?>
              <option value="<?php echo $row_listCat['cat_id']; ?>" <?php if($row_listCat['cat_id'] == $quiz->fk_quiz_cat){ echo "selected"; }; ?>><?php echo $row_listCat['cat_name']?></option>
              <?php } while ($row_listCat = mysql_fetch_assoc($listCat));
			  $rows = mysql_num_rows($listCat);
			  if($rows > 0) {
				  mysql_data_seek($listCat, 0);
				  $row_listCat = mysql_fetch_assoc($listCat);
			  } ?>
            </select></td>
        </tr>
        <tr>
          <th rowspan="2" valign="top" scope="row"><label>Quiz Picture</label>
          <input type="hidden" name="result_picture_0" id="result_picture_0" value="<?php echo $quiz->quiz_picture; ?>" /></th>
          <td class="desc"><div id="swfupload-control-0" class="swfupload-control">
              <table border="0" cellspacing="0" cellpadding="0">
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
          <td class="desc"><div id="pictureChoser_0"><?php if(sizeof(glob("../quiz_images/".$unikey."*")) > 0){ ?><table border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><span class="formDesc">OR click on a picture below to use it as the result picture</span></td>
  </tr>
  <tr>
    <td><?php // return uploaded images
foreach(glob("../quiz_images/".$unikey."*") as $filename){ ?>
<a href="javascript:;" onClick="selectImage(0, '<?php echo basename($filename); ?>')"><img src="../quiz_images/imgcrop.php?w=80&h=60&f=<?php echo basename($filename); ?>" width="80" height="60" id="r<?php echo $result; ?>i<?php echo $count; ?>" class="selectImage"></a>
<?php $count++; } ?></td>
  </tr>
</table><?php } ?></div></td>
        </tr>
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
          <td align="right" class="desc"><input type="submit" name="next" id="next" value="Next Step!" /></td>
        </tr>
    </table>
  </form>
</div>
<?php // THE SECOND STEP: Quiz Results
break; case 2:
	// get the unikey and quiz id
	if(isset($_GET['id'])){
		$quiz_id = $_GET['id'];
		$quiz = new Quiz($quiz_id);
		
		// now check whether this quiz actually belongs to this user
		if($quiz->isOwner($member->id)){
			$unikey = $quiz->quiz_key;
		}else{
			die("Authentication Failure");
		}
	}else{
		// find a way to post and error
		die("No quiz was specified");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz: Results</h3>
  <p>You're just <strong>3</strong> steps away from creating your own quiz! <em>Step 2</em> allows you to define the results of your quiz. Quiz results appear at the end of each quiz. Depending on what options the quiz taker has chosen, the result which carries the most weightage from the options will be the final quiz result. You can add as many results as you like! </p>
  <ul class="rounded">
    <li class="completed_last start"><strong>Step 1</strong> Quiz Information</li>
    <li class="current"><strong>Step 2</strong> Results</li>
    <li><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=2" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
<div id="createResultContainer"></div>
    <div class="add_container">
      <input type="submit" name="save" id="prev" value="Previous Step" />&nbsp;
      <input type="button" name="addResultBtn" id="addResultBtn" value="Add new result" onclick="QuizResult.add()" />&nbsp;
      <input type="submit" name="save" id="next" value="Next Step!" />
    </div>
    <input type="hidden" name="resultCount" id="resultCount" value="0" />
  </form>
</div>
<?php // THE THIRD STEP: Quiz Questions
break; case 3:
	// get the unikey and quiz id
	if(isset($_GET['id'])){
		$quiz_id = $_GET['id'];
		$quiz = new Quiz($quiz_id);
		
		// now check whether this quiz actually belongs to this user
		if($quiz->isOwner($member->id)){
			$unikey = $quiz->quiz_key;
		}else{
			die("Authentication Failure");
		}
	}else{
		// find a way to post and error
		die("No quiz was specified");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz: Questions</h3>
  <p>You're just <strong>2</strong> steps away from creating your own quiz! <em>Step 3</em> allows you to populate your quiz with questions. You can provide several options for quiz takers to choose for each question. You should also specify the weightage of each option - how each option contributes to a result. </p>
  <ul class="rounded">
    <li class="complete_full start"><strong>Step 1</strong> Quiz Information</li>
    <li class="completed_last"><strong>Step 2</strong> Results</li>
    <li class="current"><strong>Step 3</strong> Question</li>
    <li><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=3" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
<input type="hidden" name="optionCounts" id="optionCounts" value="" />
<input type="hidden" name="questionCount" id="questionCount" value="" />
<div id="createQuestionContainer"></div>
    <div class="add_container">
      <input type="submit" name="save" id="prev" value="Previous Step" />&nbsp;
      <input type="button" name="addQuestionBtn" id="addQuestionBtn" value="Add new question" onclick="QuizQuestion.add()" />&nbsp;
      <input type="submit" name="save" id="next" value="Next Step!" />
    </div>
  </form>
</div>
<?php // THE FOURTH STEP: Confirm and publish
break; case 4:
	// get the unikey and quiz id
	if(isset($_GET['id'])){
		$quiz_id = $_GET['id'];
		$quiz = new Quiz($quiz_id);
		
		// now check whether this quiz actually belongs to this user
		if($quiz->isOwner($member->id)){
			$unikey = $quiz->quiz_key;
		}else{
			die("Authentication Failure");
		}
		
		$unikey = $quiz->quiz_key;
		require("../modules/variables.php");
			
		// check the number of results
		$numResults = $quiz->getResults("count");		
		// check the number of questions
		$numQuestions = $quiz->getQuestions("count");
		// check the number of options
		$listQuestion = explode(',', $quiz->getQuestions());
		$totalOptions = 0;
		
		if($numQuestions != 0){
			$questionState = true;
			$optionState = true;
			foreach($listQuestion as $question){
				// check the number of options for this question
				$numOptions = $quiz->getOptions($question, "count");
				if($numOptions < $VAR_QUIZ_MIN_OPTIONS){
					$optionState = false;
				}
				$totalOptions += $numOptions;
			}
		}
		
		if($numQuestions != 0){
			$averageOptionCount = $totalOptions / $numQuestions;
		}else{
			$averageOptionCount = 0;
		}
		
		if(!$quiz->checkPublish()){
			$quizState = false;
		}else{
			$quizState = true;
		}
	}else{
		// find a way to post and error
		die("No quiz was specified");
	}
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz: Publish</h3>
  <p>You're just <strong>1</strong> step away from creating your own quiz! The  table below shows the review of your quiz.</p>
  <ul class="rounded final">
    <li class="complete_full start"><strong>Step 1</strong> Quiz Information</li>
    <li class="complete_full"><strong>Step 2</strong> Results</li>
    <li class="completed_last"><strong>Step 3</strong> Question</li>
    <li class="final"><strong>Step 4</strong> Publish</li>
  </ul>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/createQuizEngine.php?step=4" method="post" name="createQuiz" id="createQuiz">
<input type="hidden" name="id" value="<?php echo $quiz_id; ?>" />
<table border="0" align="center" cellpadding="5" cellspacing="0" id="checkQuizTable">
      <tr>
        <th scope="col">&nbsp;</th>
        <th scope="col">Count</th>
        <th scope="col">Remarks</th>
      </tr>
      <tr>
        <th>Results</th>
        <td align="center"><?php echo $numResults; ?></td>
        <td><?php if($numResults < $VAR_QUIZ_MIN_RESULT){ ?>You need at least <?php echo $VAR_QUIZ_MIN_RESULT; ?> results<?php }else{ ?>Ok!<?php } ?></td>
      </tr>
      <tr>
        <th>Question</th>
        <td align="center"><?php echo $numQuestions; ?></td>
        <td><?php if($numQuestions < $VAR_QUIZ_MIN_QUESTIONS){ ?>You need at least <?php echo $VAR_QUIZ_MIN_QUESTIONS; ?> question(s)<?php }else{ ?>Ok!<?php } ?></td>
      </tr>
      <tr>
        <th>Options</th>
        <td align="center">Avg. ~<?php echo sprintf("%.2f", $averageOptionCount); ?></td>
        <td><?php if(!$questionState){ ?>You do not have any questions<?php }else{ if(!$optionState){ ?>One of your questions has less than <?php echo $VAR_QUIZ_MIN_OPTIONS; ?> options!<?php }else{ ?>Ok!<?php }} ?></td>
      </tr>
    </table>
    <p><?php if($quizState){ ?>
    Congratuations! Your quiz has passed the basic requirements. You can choose to preview your quiz first, or publish your quiz now.
    <?php }else{ ?>
    Opps! It seems that your quiz doesn't fulfill certain requirements. All quizzes require a minimum of <?php echo $VAR_QUIZ_MIN_RESULT; ?> result(s) and <?php echo $VAR_QUIZ_MIN_QUESTIONS; ?> questions(s). Each question also required at least <?php echo $VAR_QUIZ_MIN_OPTIONS; ?> options.
    <?php } ?></p>
    <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <th scope="row"><input type="submit" name="save" id="prev" value="Previous Step" />&nbsp;
        <?php if(!$quizState){ ?><input type="submit" name="save" id="preview" value="Preview" class="btnDisabled" disabled="disabled" />&nbsp;
        <input type="submit" name="save" id="publish" value="Publish Now!" class="btnDisabled" disabled="disabled" /><?php }else{ ?><input type="submit" name="save" id="preview" value="Preview" />&nbsp;
        <input type="submit" name="save" id="publish" value="Publish Now!" /><?php } ?></th>
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
?>
<div id="progress-container" class="frame rounded">
  <h3>Create Quiz</h3>
  <p>You're just <strong>4</strong> steps away from creating your own quiz! <em>Step 1</em> contains all the basic information we need to help you setup your quiz. If you have prepared several images for quiz, you can upload them all at once! You can choose which images to use at every step of the creation process.</p>
  <ul class="rounded">
    <li class="current start"><strong>Step 1</strong> Quiz Information</li>
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
          <th rowspan="2" valign="top" scope="row"><label>Quiz Picture</label>
          <input type="hidden" name="result_picture_0" id="result_picture_0" value="" /></th>
          <td class="desc"><div id="swfupload-control-0" class="swfupload-control">
              <table border="0" cellspacing="0" cellpadding="0">
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
          <td class="desc"><div id="pictureChoser_0"></div></td>
        </tr>
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
          <td align="right" class="desc"><input type="submit" name="next" id="next" value="Next Step!" /></td>
        </tr>
      </table>
  </form>
</div>
<?php mysql_free_result($listCat); } ?>
