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
	public $likes = NULL;
	public $dislikes = NULL;
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
				$this->likes			= $row_getQuiz['likes'];
				$this->dislikes			= $row_getQuiz['dislikes'];
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
	
	// publish the quiz
	function publish(){
		require('../Connections/quizroo.php');
		require('variables.php');
		mysql_select_db($database_quizroo, $quizroo);
		
		// set the publish flag to 1 and award the first creation score
		$query = sprintf("UPDATE q_quizzes SET isPublished = 1, quiz_score = %d WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
		mysql_query($query, $quizroo) or die(mysql_error());
		
		// check the current member stats (for level up calculation later)
		$queryCheck = sprintf("SELECT `level`, quiztaker_score FROM `s_members` WHERE `member_id` = %d", $this->fk_member_id);
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$old_level = $row_getResults['level'];
		$old_score = $row_getResults['quiztaker_score'];
		mysql_free_result($getResults);
		
		// update the member's creation score
		$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d WHERE member_id = %d", $GAME_BASE_POINT, $GAME_BASE_POINT, $this->fk_member_id);
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
	}
	
	// get the rating value by a member
	function getRating($member_id){
		// find out the rating of this quiz
		require('../Connections/quizroo.php');
		mysql_select_db($database_quizroo, $quizroo);
		$query = sprintf("SELECT rating FROM q_store_rating WHERE fk_member_id = %d AND fk_quiz_id = %d", $member_id, $this->quiz_id);
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		$totalRows_getQuery = mysql_num_rows($getQuery);
		
		if($totalRows_getQuery != 0){
			return $row_getQuery['rating'];
		}else{
			return 0;
		}
	}
	
	// award points based on like(1), dislike(-1) or neutral(0)
	function awardPoints($type, $member_id){
		require('variables.php');
		if($this->isPublished()){ // check if quiz is published
			require('../Connections/quizroo.php');

			mysql_select_db($database_quizroo, $quizroo);
			
			switch($type){
				case -1: // penalty deduction for 'dislike' rating
				
				// check if taker has already disliked
				if($this->getRating($member_id) != -1 && $GAME_ALLOW_DISLIKE){ // also check if dislikes are allowed by the system
					// deduct the quiz score
					if($this->quiz_score > 0){ // check if quiz score is more than 0
						// deduct the quiz score and increment the dislike count
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score - %d, dislike = dislike + 1 WHERE quiz_id = %d", $GAME_BASE_POINT * 2, $this->quiz_id);
						mysql_query($query, $quizroo) or die(mysql_error());
		
						// update the creator's points
						$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score - %d, quizcreator_score_today = quizcreator_score_today - %d WHERE member_id = %d", $GAME_BASE_POINT * 2, $GAME_BASE_POINT, $this->fk_member_id);
						mysql_query($query, $quizroo) or die(mysql_error());
					}
					
					// log the id of the awarder
					if($this->getRating($member_id) != 0){ // check if member has rated before
						// do an update if member has rated this quiz before
						$query = sprintf("UPDATE q_store_rating SET rating = %d WHERE fk_quiz_id = %d AND fk_member_id = %d", -1, $this->quiz_id, $member_id);
						mysql_query($query, $quizroo) or die(mysql_error());				
					}else{
						// do an insert if member is rating this quiz for the first time
						$query = sprintf("INSERT INTO q_store_rating(fk_member_id, fk_quiz_id, rating) VALUES(%d, %d, %d)", $member_id, $this->quiz_id, -1);
						mysql_query($query, $quizroo) or die(mysql_error());					
					}				
				}
				
				break;
							
				case 0:	// award the base points or bonus award for 'like' rating		
				case 1: // bonus award for 'like' rating
				default:// default case for no type specified
				
				// check if taker has already liked
				if($this->getRating($member_id) != 1){
					// update the quiz score
					if($type == 1){ // also increment the like count
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score + %d, like = like + 1 WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
					}else{ // just update the score
						$query = sprintf("UPDATE q_quizzes SET quiz_score = quiz_score + %d WHERE quiz_id = %d", $GAME_BASE_POINT, $this->quiz_id);
					}
					mysql_query($query, $quizroo) or die(mysql_error());
					
					// update the creator's points
					$query = sprintf("UPDATE s_members SET quizcreator_score = quizcreator_score + %d, quizcreator_score_today = quizcreator_score_today + %d WHERE member_id = %d", $GAME_BASE_POINT, $GAME_BASE_POINT, $this->fk_member_id);
					mysql_query($query, $quizroo) or die(mysql_error());
					
					if($type == 1){			
						// log the id of the awarder
						if($this->getRating($member_id) != 0){ // check if member has rated before
							// do an update if member has rated this quiz before
							$query = sprintf("UPDATE q_store_rating SET rating = %d WHERE fk_quiz_id = %d AND fk_member_id = %d", 1, $this->quiz_id, $member_id);
							mysql_query($query, $quizroo) or die(mysql_error());				
						}else{
							// do an insert if member is rating this quiz for the first time
							$query = sprintf("INSERT INTO q_store_rating(fk_member_id, fk_quiz_id, rating) VALUES(%d, %d, %d)", $member_id, $this->quiz_id, 1);
							mysql_query($query, $quizroo) or die(mysql_error());					
						}
					}
				}
				break;	
			}
		}
	}
	
	// return the text name of the creator
	function creator(){
		require('../Connections/quizroo.php');
		mysql_select_db($database_quizroo, $quizroo);
		$query = sprintf("SELECT member_name FROM s_members WHERE member_id = %d", GetSQLValueString($this->fk_member_id, "int"));
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
	
	// check if a user has taken quiz
	function hasTaken($facebookID){
		require('../Connections/quizroo.php');	// database connections
		
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
}
}