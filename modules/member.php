<?php
//----------------------------------------
// Member Class
//----------------------------------------
if(!class_exists("Member")){
class Member{
	// member facebook variables
	public $session = NULL;
	public $id = NULL;
	public $me = NULL;
	public $facebook = NULL;
	
	// member data
	public $level = NULL;
	public $rank = NULL;
	public $quiztaker_score = NULL;
	public $quizcreator_score = NULL;
	public $quiztaker_score_today = NULL;
	public $quizcreator_score_today = NULL;	
	
	// member type
	private $isAdmin = NULL;
	
	//----------------------------------------
	// Class constructer which
	// - Populates the facebook $me object
	// - Retrieves the facebook ID
	//----------------------------------------
	function __construct(){
		require('variables.php');
		if($_SERVER['SERVER_NAME'] == "localhost"){ // if on localhost, automatically toggle facebook debug
			$this->me = true;
			$this->me = array('name' => "Debug Superuser");
			$this->id = 999999999;
			$this->register();
		}else{
			// Load the Facebook PHP API
			require('facebook.php');
			
			// create the facebook object
			$this->facebook = new Facebook(array(
				'appId'  => $FB_APPID,
				'secret' => $FB_SECRET,
				'cookie' => true, // enable optional cookie support
			));
	
			// get the session
			$this->session = $this->facebook->getSession();
			
			// Session based API call.
			if($this->session){
				try{
					$this->id = $this->facebook->getUser();		// get the user's facebook ID
					$this->me = $this->facebook->api('/me');	// populate the facebook $me object
					
					// register the user into our database if required
					$this->register();
					
				}catch(FacebookApiException $e){
					error_log($e);
					echo $e;
				}
			}else{
				// user should login to facebook first or ask for basic authentication if needed
				$this->authenticate();
			}
		}
		
		if($this->me){
			return true;
		}else{
			return false;
		}		
	}
	
	//----------------------------------------
	// Register the user
	//----------------------------------------
	function register(){
		require('quizrooDB.php');	// database connections
		// check if the member is already in the database
		$queryCheck = sprintf("SELECT * FROM s_members WHERE member_id = %s", $this->id);
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalRows_getCheck = mysql_num_rows($getCheck);
		
		if($totalRows_getCheck == 0){ // user is not in the database, add user into the database
			$queryInsert = sprintf("INSERT INTO s_members(member_id, member_name, join_date) VALUES(%s, '%s', NOW())", $this->id, $this->getName());
			mysql_query($queryInsert, $quizroo) or die(mysql_error());
			
			// populate the user data
			$this->level = 0;
			$this->rank = 0;
			$this->quiztaker_score = 0;
			$this->quizcreator_score = 0;
			$this->quiztaker_score_today = 0;
			$this->quizcreator_score_today = 0;
			$this->isAdmin = false;
		}else{
			// populate the user data
			$this->level = $row_getCheck['level'];
			$this->rank = $row_getCheck['rank'];
			$this->quiztaker_score = $row_getCheck['quiztaker_score'];
			$this->quizcreator_score = $row_getCheck['quizcreator_score'];
			$this->quiztaker_score_today = $row_getCheck['quiztaker_score_today'];
			$this->quizcreator_score_today = $row_getCheck['quizcreator_score_today'];
			$this->isAdmin = $row_getCheck['isAdmin'];
		}	
	}
	
	//----------------------------------------
	// Generate the authentication query
	//----------------------------------------
	function authenticate($permissions = NULL){
		// set the default permissons of not specified
		$permissions = "publish_stream";
		
		// build the URL for O Auth 2.0 authentication
		$url = $this->facebook->getLoginUrl(array(
		  'canvas'     => 1
		, 'fbconnect'  => 0
		, 'display'    => 'page'
		, 'cancel_url' => null
		, 'req_perms'  => $permissions
		));
		
		// redirect the user
		echo '<script type="text/javascript">top.location.href = "'.$url.'";</script>';
	}
	
	//----------------------------------------
	// Terminate the user
	//----------------------------------------
	function terminate(){
		// TODO: remove user from database	
	}
	
	//----------------------------------------
	// Update the member's combined score
	//----------------------------------------
	function getTotalScore(){
		return $this->quizcreator_score + $this->quiztaker_score;
	}
	
	//----------------------------------------
	// check and update the member's creator score
	// - use with caution, will reset global scores if member quizzes are removed!
	//----------------------------------------
	function updateCreatorScore(){
		require('quizrooDB.php');	// database connections
		
		// count all the points and update the member's creator score
		$queryCheck = sprintf("SELECT COUNT(quiz_score) AS score FROM q_quizzes WHERE fk_member_id = %d", $this->id);
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalScore = $row_getCheck['score'];
		mysql_free_result($getCheck);
		$query = sprintf("UPDATE s_members SET quizcreator_score = %d WHERE member_id = %d)", $totalScore, $this->id);
		mysql_query($query, $quizroo) or die(mysql_error());
	}
	
	//----------------------------------------
	// or calculation of points to award to member for taking quiz
	// - return the level
	//----------------------------------------
	function calculatePoints($quiz_id, $quiz_publish_status, $achievement_array){
		include("quizrooDB.php");
		include("variables.php");
		
		// check if user has already taken this quiz
		$timesTaken = $this->timesTaken($quiz_id);
		
		// we follow through only if retakes are allowed and the quiz is published
		if(($timesTaken == 1 || $GAME_REWARD_RETAKES) && $quiz_publish_status){
			// The following factors should be fulfilled before points are awarded
			// - first time taking this question OR always reward flag on
			// - quiz is published
	
			// get the multiplier value
			$queryCheck = sprintf("SELECT COUNT(store_id) AS count FROM `q_store_result` WHERE `fk_member_id` = %s AND DATE(`timestamp`) = DATE(NOW())", $this->id);
			$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
			$row_getResults = mysql_fetch_assoc($getResults);
			$todayMultiplier = $row_getResults['count'];
			mysql_free_result($getResults);
			
			// calculate the points by multiplier
			$points = $GAME_BASE_POINT + ($todayMultiplier - 1) * ($GAME_MULTIPLIER);
			
			// check the current member stats (for level up calculation later)
			$old_level = $this->level;
			$old_score = $this->quiztaker_score;
			
			// check if the there is a levelup:
			///////////////////////////////////////
			
			// check the level table 
			$queryCheck = sprintf("SELECT id FROM `g_levels` WHERE points <= (SELECT `quiztaker_score`+`quizcreator_score` FROM s_members WHERE member_id = %d)+%s ORDER BY points DESC LIMIT 0, 1", $this->id, $points);
			$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
			$row_getResults = mysql_fetch_assoc($getResults);
			$new_level = $row_getResults['id'];
			mysql_free_result($getResults);
			
			if($new_level > $old_level){
				// a levelup has occurred
				$achievement_array[] = $new_level;	// provide the ID of the level acheievement
				
				// update the member table to reflect the new level
				$queryUpdate = sprintf("UPDATE s_members SET quiztaker_score = quiztaker_score + %s, quiztaker_score_today = quiztaker_score_today + %s, level = %d WHERE member_id = %s", $points, $points, $new_level, $this->id);
			}else{
				// just update the member table to reflect the points
				$queryUpdate = sprintf("UPDATE s_members SET quiztaker_score = quiztaker_score + %s, quiztaker_score_today = quiztaker_score_today + %s WHERE member_id = %s", $points, $points, $this->id);
			}
			// execute the update statement
			mysql_query($queryUpdate, $quizroo) or die(mysql_error());	
		}
		// return the array
		return $achievement_array;
	}
	
	// check if user has taken quiz
	function timesTaken($quiz_id){
		include("quizrooDB.php");
		// check how many time user has taken this quiz
		$queryCheck = sprintf("SELECT COUNT(store_id) AS count FROM q_store_result WHERE `fk_member_id` = %s AND `fk_quiz_id` = %s", $this->id, GetSQLValueString($quiz_id, "int"));
		$getResults = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getResults = mysql_fetch_assoc($getResults);
		$timesTaken = $row_getResults['count'];	
		mysql_free_result($getResults);
		
		return $timesTaken;
	}
	
	//----------------------------------------
	// Data providers
	//----------------------------------------	
	function getToken(){
		// returns the OAuth access token
		return $this->session['access_token'];
	}
	
	function getName(){
		return $this->me['name'];
	}
	
	function getGender(){
		return $this->me['gender'];
	}
	
	// bind a unikey with this member
	function bindImagekey($unikey){
		require('quizrooDB.php');	// database connections
		
		$queryCheck = sprintf("INSERT INTO s_image_store(`uni_key`, `fk_member_id`) VALUES(%s, %d)",  GetSQLValueString($unikey, "text"), $this->id);
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
	}
	
	// get their ranking
	function getRanking(){
		require('quizrooDB.php');	// database connections
		
		// find out the rank
		if($this->isAdmin){
			$queryRanking = sprintf("SELECT ranking FROM (
SELECT @rownum:=@rownum+1 ranking, member_id, quiztaker_score+quizcreator_score AS score FROM s_members, (SELECT @rownum:=0) numbering ORDER BY score DESC) ranks
WHERE member_id = %d", $this->id);		
		}else{
			$queryRanking = sprintf("SELECT ranking FROM (
SELECT @rownum:=@rownum+1 ranking, member_id, quiztaker_score+quizcreator_score AS score FROM s_members, (SELECT @rownum:=0) numbering WHERE member_id NOT IN (SELECT member_id FROM s_members WHERE isAdmin = 1) ORDER BY score DESC) ranks
WHERE member_id = %d", $this->id);		
		}
		$getRanking = mysql_query($queryRanking, $quizroo) or die(mysql_error());
		$row_getRanking = mysql_fetch_assoc($getRanking);
		$ranking = $row_getRanking['ranking'];	
		mysql_free_result($getRanking);
		
		return $ranking;
	}
	
	// get leaderboard stats
	function getLeaderBoardStat($rank){
		require('quizrooDB.php');	// database connections
		
		if($this->isAdmin){
			$query_getRanking = sprintf("SELECT * FROM (SELECT @rownum:=@rownum+1 ranking, member_id, member_name, level, g_achievements.name as rank_name, quiztaker_score+quizcreator_score AS score, quiztaker_score, quizcreator_score FROM s_members, g_achievements, (SELECT @rownum:=0) numbering WHERE s_members.rank = g_achievements.id ORDER BY score DESC) ranks WHERE ranking = %d", $rank);
		}else{
			$query_getRanking = sprintf("SELECT * FROM (SELECT @rownum:=@rownum+1 ranking, member_id, member_name, level, g_achievements.name as rank_name, quiztaker_score+quizcreator_score AS score, quiztaker_score, quizcreator_score FROM s_members, g_achievements, (SELECT @rownum:=0) numbering WHERE s_members.rank = g_achievements.id AND member_id NOT IN (SELECT member_id FROM s_members WHERE isAdmin = 1) ORDER BY score DESC) ranks WHERE ranking = %d", $rank);
		}
		$getRanking = mysql_query($query_getRanking, $quizroo) or die(mysql_error());
		$row_getRanking = mysql_fetch_assoc($getRanking);
		$totalRows_getRanking = mysql_num_rows($getRanking);
		mysql_free_result($getRanking);
		
		// only return the array of ranking exists
		if($totalRows_getRanking != 0){
			return $row_getRanking;
		}else{
			return NULL;
		}
	}
	
	// get stats
	function getStats($type){
		require('quizrooDB.php');	// database connections
		
		switch($type){
			// Total Created Quizzes
			case "quizzes_total":
			$queryStat = sprintf("SELECT COUNT(quiz_id) AS count FROM q_quizzes WHERE fk_member_id = %d", $this->id);
			break;
			
			// Number of drafts
			case "quizzes_draft":
			$queryStat = sprintf("SELECT COUNT(quiz_id) AS count FROM q_quizzes WHERE fk_member_id = %d AND isPublished = 0", $this->id);
			break;

			// Published quizzes
			case "quizzes_published":
			$queryStat = sprintf("SELECT COUNT(quiz_id) AS count FROM q_quizzes WHERE fk_member_id = %d AND isPublished = 1", $this->id);
			break;
			
			// Quizzes under modification
			case "quizzes_modify":
			$queryStat = sprintf("SELECT COUNT(quiz_id) AS count FROM q_quizzes WHERE fk_member_id = %d AND isPublished = 2", $this->id);
			break;
			
			// Number of archived quizzes
			case "quizzes_archived":
			$queryStat = sprintf("SELECT COUNT(quiz_id) AS count FROM q_quizzes WHERE fk_member_id = %d AND isPublished = 3", $this->id);
			break;
			
			// Total number of questions for quizzes created
			case "questions":
			$queryStat = sprintf("SELECT COUNT(question_id) AS count FROM q_questions WHERE fk_quiz_id IN (SELECT quiz_id FROM q_quizzes WHERE fk_member_id = %d)", $this->id);
			break;
			
			// Total number of options for quizzes created
			case "options":
			$queryStat = sprintf("SELECT COUNT(option_id) AS count FROM q_options WHERE fk_question_id IN (SELECT question_id FROM q_questions WHERE fk_quiz_id IN (SELECT quiz_id FROM q_quizzes WHERE fk_member_id = %d))", $this->id);
			break;
			
			// Total likes from quizzes
			case "likes":
			$queryStat = sprintf("SELECT SUM(likes) AS count FROM q_quizzes WHERE fk_member_id = %d", $this->id);
			break;
			
			// Quiz taker points
			case "taker_points":
			$queryStat = sprintf("SELECT quiztaker_score AS count FROM s_members WHERE fk_member_id = %d", $this->id);
			break;
			
			// Quiz creator points
			case "creator_points":
			$queryStat = sprintf("SELECT quizcreator_score AS count FROM s_members WHERE fk_member_id = %d", $this->id);
			break;
			
			// Number of quiz taking attempts
			case "taken_quizzes_total":
			$queryStat = sprintf("SELECT COUNT(store_id) AS count FROM q_store_result WHERE fk_member_id = %d", $this->id);
			break;
			
			// Number of unique quiz taking attempts
			case "taken_quizzes_unique":
			$queryStat = sprintf("SELECT COUNT(*) AS count FROM (SELECT store_id FROM q_store_result WHERE fk_member_id = %d GROUP BY fk_quiz_id) t", $this->id);
			break;
		}
		$getStat = mysql_query($queryStat, $quizroo) or die(mysql_error());
		$row_getStat = mysql_fetch_assoc($getStat);
		$resultCount = $row_getStat['count'];
		mysql_free_result($getStat);
		
		return $resultCount;
	}
}
}

?>