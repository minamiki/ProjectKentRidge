<?php
//----------------------------------------
// Member Class
//----------------------------------------
if(!class_exists("Member")){
class Member{
	// debugging mode
	private $debug = false;
	
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
	
	//----------------------------------------
	// Class constructer which
	// - Populates the facebook $me object
	// - Retrieves the facebook ID
	//----------------------------------------
	function __construct(){
		if(!$this->debug){	
			require('facebook.php');				// request for facebook ID
			require('variables.php');				// global variables
			
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
		}else{
			$this->me = true;
			$this->me = array('name' => "Debug Test User");
			$this->id = 1;
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
		require('../Connections/quizroo.php');	// database connections
		// check if the member is already in the database
		mysql_select_db($database_quizroo, $quizroo);
		$queryCheck = sprintf("SELECT * FROM s_members WHERE member_id = %s", $this->id);
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalRows_getCheck = mysql_num_rows($getCheck);
		
		if($totalRows_getCheck == 0){ // user is not in the database, add user into the database
			$queryInsert = sprintf("INSERT INTO s_members(member_id, member_name) VALUES(%s, '%s')", $this->id, $this->getName());
			mysql_query($queryInsert, $quizroo) or die(mysql_error());
			
			// populate the user data
			$this->level = 0;
			$this->rank = 0;
			$this->quiztaker_score = 0;
			$this->quizcreator_score = 0;
			$this->quiztaker_score_today = 0;
			$this->quizcreator_score_today = 0;	
		}else{
			// populate the user data
			$this->level = $row_getCheck['level'];
			$this->rank = $row_getCheck['rank'];
			$this->quiztaker_score = $row_getCheck['quiztaker_score'];
			$this->quizcreator_score = $row_getCheck['quizcreator_score'];
			$this->quiztaker_score_today = $row_getCheck['quiztaker_score_today'];
			$this->quizcreator_score_today = $row_getCheck['quizcreator_score_today'];	
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
	//----------------------------------------
	function updateCreatorScore(){
		require('../Connections/quizroo.php');	// database connections
		
		// count all the points and update the member's creator score
		mysql_select_db($database_quizroo, $quizroo);
		$queryCheck = sprintf("SELECT COUNT(quiz_score) AS score FROM q_quizzes WHERE fk_member_id = %d", $this->id);
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalScore = $row_getCheck['score'];
		mysql_free_result($getCheck);
		$query = sprintf("UPDATE s_members SET quizcreator_score = %d WHERE member_id = %d)", $totalScore, $this->id);
		mysql_query($query, $quizroo) or die(mysql_error());
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
}
}

?>