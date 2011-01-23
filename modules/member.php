<?php
//----------------------------------------
// Member Class
//----------------------------------------
if(!class_exists("Member")){
class Member{
	// debugging mode
	private $debug = false;
	
	// member variables
	public $session = NULL;
	public $id = NULL;
	public $me = NULL;
	public $facebook = NULL;
	
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
		$queryCheck = sprintf("SELECT member_id FROM members WHERE member_id = %s", GetSQLValueString($this->id, "int"));
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalRows_getCheck = mysql_num_rows($getCheck);
		
		if($totalRows_getCheck == 0){ // user is not in the database, add user into the database
			$queryInsert = sprintf("INSERT INTO members(member_id, member_name) VALUES(%s, %s)", GetSQLValueString($this->id, "int"), GetSQLValueString($this->getName(), "text"));
			mysql_query($queryInsert, $quizroo) or die(mysql_error());
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
		// remove user from database	
	}
	
	//----------------------------------------
	// Date providers
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