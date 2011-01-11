<?php
//----------------------------------------
// Member Class
//----------------------------------------

class Member{
	// debugging mode
	private $debug = true;
	
	// member variables
	public $facebook;
	public $id;
	public $me = NULL;
	
	//----------------------------------------
	// Class constructer which
	// - Populates the facebook $me object
	// - Retrieves the facebook ID
	//----------------------------------------
	function __construct(){
		if(!$debug){
			require('../Connections/quizroo.php');	// database connections
			require('facebook.php');				// request for facebook ID
			require('variables.php');				// global variables
			
			// create the facebook object
			$this->facebook = new Facebook(array(
				'appId'  => $FB_APPID,
				'secret' => $FB_SECRET,
				'cookie' => true, // enable optional cookie support
			));
	
			// get the session
			$session = $facebook->getSession();
			
			// Session based API call.
			if($session){
				try{
					$this->id = $facebook->getUser();
					$this->me = $facebook->api('/me');	// populate the facebook $me object
				}catch(FacebookApiException $e){
					error_log($e);
				}
			}
		}else{
			$this->$me['name'] = "Debug Test User";
			$this->id = 1;
		}
		
		if($me){
			return true;
		}else{
			return false;
		}		
	}
	
	//----------------------------------------
	// Register the user
	//----------------------------------------
	function register(){
		// check if the member is already in the database
		$queryCheck = sprintf("SELECT member_id FROM members WHERE member_id = %s", GetSQLValueString($facebook_id, "int"));
		$getCheck = mysql_query($queryCheck, $quizroo) or die(mysql_error());
		$row_getCheck = mysql_fetch_assoc($getCheck);
		$totalRows_getCheck = mysql_num_rows($getCheck);
		
		if($totalRows_getCheck != 0){ // user is not in the database, add user into the database
			$queryInsert = sprintf("INSERT INTO members(member_id) VALUES(%s)", GetSQLValueString($facebook_id, "int"));
			mysql_query($queryInsert, $quizroo) or die(mysql_error());
		}
	}
	
	//----------------------------------------
	// Terminate the user
	//----------------------------------------
	function terminate(){
		// remove user from database	
	}
}
?>