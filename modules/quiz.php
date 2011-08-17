<?php
/**************************************************************************
Quiz Class: this class contains all the quiz operations on the database 
 -Function exist: return false when quizzes do not exist or are not published 
 -Function create: insert into table the newly created quiz then return back the quiz id for other purpose
 -Funtion update: update information of the quiz such as description, catergory, picture, etc.
 -Function delete($memberID): check whether the current user is member-> delete questions, results and images
 -Function addResult: create a new result
 -Function updateResult: create a new result
 -Function removeResult: remove a new result
 -Function addQuestion: create a new question
 -Function update a question
 -Function remove a question
 -Function addQuestion: create a new option
 -Function update an option
 -Function remove an option
 -Function numQuestions: return the number of question in this quiz
 -Function isPublished: return the publish status of the quiz
 -Function checkPublish: check if quiz is ready to be published
 -Function: publish the quiz
 -Function: re-publish the quiz
 -Function: archive the quiz
 -Function: get the rating value by a member
 -Function getResults: get the list of results belonging to this quiz
 -Function getQuestions: get the list of questions belonging to this quiz
 -Function getOptions: get the list of options belonging to a question
 -Function getAttempts: get the list of attempts for this quiz
 -Function awardPoints: award points based on like(1), dislike(-1) or neutral(0)
 -Function creator: return the text name of the creator
 -Function category: return the quiz topic
 -Function isOwner: check if user is owner
 -Function hasTaken: check if a user has taken quiz
 -Function bindImagekey: bind a unikey with this quiz
****************************************************************/
if(!class_exists("Quiz")){
class Quiz{
	// Quiz data
	public $quiz_id = NULL;
	public $quiz_name = NULL;
	public $quiz_description = NULL;
	public $fk_quiz_cat = NULL;
	public $quiz_picture = NULL;
	public $creation_date = NULL;
	public $fk_member_id = NULL;
	public $quiz_score = NULL;
	public $likes = NULL;
	public $dislikes = NULL;
	public $isPublished = NULL;
	public $quiz_key = NULL;
	
	public $quiz_not_available = NULL;
	
	/*********************
	 * construction
	 *********************/
	function __construct($quiz_id = NULL){
		if($quiz_id != NULL && $quiz_id != ""){
			require('quizrooDB.php');
			// populate class with quiz data			
			$queryQuiz = sprintf("SELECT * FROM q_quizzes WHERE quiz_id = %d", GetSQLValueString($quiz_id, "int"));
			$getQuiz = mysql_query($queryQuiz, $quizroo) or die(mysql_error());
			$row_getQuiz = mysql_fetch_assoc($getQuiz);
			$totalRows_getQuiz = mysql_num_rows($getQuiz);
			
			if($totalRows_getQuiz != 0){		
				$this->quiz_id 			= $quiz_id;
				$this->quiz_name 		= $row_getQuiz['quiz_name'];
				$this->quiz_description = $row_getQuiz['quiz_description'];
				$this->fk_quiz_cat		= $row_getQuiz['fk_quiz_cat'];
				$this->quiz_picture		= $row_getQuiz['quiz_picture'];
				$this->creation_date	= $row_getQuiz['creation_date'];
				$this->fk_member_id		= $row_getQuiz['fk_member_id'];
				$this->quiz_score		= $row_getQuiz['quiz_score'];
				$this->likes			= $row_getQuiz['likes'];
				$this->dislikes			= $row_getQuiz['dislikes'];
				$this->isPublished		= $row_getQuiz['isPublished'];
				$this->quiz_key			= $row_getQuiz['quiz_key'];
				return true;
			}else{
				$this->quiz_not_available = true;
				return false;
			}
		}else{
			$this->quiz_not_available = true;
			return false;
		}	
	}
	
	/*********************
	 * Function exist: return false when quizzes do not exist or are not published 
	 *********************/
	function exists(){
		if($this->quiz_not_available){
			return false;
		}else{
			if($this->isPublished == 3){
				return false;
			}else{
				return true;
			}
		}
	}
	
	/*********************************************************************************************************
	 *Function create: insert into table the newly created quiz then return back the quiz id for other purpose
	 ***********************************************************************************************************/
	function create($title, $description, $cat, $picture, $member_id, $key){
		require('quizrooDB.php');
		
		// insert into the quiz table (protect each insert from HTML Injection)
		$insertSQL = sprintf("INSERT INTO q_quizzes(`quiz_name`, `quiz_description`, `fk_quiz_cat`, `quiz_picture`, `fk_member_id`, `quiz_key`) VALUES (%s, %s, %d, %s, %d, %s)",
						   htmlentities(GetSQLValueString($title, "text")),
						   htmlentities(GetSQLValueString($description, "text")),
						   GetSQLValueString($cat, "int"),
						   GetSQLValueString($picture, "text"),
						   GetSQLValueString($member_id, "int"),
						   GetSQLValueString($key, "text"));
		mysql_query($insertSQL, $quizroo) or die(mysql_error());
		
		// find the quiz id to return the quiz_id for the method
		$querySQL = "SELECT LAST_INSERT_ID() AS insertID, creation_date FROM q_quizzes WHERE quiz_id = LAST_INSERT_ID()";
		$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
		$row_resultID = mysql_fetch_assoc($resultID);
		
		$this->quiz_id = $row_resultID['insertID'];
		$this->quiz_name = htmlentities($title);
		$this->quiz_description = htmlentities($description);
		$this->fk_quiz_cat = $cat;
		$this->quiz_picture = $picture;
		$this->creation_date = $row_resultID['creation_date'];
		$this->fk_member_id = $member_id;
		$this->quiz_key = $key;
		
		mysql_free_result($resultID);
		
		// update the keystore
		$this->bindImagekey($key);
		
		return $this->quiz_id;
	}
	/********************************************************************************************
	 * Funtion update: update information of the quiz such as description, catrgory, picture, etc.
	 *******************************************************************************************/
	function update($title, $description, $cat, $picture, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert into the quiz table
			$insertSQL = sprintf("UPDATE q_quizzes SET `quiz_name`=%s, `quiz_description`=%s, `fk_quiz_cat`=%d, `quiz_picture`=%s WHERE `quiz_id` = %d",
							   htmlentities(GetSQLValueString($title, "text")),
							   htmlentities(GetSQLValueString($description, "text")),
							   GetSQLValueString($cat, "int"),
							   GetSQLValueString($picture, "text"),
							   GetSQLValueString($this->quiz_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			return $this->quiz_id;
		}else{
			return false;
		}
	}
	
	/********************************************************************************************
	 * Function delete($memberID): check whether the current user is member-> delete questions, results and images
	 *******************************************************************************************/
	function delete($memberID){
		require('quizrooDB.php'); // database connection
		
		// check if is member
		if($this->isOwner($memberID)){
			// remove the questions
			$questionList = explode(',', $this->getQuestions());
			foreach($questionList as $question){
				if($question != ""){
					$this->removeQuestion($question, $memberID);
				}
			}
			
			// remove the results
			$resultList = explode(',', $this->getResults());
			foreach($resultList as $result){
				if($result != ""){
					$this->removeResult($result, $memberID);
				}
			}
			
			// clean images
			if($this->quiz_key != ""){
				foreach(glob("../quiz_images/".$this->quiz_key."*") as $filename){
					unlink($filename);
				}			
			}			
			
			// remove the question
			$insertSQL = sprintf("DELETE FROM q_quizzes WHERE `quiz_id` = %d", GetSQLValueString($this->quiz_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			$removed_id = $this->quiz_id;
			
			$this->quiz_id 			= NULL;
			$this->quiz_name 		= NULL;
			$this->quiz_description = NULL;
			$this->fk_quiz_cat		= NULL;
			$this->quiz_picture		= NULL;
			$this->creation_date	= NULL;
			$this->fk_member_id		= NULL;
			$this->quiz_score		= NULL;
			$this->likes			= NULL;
			$this->dislikes			= NULL;
			$this->isPublished		= NULL;
			$this->quiz_key			= NULL;
			
			return $removed_id;	
		}else{
			return false;
		}
	}
	
	/********************************************************************************************
	 *  Function addResult: create a new result (for old database)
	 *******************************************************************************************/
	function addResult($result_title, $result_description, $result_picture, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// Insert the result
			$insertSQL = sprintf("INSERT INTO q_results(`result_title`, `result_description`, `result_picture`, `fk_quiz_id`) VALUES (%s, %s, %s, %d)",
							   htmlentities(GetSQLValueString($result_title, "text")),
							   htmlentities(GetSQLValueString($result_description, "text")),
							   GetSQLValueString($result_picture, "text"),
							   GetSQLValueString($this->quiz_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			// find the result id
			$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
			$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
			$row_resultID = mysql_fetch_assoc($resultID);
			mysql_free_result($resultID);
			
			return $row_resultID['insertID'];
		}else{
			return false;
		}
	}
	
	/********************************************************************************************
	 *  Function updateResult: update new result
	 *******************************************************************************************/
	function updateResult($result_title, $result_description, $result_picture, $result_id, $memberID){
		require('quizrooDB.php'); // database connection
		
		// check if is member
		if($this->isOwner($memberID)){
			// Insert the result
			$insertSQL = sprintf("UPDATE q_results SET `result_title` = %s, `result_description` = %s, `result_picture` = %s WHERE `result_id` = %d",
							   htmlentities(GetSQLValueString($result_title, "text")),
							   htmlentities(GetSQLValueString($result_description, "text")),
							   GetSQLValueString($result_picture, "text"),
							   GetSQLValueString($result_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			return $result_id;
		}else{
			return false;
		}
	}
	
	/********************************************************************************************
	 *  Function removeResult: remove a new result
	 *******************************************************************************************/
	function removeResult($result_id, $memberID){
		require('quizrooDB.php'); // database connection
		
		// owner check
		if($this->isOwner($memberID)){
			// delete the result and also check if this results actually belongs to this quiz
			$insertSQL = sprintf("DELETE FROM q_results WHERE `result_id` = %d AND `result_id` IN(%s)", GetSQLValueString($result_id, "int"), $this->getResults());
			mysql_query($insertSQL, $quizroo) or die(mysql_error());	
			return true;
		}else{
			return false;
		}
	}
	
	/********************************************************************************************
	 *  Function addQuestion: create a new question
	 *******************************************************************************************/
	 function addQuestion($question, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert the question
			$insertSQL = sprintf("INSERT INTO q_questions(`question`, `fk_quiz_id`) VALUES (%s, %d)",
						   htmlentities(GetSQLValueString($question, "text")),
						   GetSQLValueString($this->quiz_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
		
			// find the question id
			$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
			$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
			$row_resultID = mysql_fetch_assoc($resultID);
			$currentQuestionID = $row_resultID['insertID'];
			mysql_free_result($resultID);
			
			return $row_resultID['insertID'];
		}else{
			return false;
		}
	}
	
	/*****************************
	 *Function update a question
	 ******************************/
	function updateQuestion($question, $question_id, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert the question
			$insertSQL = sprintf("UPDATE q_questions SET `question` = %s WHERE `question_id` = %d",
						  htmlentities(GetSQLValueString($question, "text")),
						   GetSQLValueString($question_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			return $question_id;
		}else{
			return false;
		}
	}
	
	/*****************************
	 * Function remove a question
	 ******************************/
	function removeQuestion($question_id, $memberID){
		require('quizrooDB.php');
		
		// owner check
		if($this->isOwner($memberID)){
			// delete the options and also check if this results actually belongs to this quiz
			$insertSQL = sprintf("DELETE FROM q_options WHERE `fk_question_id` = %d AND `fk_question_id` IN(%s)", GetSQLValueString($question_id, "int"), $this->getQuestions());
			mysql_query($insertSQL, $quizroo) or die(mysql_error());				
			// delete the result and also check if this results actually belongs to this quiz
			$insertSQL = sprintf("DELETE FROM q_questions WHERE `question_id` = %d AND `question_id` IN(%s)", GetSQLValueString($question_id, "int"), $this->getQuestions());
			mysql_query($insertSQL, $quizroo) or die(mysql_error());	
			return true;
		}else{
			return false;
		}
	}
	
	/*****************************************
	 * Function create a option (old database)
	 *****************************************/
	function addOption($option, $result, $weightage, $question, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert the option
			$insertSQL = sprintf("INSERT INTO q_options(`option`, `fk_result`, `option_weightage`, `fk_question_id`) VALUES (%s, %d, %d, %d)",
						   htmlentities(GetSQLValueString($option, "text")),
						   GetSQLValueString($result, "int"),
						   GetSQLValueString($weightage, "int"),
						   GetSQLValueString($question, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
		
			// find the option id
			$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
			$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
			$row_resultID = mysql_fetch_assoc($resultID);
			$currentQuestionID = $row_resultID['insertID'];
			mysql_free_result($resultID);
			
			return $row_resultID['insertID'];
		}else{
			return false;
		}
	}
	
	/*****************************
	 * Function update an option
	 ******************************/
	function updateOption($option, $result, $weightage, $option_id, $memberID){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert the option
			$insertSQL = sprintf("UPDATE q_options SET `option`=%s, `fk_result`=%d, `option_weightage`=%d WHERE option_id=%d",
						   htmlentities(GetSQLValueString($option, "text")),
						   GetSQLValueString($result, "int"),
						   GetSQLValueString($weightage, "int"),
						   GetSQLValueString($option_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			return $option_id;
		}else{
			return false;
		}
	}
	
	/*****************************
	 * Function remove an option
	 ******************************/
	function removeOption($option_id, $memberID){
		require('quizrooDB.php');
		
		// owner check
		if($this->isOwner($memberID)){
			// delete the options and also check if this results actually belongs to this quiz
			$insertSQL = sprintf("DELETE FROM q_options WHERE `option_id` = %d AND `fk_question_id` IN(%s)", GetSQLValueString($option_id, "int"), $this->getQuestions());
			mysql_query($insertSQL, $quizroo) or die(mysql_error());				
			return true;
		}else{
			return false;
		}
	}
	
	/*****************************
	 * Function numQuestions: return the number of question in this quiz
	 ******************************/
	function numQuestions(){
		require('quizrooDB.php');
		$query = sprintf("SELECT question_id FROM q_questions WHERE fk_quiz_id = %d", GetSQLValueString($this->quiz_id, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		return mysql_num_rows($getQuery);
	}
	
	/*****************************
	 *  Function isPublished: return the publish status of the quiz
	 ******************************/
	function isPublished(){
		if($this->isPublished == 1){
			return true;
		}else{
			return false;
		}
	}
	
	/*****************************
	 *  Function checkPublish: check if quiz is ready to be published
	 ******************************/
	function checkPublish(){
		require('variables.php');		
		// check the number of results
		$numResults = $this->getResults("count");
		// check the number of questions
		$numQuestions = $this->getQuestions("count");
		// check the number of options
		$listQuestion = explode(',', $this->getQuestions());
		
		if($numQuestions != 0){
			$questionState = true;
			$optionState = true;
			foreach($listQuestion as $question){
				// check the number of options for this question
				$numOptions = $this->getOptions($question, "count");
				if($numOptions < $VAR_QUIZ_MIN_OPTIONS){
					$optionState = false;
				}
			}
		}else{
			$questionState = false;
			$optionState = false;
		}
		// run through the checks, return false if failed
		if($numResults < $VAR_QUIZ_MIN_RESULT || $numQuestions < $VAR_QUIZ_MIN_QUESTIONS || !$optionState){
			return false;
		}else{
			return true;
		}
	}
	
	/*****************************
	 *  Function: publish the quiz
	 ******************************/
	function publish($memberID){
		require('quizrooDB.php'); // database connection
		require('variables.php');
		
		// check ig quiz belongs to member
		if($this->isOwner($memberID)){
			// check if the quiz is already published
			if(!$this->isPublished()){
				// run through the checks, return false if failed
				if(!$this->checkPublish()){
					return -2;
				}
				
				// check if coming from edits
				if($this->isPublished == 0){				
					// set the publish flag to 1 and award the first creation score
					$query = sprintf("UPDATE q_quizzes SET isPublished = 1, quiz_score = %d WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					$this->isPublished = 1;
					
					// check the current member stats (for level up calculation later)
					$queryCheck = sprintf("SELECT `level`, quiztaker_score FROM `s_members` WHERE `member_id` = %d", $this->fk_member_id);
					$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
					$row_getResults = mysql_fetch_assoc($getResults);
					$old_level = $row_getResults['level'];
					$old_score = $row_getResults['quiztaker_score'];
					mysql_free_result($getResults);
					
					// update the member's creation score
					$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d WHERE member_id = %s", $GAME_BASE_POINT, $GAME_BASE_POINT, $this->fk_member_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					
					// check if the there is a levelup:
					///////////////////////////////////////
					
					// check the level table 
					$queryLevel = sprintf("SELECT id FROM `g_levels` WHERE points <= %d ORDER BY points DESC LIMIT 0, 1", $old_score + $GAME_BASE_POINT);
					$getLevel = mysql_query($queryLevel, $quizroo) or die(mysql_error());
					$row_getLevel = mysql_fetch_assoc($getLevel);
					$new_level = $row_getLevel['id'];
					
					if($new_level > $old_level){ // a levelup has occurred
						// update the member table to reflect the new level
						$queryUpdate = sprintf("UPDATE s_members SET level = %d WHERE member_id = %s", $new_level, $this->fk_member_id);
						mysql_query($queryUpdate, $quizroo) or die(mysql_error());	
						
						// return the ID of the level acheievement
						return $new_level;	
					}else{
						return -1;
					}
				}else{
					// set the publish flag to 1
					$query = sprintf("UPDATE q_quizzes SET isPublished = 1 WHERE quiz_id = %d", $this->quiz_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					$this->isPublished = 1;
					
					return -1;
				}
			}else{
				// already published, do nothing
				return -1;
			}
		}else{
			return false;
		}
	}
	
	/*****************************
	 * Function: re-publish the quiz
	 ******************************/
	function republish($memberID){
		require('quizrooDB.php');
		require('variables.php');
		
		// check if the quiz is already published
		if(!$this->isPublished() && $this->isOwner($memberID)){		
			// set the publish flag to 1
			$query = sprintf("UPDATE q_quizzes SET isPublished = 1 WHERE quiz_id = %d", $this->quiz_id);
			mysql_query($query, $quizroo) or die(mysql_error());
			
			$this->isPublished = 1;
			
			return true;
		}else{
			return false;
		}
	}
	
	/********************
	 * unpublish the quiz
	 *********************/
	function unpublish($memberID){
		require('quizrooDB.php');
		require('variables.php');
		
		// check if the quiz is already published
		if($this->isPublished() && $this->isOwner($memberID)){
			// check if it's a draft or a published quiz
			if($this->isPublished == 1){
				// set the publish flag to 2
				$query = sprintf("UPDATE q_quizzes SET isPublished = 2 WHERE quiz_id = %d", $this->quiz_id);
				$this->isPublished = 2;
			}else{
				// set back to 0
				$query = sprintf("UPDATE q_quizzes SET isPublished = 0 WHERE quiz_id = %d", $this->quiz_id);
				$this->isPublished = 0;
			}
			mysql_query($query, $quizroo) or die(mysql_error());
			
			return true;
		}else{
			return false;
		}
	}
	
	/********************
	 * Function: archive the quiz
	 *********************/
	function archive($memberID){
		require('quizrooDB.php');
		require('variables.php');
		
		// check if the quiz is already published
		if($this->isPublished() && $this->isOwner($memberID)){		
			// set the publish flag to 0
			$query = sprintf("UPDATE q_quizzes SET isPublished = 3 WHERE quiz_id = %d", $this->quiz_id);
			mysql_query($query, $quizroo) or die(mysql_error());
			
			$this->isPublished = 3;
			
			return true;
		}else{
			return false;
		}
	}
	
	/********************
	 * Function: get the rating value by a member
	 *********************/
	function getRating($member_id){
		// find out the rating of this quiz
		require('quizrooDB.php');
		$query = sprintf("SELECT rating FROM q_store_rating WHERE fk_member_id = %s AND fk_quiz_id = %d", $member_id, $this->quiz_id);
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($totalRows_getQuery != 0){
			return $row_getQuery['rating'];
		}else{
			return 0;
		}
	}
	
	/********************
	 * Function getResults: get the list of results belonging to this quiz
	 *********************/
	function getResults($type = NULL){
		require('quizrooDB.php');
		$query = sprintf("SELECT result_id FROM q_results WHERE fk_quiz_id = %d", $this->quiz_id);
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($type == "count"){
			// return the count
			return $totalRows_getQuery;
		}else{
			// return the list
			if($totalRows_getQuery != 0){
				$results = "";
				do{
					$results .= $row_getQuery['result_id'].",";
				}while($row_getQuery = mysql_fetch_assoc($getQuery));
				return substr($results, 0, -1);
			}else{
				return false;
			}
		}
	}
	
	/********************
	 * Function getQuestions: get the list of questions belonging to this quiz
	 *********************/
	function getQuestions($type = NULL){
		require('quizrooDB.php');
		$query = sprintf("SELECT question_id FROM q_questions WHERE fk_quiz_id = %d", $this->quiz_id);
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($type == "count"){
			// return the count
			return $totalRows_getQuery;
		}else{
			// return the list
			if($totalRows_getQuery != 0){
				$results = "";
				do{
					$results .= $row_getQuery['question_id'].",";
				}while($row_getQuery = mysql_fetch_assoc($getQuery));
				return substr($results, 0, -1);
			}else{
				return false;
			}
		}
	}
	
	/********************
	 * Function getOptions: get the list of options belonging to a question
	 *********************/
	function getOptions($question_id, $type = NULL){
		require('quizrooDB.php');
		$query = sprintf("SELECT option_id FROM q_options WHERE fk_question_id = %d", $question_id);
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($type == "count"){
			// return the count
			return $totalRows_getQuery;
		}else{
			if($totalRows_getQuery != 0){
				$results = "";
				do{
					$results .= $row_getQuery['option_id'].",";
				}while($row_getQuery = mysql_fetch_assoc($getQuery));
				return substr($results, 0, -1);
			}else{
				return false;
			}
		}
	}
	
	/********************
	 * Function getAttempts: get the list of attempts for this quiz
	 *********************/
	function getAttempts($unique = false){
		require('quizrooDB.php');
		if($unique){
			$query = sprintf("SELECT COUNT(*) as count FROM (SELECT store_id FROM q_store_result WHERE fk_quiz_id = %s GROUP BY fk_member_id) t", $this->quiz_id);
		}else{
			$query = sprintf("SELECT COUNT(*) as count FROM (SELECT store_id FROM q_store_result WHERE fk_quiz_id = %s) t", $this->quiz_id);
		}
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($totalRows_getQuery == 0){
			return 0;
		}else{
			return $row_getQuery['count'];
		}
	}
	
	/********************
	 * Function awardPoints: award points based on like(1), dislike(-1) or neutral(0)
	 *********************/
	function awardPoints($type, $member_id){
		require('variables.php');
		if($this->isPublished()){ // check if quiz is published
			require('quizrooDB.php');
				
			// store the current level of the quiz creator
			$creator_old_level = $this->creator('level');
			$creator_old_rank = $this->creator('rank');
			
			switch($type){				
				case -2: // penalty deduction for 'dislike' rating
				
				// check if taker has already disliked
				if($this->getRating($member_id) != -1 && $GAME_ALLOW_DISLIKE){ // also check if dislikes are allowed by the system
					// deduct the quiz score
					if($this->quiz_score > 0){ // check if quiz score is more than 0
						// deduct the quiz score and increment the dislike count
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score - %d, dislikes = dislikes + 1 WHERE quiz_id = %d", $GAME_BASE_POINT * 2, $this->quiz_id);
						mysql_query($query, $quizroo) or die(mysql_error());
		
						// update the creator's points
						$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score - %d, quizcreator_score_today = quizcreator_score_today - %d WHERE member_id = %s", $GAME_BASE_POINT * 2, $GAME_BASE_POINT * 2, $this->fk_member_id);
						mysql_query($query, $quizroo) or die(mysql_error());
					}
					
					// log the id of the awarder
					if($this->getRating($member_id) != 0){ // check if member has rated before
						// do an update if member has rated this quiz before
						$query = sprintf("UPDATE q_store_rating SET rating = %d WHERE fk_quiz_id = %d AND fk_member_id = %s", -1, $this->quiz_id, $member_id);
						mysql_query($query, $quizroo) or die(mysql_error());				
					}else{
						// do an insert if member is rating this quiz for the first time
						$query = sprintf("INSERT INTO q_store_rating(fk_member_id, fk_quiz_id, rating) VALUES(%d, %d, %d)", $member_id, $this->quiz_id, -1);
						mysql_query($query, $quizroo) or die(mysql_error());					
					}				
				}
				
				break; // end case -2
				
				case -1: // minus the point given during the 'like'
				// we make sure quiz was already liked
				if($this->getRating($member_id) == 1){
					// deduct the quiz score
					$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score - %d, likes = likes - 1 WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					
					// update the creator's points
					$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score - %d, quizcreator_score_today = quizcreator_score_today - %d WHERE member_id = %s", $GAME_BASE_POINT, $GAME_BASE_POINT, $this->fk_member_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					
					// update member has rating of this quiz
					$query = sprintf("DELETE FROM q_store_rating WHERE fk_quiz_id = %d AND fk_member_id = %s", $this->quiz_id, $member_id);
					mysql_query($query, $quizroo) or die(mysql_error());
				}
				
				break; // end case -1
							
				case 0:	// award the base points or bonus award for 'like' rating		
				case 1: // bonus award for 'like' rating
				default:// default case for no type specified
				
				// check if taker has already liked
				if($this->getRating($member_id) != 1){
					// precheck the level table to see if there's a levelup
					$queryCheck = sprintf("SELECT id FROM `g_levels` WHERE points <= (SELECT `quiztaker_score`+`quizcreator_score` FROM s_members WHERE member_id = %s)+%s ORDER BY points DESC LIMIT 0, 1", $this->fk_member_id, $GAME_BASE_POINT);
					$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
					$row_getCheck = mysql_fetch_assoc($getCheck);
					$creator_new_level = $row_getCheck['id'];
					mysql_free_result($getCheck);
					
					// precheck the rank table to see if there's a leveluo
					$queryCheck = sprintf("SELECT fk_id FROM `g_ranks` WHERE `min` <= %d ORDER BY `min` DESC LIMIT 0, 1", $creator_new_level);
					$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
					$row_getCheck = mysql_fetch_assoc($getCheck);
					$creator_new_rank = $row_getCheck['fk_id'];
					mysql_free_result($getCheck);
					
					if($creator_new_level > $creator_old_level){ // a levelup has occurred, update the achievement log
						$queryUpdate = sprintf("INSERT INTO g_achievements_log(fk_member_id, fk_achievement_id) VALUES(%d, %d)", $this->creator('member_id'), $creator_new_level);
						mysql_query($queryUpdate, $quizroo) or die(mysql_error());
						
						if($creator_new_rank > $creator_old_rank){ // a rankup also occurred, update the achievement log
							$queryUpdate = sprintf("INSERT INTO g_achievements_log(fk_member_id, fk_achievement_id) VALUES(%d, %d)", $this->creator('member_id'), $creator_new_level);
							mysql_query($queryUpdate, $quizroo) or die(mysql_error());
							
							// update the creator's points and increment the level and rank
							$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d, level = %d, rank = %d WHERE member_id = %s", $GAME_BASE_POINT, $GAME_BASE_POINT, $creator_new_level, $creator_new_rank, $this->fk_member_id);
							mysql_query($query, $quizroo) or die(mysql_error());
						}else{
							// update the creator's points and increment the level
							$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d, level = %d WHERE member_id = %s", $GAME_BASE_POINT, $GAME_BASE_POINT, $creator_new_level, $this->fk_member_id);
							mysql_query($query, $quizroo) or die(mysql_error());
						}
					}else{
						// no levelup, just update the creator's points
						$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d WHERE member_id = %s", $GAME_BASE_POINT, $GAME_BASE_POINT, $this->fk_member_id);
						mysql_query($query, $quizroo) or die(mysql_error());
					}
					
					// update the quiz score
					if($type == 1){ // also increment the like count
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score + %d, likes = likes + 1 WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
						mysql_query($query, $quizroo) or die(mysql_error());
					}else{ // just update the score
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score + %d WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
						mysql_query($query, $quizroo) or die(mysql_error());
					}
					
					if($type == 1){			
						// log the id of the awarder
						if($this->getRating($member_id) != 0){ // check if member has rated before
							// do an update if member has rated this quiz before
							$query = sprintf("UPDATE q_store_rating SET rating = %d WHERE fk_quiz_id = %d AND fk_member_id = %s", 1, $this->quiz_id, $member_id);
							mysql_query($query, $quizroo) or die(mysql_error());				
						}else{
							// do an insert if member is rating this quiz for the first time
							$query = sprintf("INSERT INTO q_store_rating(fk_member_id, fk_quiz_id, rating) VALUES(%d, %d, %d)", $member_id, $this->quiz_id, 1);
							mysql_query($query, $quizroo) or die(mysql_error());					
						}
					}
				}
				
				break; // end case 0, 1
			}
		}
	}
	
	/********************
	 * Function creator: return the text name of the creator
	 * - Note: Field is not checked for existance!
	 *********************/
	function creator($field = NULL){
		require('quizrooDB.php');
		$query = sprintf("SELECT * FROM s_members WHERE member_id = %s", GetSQLValueString($this->fk_member_id, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		
		if($field == NULL){
			return $row_getQuery['member_name'];
		}else{
			if(array_key_exists($field, $row_getQuery)){
				return $row_getQuery[$field];
			}else{
				return false;
			}
		}
	}
	
	/********************
	 * Function category: return the quiz topic
	 *********************/
	function category(){
		require('quizrooDB.php');
		$query = sprintf("SELECT cat_name FROM q_quiz_cat WHERE cat_id = %d", GetSQLValueString($this->fk_quiz_cat, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		return $row_getQuery['cat_name'];
	}
	
	/********************
	 * Function isOwner: check if user is owner
	 *********************/
	function isOwner($facebookID){
		if($facebookID == $this->fk_member_id){
			return true;
		}else{
			return false;
		}		
	}
	
	/********************
	 * Function hasTaken: check if a user has taken quiz
	 *********************/
	function hasTaken($facebookID){
		require('quizrooDB.php');	// database connections
		
		// check if user has already taken this quiz
		$queryCheck = sprintf("SELECT COUNT(store_id) AS count FROM q_store_result WHERE `fk_member_id` = %s AND `fk_quiz_id` = %s", $facebookID, $this->quiz_id);
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$timesTaken = $row_getResults['count'];	
		
		if($timesTaken != 0){
			return true;
		}else{
			return false;
		}
	}
	
	/********************
	 * Function bindImagekey: bind a unikey with this quiz
	 *********************/
	function bindImagekey($unikey){
		require('quizrooDB.php');	// database connections
		
		$queryCheck = sprintf("UPDATE s_image_store SET `fk_quiz_id` = %d WHERE `uni_key` = %s AND `fk_member_id`= %s", $this->quiz_id, GetSQLValueString($unikey, "text"), $this->fk_member_id);
		mysql_query($queryCheck, $quizroo) or die(mysql_error());
	}
	
	/********************
	 * Modify on 26 Jul: function for recommending quizzes, update isRecommended value in database
	 *********************/
	 function quizRecommendation (){
		require('quizrooDB.php');	// database connections
		
		$insertSQL = sprintf("UPDATE q_quizzes SET `isRecommended`= 1 WHERE `quiz_id` = %d",
							   GetSQLValueString($this->quiz_id, "int"));
		mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
		return $this->quiz_id;
	}
	
	/**************************************************************************
	 * Modify on 17 Aug: function for adding options according to new database (Test type)
	 * (Foreign key question_id is provided)
	 **************************************************************************/
	 function addTestTypeOption($option, $isCorrect, $fk_question_id){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// insert the option
			$insertSQL = sprintf("INSERT INTO q_options_test(`option`, `isCorrect`, `fk_question_id`) VALUES (%s, %d, %d)",
						   htmlentities(GetSQLValueString($option, "text")),
						   GetSQLValueString($isCorrect, "int"),
						   GetSQLValueString($fk_question_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
		
			// find the option id
			$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
			$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
			$row_resultID = mysql_fetch_assoc($resultID);
			$currentQuestionID = $row_resultID['insertID'];
			mysql_free_result($resultID);
			
			return $row_resultID['insertID'];
		}else{
			return false;
		}
	}
	
    /**************************************************************************
	 * Modify on 17 Aug: function for adding result according to new database (Test type)
	 * (Foreign key quiz_id is provided)
	 **************************************************************************/
	function addTestTypeResult($result_title, $result_description, $result_picture, $range_max, $range_min, $fk_quiz_id){
		require('quizrooDB.php');
		
		// check if is member
		if($this->isOwner($memberID)){
			// Insert the result
			$insertSQL = sprintf("INSERT INTO q_results_test(`result_title`, `result_description`, `result_picture`, `range_max`, `range_min`, `fk_quiz_id`) VALUES (%s, %s, %s, %d, %d, %d)",
							   htmlentities(GetSQLValueString($result_title, "text")),
							   htmlentities(GetSQLValueString($result_description, "text")),
							   GetSQLValueString($result_picture, "text"),
							   GetSQLValueString($range_max, "int"));
							   GetSQLValueString($range_min, "int"));
							   GetSQLValueString($fk_quiz_id, "int"));
			mysql_query($insertSQL, $quizroo) or die(mysql_error());
			
			// find the result id
			$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
			$resultID = mysql_query($querySQL, $quizroo) or die(mysql_error());
			$row_resultID = mysql_fetch_assoc($resultID);
			mysql_free_result($resultID);
			
			return $row_resultID['insertID'];
		}else{
			return false;
		}
	}
	
  }
}