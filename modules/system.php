<?php // Quizroo Fact Class

if(!class_exists("System")){
class System{
	private $date_set = false;
	private $data_set = false;
	
	// stats
	public $member_count = NULL;
	public $member_total_score = NULL;
	public $quiz_total = NULL;
	public $quiz_draft = NULL;
	public $quiz_published = NULL;
	public $quiz_modify = NULL;
	public $quiz_archive = NULL;
	public $quiz_total_score = NULL;
	public $quiz_total_taken = NULL;
	public $quiz_total_taken_unique = NULL;
	public $quiz_total_likes = NULL;
	public $quiz_total_questions = NULL;
	public $quiz_total_options = NULL;
	
	// default contructor
	function __construct(){
		// nothing to do here
	}
	
	// reset daily scores
	function resetDailyScore(){
		require('quizrooDB.php');
		
		$query = "UPDATE `s_members` SET `quiztaker_score_today` = 0, `quizcreator_score_today` = 0";
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$rows_updated = mysql_affected_rows();
		echo sprintf("%d member daily scores resetted.\n", $rows_updated);
	}
	
	// function get member stats
	function getMemberStats($date = NULL){
		require('quizrooDB.php');
		
		if($date == NULL){
			// Get fresh stats
			$query = "SELECT
			(SELECT COUNT(member_id) FROM s_members WHERE isAdmin = 0) AS member_count,
			(SELECT SUM(quiztaker_score) + SUM(quizcreator_score) FROM s_members WHERE isAdmin = 0) AS member_total_score,
			(SELECT COUNT(quiz_id) FROM q_quizzes) AS quiz_total,
			(SELECT COUNT(quiz_id) FROM q_quizzes WHERE isPublished = 0) AS quiz_draft,
			(SELECT COUNT(quiz_id) FROM q_quizzes WHERE isPublished = 1) AS quiz_published,
			(SELECT COUNT(quiz_id) FROM q_quizzes WHERE isPublished = 2) AS quiz_modify,
			(SELECT COUNT(quiz_id) FROM q_quizzes WHERE isPublished = 3) AS quiz_archive,
			(SELECT SUM(quiz_score) FROM q_quizzes WHERE isPublished = 1) AS quiz_total_score,
			(SELECT COUNT(store_id) FROM q_store_result) AS quiz_total_taken,
			(SELECT SUM(likes) FROM q_quizzes WHERE isPublished = 1) AS quiz_total_likes,
			(SELECT COUNT(question_id) FROM q_quizzes, q_questions WHERE isPublished = 1 AND fk_quiz_id = quiz_id) AS quiz_total_questions,
			(SELECT COUNT(option_id) FROM q_options, q_questions, q_quizzes WHERE isPublished = 1 AND fk_quiz_id = quiz_id AND fk_question_id = question_id) AS quiz_total_options";
			$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
			$row_getQuery = mysql_fetch_assoc($getQuery);
			
			$queryCount = "SELECT COUNT(store_id) FROM q_store_result GROUP BY fk_member_id, fk_quiz_id";
			$getCount = mysql_query($queryCount, $quizroo) or die(mysql_error());
			//$row_getCount = mysql_fetch_assoc($getCount);
			$this->quiz_total_taken_unique = mysql_num_rows($getCount);
		}else{
			// get from log
			$query = sprintf("SELECT * FROM s_dailystats WHERE DATE(log_timestamp) = DATE('%s') ORDER BY log_timestamp DESC LIMIT 0, 1", $date);
			$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
			$row_getQuery = mysql_fetch_assoc($getQuery);
			$totalrow_getQuery = mysql_num_rows($getQuery);
			
			if($totalrow_getQuery == 0){
				return false;
			}	
			$this->quiz_total_taken_unique = $row_getQuery['quiz_total_taken_unique'];
		}
		
		// assign the values
		$this->member_count = $row_getQuery['member_count'];
		$this->member_total_score = $row_getQuery['member_total_score'];
		$this->quiz_total = $row_getQuery['quiz_total'];
		$this->quiz_draft = $row_getQuery['quiz_draft'];
		$this->quiz_published = $row_getQuery['quiz_published'];
		$this->quiz_modify = $row_getQuery['quiz_modify'];
		$this->quiz_archive = $row_getQuery['quiz_archive'];
		$this->quiz_total_score = $row_getQuery['quiz_total_score'];
		$this->quiz_total_taken = $row_getQuery['quiz_total_taken'];
		$this->quiz_total_likes = $row_getQuery['quiz_total_likes'];
		$this->quiz_total_questions = $row_getQuery['quiz_total_questions'];
		$this->quiz_total_options = $row_getQuery['quiz_total_options'];
		
		$this->data_set = true;
	}
	
	// store a snapshot of member stats
	function logMemberStats(){
		require('quizrooDB.php');
		
		// get the member stats if not already there
		if(!$data_set){
			$this->getMemberStats();
		}
		
		// log them into daily stats
		$queryInsert = sprintf("INSERT INTO s_dailystats(member_count, member_total_score, quiz_total, quiz_draft, quiz_published, quiz_modify, quiz_archive, quiz_total_score, quiz_total_taken, quiz_total_taken_unique, quiz_total_likes, quiz_total_questions, quiz_total_options)
		VALUES(%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)", 
		$this->member_count,
		$this->member_total_score,
		$this->quiz_total,
		$this->quiz_draft,
		$this->quiz_published,
		$this->quiz_modify,
		$this->quiz_archive,
		$this->quiz_total_score,
		$this->quiz_total_taken,
		$this->quiz_total_taken_unique,
		$this->quiz_total_likes,
		$this->quiz_total_questions,
		$this->quiz_total_options);
		mysql_query($queryInsert, $quizroo) or die(mysql_error());
	}
	
	// get average stats of given parameter
	function getAverageStat($stat){
		// get the member stats if not already there
		if(!$this->data_set){
			$this->getMemberStats();
		}
		
		if($this->quiz_total == 0){
			return 0;
		}else{
			switch($stat){
				case "quiz_score":
				$returnVal = $this->quiz_total_score / $this->quiz_published;
				break;
				case "member_score":
				if($this->member_count != 0){
					$returnVal = $this->member_total_score / $this->member_count;
				}else{
					$returnVal = 0;
				}
				break;
				case "member_create_quiz":
				if($this->member_count != 0){
					$returnVal = $this->quiz_published / $this->member_count;
				}else{
					$returnVal = 0;
				}
				break;
				case "member_take_quiz":
				if($this->member_count != 0){
					$returnVal = $this->quiz_total_taken_unique / $this->member_count;
				}else{
					$returnVal = 0;
				}
				break;
				case "likes":
				$returnVal = $this->quiz_total_likes / $this->quiz_published;
				break;
				case "quizzes":
				if($this->member_count != 0){
					$returnVal = $this->quiz_total / $this->member_count;
				}else{
					$returnVal = 0;
				}				
				break;
				case "quiz_taken":
				if($this->quiz_published != 0){
					$returnVal = $this->quiz_total_taken_unique / $this->quiz_published;
				}else{
					$returnVal = 0;
				}
				break;
				case "questions":
				$returnVal = $this->quiz_total_questions / $this->quiz_total;
				break;
				case "options":
				if($this->quiz_total_questions != 0){
					$returnVal = $this->quiz_total_options / $this->quiz_total_questions;
				}else{
					$returnVal = 0;
				}
				break;
				
				default:
				$returnVal = 0;
			}
		}
		return $returnVal;	
	}
	
	function getTodayStat($stat){
		require('quizrooDB.php');
		
		switch($stat){
			case "unique_takes":
			$query = "SELECT COUNT(*) as count FROM (SELECT store_id FROM `q_store_result` WHERE DATE(timestamp) = DATE(NOW()) GROUP BY fk_member_id, fk_result_id) as counted";
			break;
			case "total_takes":
			$query = "SELECT COUNT(*) as count FROM `q_store_result` WHERE DATE(timestamp) = DATE(NOW())";
			break;
			case "ratings":
			$query = "SELECT COUNT(*) as count FROM `q_store_rating` WHERE DATE(timestamp) = DATE(NOW())";
			break;
			case "achievements":
			$query = "SELECT COUNT(*) as count FROM `g_achievements_log` WHERE DATE(timestamp) = DATE(NOW())";
			break;
			case "members":
			$query = "SELECT COUNT(*) as count FROM `s_members` WHERE DATE(join_date) = DATE(NOW())";
			break;
			case "quizzes_all":
			$query = "SELECT COUNT(*) as count FROM `q_quizzes` WHERE DATE(creation_date) = DATE(NOW())";
			break;
			case "quizzes_published":
			$query = "SELECT COUNT(*) as count FROM `q_quizzes` WHERE DATE(creation_date) = DATE(NOW()) AND isPublished = 1";
			break;
			default:
			return false;	
		}
		$getQuery = mysql_query($query, $quizroo) or die(mysql_error());
		$row_getQuery = mysql_fetch_assoc($getQuery);
		
		return $row_getQuery['count'];
	}
	
	// 
	function cleanImageStore($action = NULL){
		require('quizrooDB.php');
		
		// go through all the images
		$orphan = 0;
		
		// find out orphaned unikeys
		$queryKey = "SELECT uni_key FROM s_image_store WHERE fk_quiz_id = NULL";
		$getImageKey = mysql_query($queryKey, $quizroo) or die(mysql_error());
		$row_getImageKey = mysql_fetch_assoc($getImageKey);
		$totalRows_getImageKey = mysql_num_rows($getImageKey);
		
		do{
			// find the orphaned images
			if($row_getImageKey['uni_key'] != ""){
				foreach(glob("../quiz_images/".$row_getImageKey['uni_key']."*") as $filename){
					if($action == "remove"){
						unlink($filename);
					}
					$orphan++;
				}
			}			
		}while($row_getImageKey = mysql_fetch_assoc($getImageKey));

		if($action == "remove"){
			echo sprintf("Removed %d orphaned files.", $orphan);
		}else{
			echo sprintf("%d orphaned files were found in the image store.", $orphan);
		}
	}
	
	// display output in text format
	function displayStats(){
		// get the member stats if not already there
		if(!$this->data_set){
			$this->getMemberStats();
		}
		
		echo "//////////////////////////////////////////////////////////////////////\n";
		echo "// Quizroo System report for ".date("F j, Y")."\n";
		echo "//////////////////////////////////////////////////////////////////////\n\n";
		
		echo "// Member Information\n";
		echo sprintf("Total %d members, with %d new member(s) today.\n", $this->member_count, $this->getTodayStat('members'));
		echo sprintf("%d member(s) received new achievements today.\n\n", $this->getTodayStat('achievements'));
		
		echo "// Member Anatomy\n";
		echo sprintf("Each member has an average score of %.2f points.\n", $this->getAverageStat('member_score'));
		echo sprintf("Each member has taken an average of %.2f quizzes.\n", $this->getAverageStat('member_take_quiz'));
		echo sprintf("Each member has created an average of %.2f quizzes.\n\n", $this->getAverageStat('member_create_quiz'));
		
		echo "// Quiz Information\n";
		echo sprintf("Total %d quizzes, with %d published, %d drafts/unpublished, %d being modified and %d archived.\n", $this->quiz_total, $this->quiz_published, $this->quiz_draft, $this->quiz_modify, $this->quiz_archive);
		echo sprintf("%d new quizzes created today, with %d published.\n", $this->getTodayStat('quizzes_all'), $this->getTodayStat('quizzes_published'));
		echo sprintf("%d quizzes were taken today, with %d unique attempts.\n", $this->getTodayStat('total_takes'), $this->getTodayStat('unique_takes'));
		echo sprintf("%d quizzes were liked today.\n\n", $this->getTodayStat('ratings'));
		
		echo "// Quiz Anatomy\n";
		echo sprintf("Each quiz has an average of %.2f questions.\n", $this->getAverageStat('questions'));
		echo sprintf("Each question has an average of %.2f questions.\n", $this->getAverageStat('options'));
		echo sprintf("Each quiz has an average of %.2f likes.\n", $this->getAverageStat('likes'));
		echo sprintf("Each quiz has an average of %.2f points.\n", $this->getAverageStat('quiz_score'));
		echo sprintf("Each quiz was taken an average of %.2f times.\n\n", $this->getAverageStat('quiz_taken'));
		
		echo "// System Information\n";
		echo $this->cleanImageStore()."\n";
	}
	
}
}