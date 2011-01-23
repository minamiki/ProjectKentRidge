<?php
//----------------------------------------
// Quiz Class
//----------------------------------------
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
	public $isPublished = NULL;
	
	function __construct($quiz_id){
		if($quiz_id != NULL){
			require('../Connections/quizroo.php');
			// populate class with quiz data			
			mysql_select_db($database_quizroo, $quizroo);
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
				$this->isPublished		= $row_getQuiz['isPublished'];				
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}	
	}
	
	// return the number of question in this quiz
	function numQuestions(){
		require('../Connections/quizroo.php');
		mysql_select_db($database_quizroo, $quizroo);
		$query = sprintf("SELECT question_id FROM q_questions WHERE fk_quiz_id = %d", GetSQLValueString($this->quiz_id, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		return mysql_num_rows($getQuery);
	}
	
	// return the publish status of the quiz
	function isPublished(){
		return $this->isPublished;
	}
	
	// return the text name of the creator
	function creator(){
		require('../Connections/quizroo.php');
		mysql_select_db($database_quizroo, $quizroo);
		$query = sprintf("SELECT member_name FROM members WHERE member_id = %d", GetSQLValueString($this->fk_member_id, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		return $row_getQuery['member_name'];
	}
	
	// return the quiz topic
	function category(){
		require('../Connections/quizroo.php');
		mysql_select_db($database_quizroo, $quizroo);
		$query = sprintf("SELECT cat_name FROM q_quiz_cat WHERE cat_id = %d", GetSQLValueString($this->fk_quiz_cat, "int"));
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		return $row_getQuery['cat_name'];
	}
	
	// check if user is owner
	function isOwner($facebookID){
		if($facebookID == $this->fk_member_id){
			return true;
		}else{
			return false;
		}		
	}
}
}