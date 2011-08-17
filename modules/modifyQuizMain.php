<!--This is the main UI for modifying quizzes, defining all the steps required for modifying and retrieve the existing quiz content-->

<?php
require('../modules/quizrooDB.php'); // require database connection
require('../modules/uploadFunctions.php'); 
require("../modules/quiz.php"); //require database operations

// now check whether this quiz actually belongs to this user
if(isset($_GET['id'])){
	$quiz = new Quiz($_GET['id']);
	//check if the quiz exist and if the current member is owner
	if($quiz->exists() && $quiz->isOwner($member->id)){
		$quiz_state = true;
		// unpublish the quiz before modifying
		$quiz->unpublish($member->id);
		$unikey = $quiz->quiz_key;
	}else{
		$quiz_state = false;
	}
}else{
	$quiz_state = false;
}
if($quiz_state){

if(isset($_GET['step'])){
	$step = $_GET['step'];
}else{
	$step = 1;
}
switch($step){

/**********************************************
 * THE FIRST STEP (Returning): Quiz Information
 **********************************************/
default: case 1:
	// populate the categories
	$query_listCat = "SELECT cat_id, cat_name FROM q_quiz_cat";
	$listCat = mysql_query($query_listCat, $quizroo) or die(mysql_error());
	$row_listCat = mysql_fetch_assoc($listCat);
	$totalRows_listCat = mysql_num_rows($listCat);
?>
<form action="../modules/modifyQuizEngine.php?step=1" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<div id="progress-container" class="framePanel rounded">
  <h2>Modify Quiz: Quiz Information</h2>
  <div class="content-container">
  <p>Part 1<em></em> contains all the basic information we need to help you setup your quiz. It allows you to tell a potential quiz taker what insights your quiz intends to deliver.</p>
  <p>If you have prepared several images for quiz, you can upload them all at once! Images that you have uploaded previously with this quiz can also be used. You can choose which images to use at every part of the  process.</p>
  <ul class="rounded">
    <li class="current"><strong>Part 1</strong> Quiz Information</li>
    <li><strong>Part 2</strong> Results</li>
    <li><strong>Part 3</strong> Question</li>
    <li><strong>Part 4</strong> Update</li>
  </ul>
  </div>
</div>
<div id="create-quiz" class="frame rounded">
    <input type="hidden" name="id" value="<?php echo $quiz->quiz_id; ?>" />
    <input type="hidden" name="unikey" value="<?php echo $unikey; ?>" />
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
    <td><span class="formDesc">OR click on a picture below to use it as the quiz picture</span></td>
  </tr>
  <tr>
    <td><?php // return uploaded images
	$count = 0;
foreach(glob("../quiz_images/".$unikey."*") as $filename){ ?>
<a href="javascript:;" onClick="selectImage(0, '<?php echo basename($filename); ?>')"><img src="../quiz_images/imgcrop.php?w=80&h=60&f=<?php echo basename($filename); ?>" width="80" height="60" id="d<?php echo $count; ?>" class="selectImage"></a>
<?php $count++; } ?></td>
  </tr>
</table><?php } ?></div></td>
        </tr>
        <tr>
          <th valign="top" scope="row">&nbsp;</th>
          <td align="right" class="desc"><input type="submit" name="next" id="next" value="Next Part" /></td>
        </tr>
    </table>
</div>
</form>
<?php 
/**********************************
 *THE SECOND STEP: Quiz Results
 **********************************/
break; case 2:
?>
<div id="progress-container" class="framePanel rounded">
  <h2>Modify Quiz: Results</h2>
  <div class="content-container">
  <p>Part 2 allows you to define the results of your quiz. Quiz results appear at the end of each quiz. Depending on what options the quiz taker has chosen, the result which carries the most weightage from the options will be the final quiz result. You can add as many results as you like! </p>
  <ul class="rounded">
    <li class="completed_last start"><strong>Part 1</strong> Quiz Information</li>
    <li class="current"><strong>Part 2</strong> Results</li>
    <li><strong>Part 3</strong> Question</li>
    <li><strong>Part 4</strong> Update</li>
  </ul>
  </div>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/modifyQuizEngine.php?step=2" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="id" value="<?php echo $quiz->quiz_id; ?>" />
<div id="createResultContainer"></div>
    <div class="add_container">
      <input type="submit" name="save" id="prev" value="Previous Part" />&nbsp;
      <input type="button" name="addResultBtn" id="addResultBtn" value="Add new result" onclick="QuizResult.add()" />&nbsp;
      <input type="submit" name="save" id="next" value="Next Part" />
    </div>
    <input type="hidden" name="resultCount" id="resultCount" value="0" />
  </form>
</div>
<?php // THE THIRD STEP: Quiz Questions
break; case 3:
?>
<div id="progress-container" class="framePanel rounded">
  <h2>Modify Quiz: Questions</h2>
  <div class="content-container">
  <p>Part 3 allows you to populate your quiz with questions. You can provide several options for quiz takers to choose for each question. You should also specify the weightage of each option - how each option contributes to a result. </p>
  <ul class="rounded">
    <li class="complete_full start"><strong>Part 1</strong> Quiz Information</li>
    <li class="completed_last"><strong>Part 2</strong> Results</li>
    <li class="current"><strong>Part 3</strong> Question</li>
    <li><strong>Part 4</strong> Update</li>
  </ul>
  </div>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/modifyQuizEngine.php?step=3" method="post" enctype="multipart/form-data" name="createQuiz" id="createQuiz" onsubmit="return submitCheck(Spry.Widget.Form.validate(this));">
<input type="hidden" name="id" value="<?php echo $quiz->quiz_id; ?>" />
<input type="hidden" name="optionCounts" id="optionCounts" value="" />
<input type="hidden" name="questionCount" id="questionCount" value="" />
<div id="createQuestionContainer"></div>
    <div class="add_container">
      <input type="submit" name="save" id="prev" value="Previous Part" />&nbsp;
      <input type="button" name="addQuestionBtn" id="addQuestionBtn" value="Add new question" onclick="QuizQuestion.add()" />&nbsp;
      <input type="submit" name="save" id="next" value="Next Part" />
    </div>
  </form>
</div>
<?php 
/**************************************
 * THE FOURTH STEP: Confirm and publish
 **************************************/
break; case 4:
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
?>
<div id="progress-container" class="framePanel rounded">
  <h2>Modify Quiz: Save Changes</h2>
  <div class="content-container">
  <p>You have complete all the parts of the quiz. The  table below shows the review of your quiz.</p>
  <ul class="rounded final">
    <li class="complete_full start"><strong>Part 1</strong> Quiz Information</li>
    <li class="complete_full"><strong>Part 2</strong> Results</li>
    <li class="completed_last"><strong>Part 3</strong> Question</li>
    <li class="final"><strong>Part 4</strong> Update</li>
  </ul>
  </div>
</div>
<div id="create-quiz" class="frame rounded">
  <form action="../modules/modifyQuizEngine.php?step=4" method="post" name="createQuiz" id="createQuiz">
<input type="hidden" name="id" value="<?php echo $quiz->quiz_id; ?>" />
<table border="0" align="center" cellpadding="5" cellspacing="0" id="checkQuizTable">
      <tr>
        <th scope="col">&nbsp;</th>
        <th scope="col">Count</th>
        <th scope="col">Remarks</th>
      </tr>
      <tr>
      <!-- check if the number of results is sufficient-->
        <th>Results</th>
        <td align="center"><?php echo $numResults; ?></td>
        <td><?php if($numResults < $VAR_QUIZ_MIN_RESULT){ ?>You need at least <?php echo $VAR_QUIZ_MIN_RESULT; ?> results<?php }else{ ?>Ok!<?php } ?></td>
      </tr>
      <tr>
      <!-- check if the number of questions is sufficient-->
        <th>Question</th>
        <td align="center"><?php echo $numQuestions; ?></td>
        <td><?php if($numQuestions < $VAR_QUIZ_MIN_QUESTIONS){ ?>You need at least <?php echo $VAR_QUIZ_MIN_QUESTIONS; ?> question(s)<?php }else{ ?>Ok!<?php } ?></td>
      </tr>
      <tr>
      <!-- check if the number of options is sufficient-->
        <th>Options</th>
        <td align="center">Avg. ~<?php echo sprintf("%.2f", $averageOptionCount); ?></td>
        <td><?php if(!$questionState){ ?>You do not have any questions<?php }else{ if(!$optionState){ ?>One of your questions has less than <?php echo $VAR_QUIZ_MIN_OPTIONS; ?> options!<?php }else{ ?>Ok!<?php }} ?></td>
      </tr>
    </table>
    <p><?php if($quizState){ ?>
    Congratuations! Your quiz has passed the basic requirements. You can save your changes and re-publish your quiz now.
    <?php }else{ ?>
    Opps! It seems that your quiz doesn't fulfill certain requirements. All quizzes require a minimum of <?php echo $VAR_QUIZ_MIN_RESULT; ?> result(s) and <?php echo $VAR_QUIZ_MIN_QUESTIONS; ?> questions(s). Each question also required at least <?php echo $VAR_QUIZ_MIN_OPTIONS; ?> options.
    <?php } ?></p>
    <table width="95%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <th scope="row"><input type="submit" name="save" id="prev" value="Previous Part" />&nbsp;
        <?php if(!$quizState){ ?>
        <input type="submit" name="save" id="publish" value="Save Changes" class="btnDisabled" disabled="disabled" /><?php }else{ ?>
        <input type="submit" name="save" id="publish" value="Save Changes" /><?php } ?></th>
      </tr>
    </table>
    <input type="hidden" name="resultCount" id="resultCount" value="" />
    <input type="hidden" name="questionCount" id="questionCount" value="" />
    <input type="hidden" name="optionCounts" id="optionCounts" value="" />
  </form>
</div>
<?php break; } ?>
<!-- If quiz does not exist-->
<?php }else{ ?>
<div id="takequiz-preamble" class="framePanel rounded">
  <h2>Opps, quiz not found!</h2>
  <div class="content-container"> <span class="logo"><img src="../webroot/img/quizroo-question.png" alt="Member not found" width="248" height="236" /></span>
    <p>Sorry! The quiz that you are looking for may not be available. Please check the ID of the quiz again.</p>
    <p>The reason you are seeing this error could be due to:</p>
    <ul>
      <li>The URL is incorrect or does not contain the ID of the quiz</li>
      <li>No quiz with this ID exists</li>
      <li>The owner could have removed the quiz</li>
      <li>The quiz was taken down due to the violation of rules at Quizroo</li>
    </ul>
  </div>
</div>
<?php } ?>
