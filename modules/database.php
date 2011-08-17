<!-- Functions to help make manipulating/making use/getting data from database easier -->
<?php
class Database
{	
var $SQLStatement = "";
var $Error = "";
var $connection;
var $servername = "";
var $username = "";
var $password = "";
var $database = "";

function Database(){
	include('quizrooDB.php');	
	$this->servername = $hostname_quizroo;
	$this->username = $username_quizroo;
	$this->password = $password_quizroo;
	$this->database = $database_quizroo;
}

function Connect(){
	$this->connection = mysql_connect($this->servername, $this->username, $this->password) or
	die('Could not connect: ' . mysql_error());
	mysql_select_db($this->database, $this->connection);
}

function Disconnect(){
	mysql_close($this->connection) or 
 	die('Could not connect: '.mysql_error());;
}

/*
 * Gets values from the specified table with attributes in array format (e.g. array("apple","pear","orange"))
 * given the "WHERE" clause.
 */
function get($table,$attributes,$where){
	
	$this->Connect();	
	
	$this->SQLStatement = "SELECT ".implode(',',$attributes)." FROM ".$table."  WHERE ".$where;
	
	$results = mysql_query($this->SQLStatement);
	
	$return = array();
	
	if(!is_bool($results)){
		while($row = mysql_fetch_array($results)){
			array_push($return,$row);
		}
	}
	
	$this->Disconnect();
	
	return $return;
}

/*
 * Gets values from the specified table with attributes in array format (e.g. array("apple","pear","orange"))
 * given the "WHERE" clause. Limits results to number specified
 */
function limit($table,$attributes,$where,$limit){
	
	$this->Connect();	
	
	$this->SQLStatement = "SELECT ".implode(',',$attributes)." FROM ".$table."  WHERE ".$where." LIMIT ".$limit;
	
	$results = mysql_query($this->SQLStatement);
	
	$return = array();
	
	if(!is_bool($results)){
		while($row = mysql_fetch_array($results)){
			array_push($return,$row);
		}
	}
	
	$this->Disconnect();
	
	return $return;
}

/*
 * Executes the query specified.
 */
function query($query){
	$this->Connect();	
	
	$this->SQLStatement = $query;
	$results = mysql_query($this->SQLStatement);
	
	$return = array();
	
	while($row = mysql_fetch_array($results)){
		array_push($return,$row);
	}
	
	$this->Disconnect();
	
	return $return;
}

/*
 * Saves a set of values for the given attributes where the attributes and values are given in the same order in CSV format
 * (e.g. array("name","company","position"), array("John","Microsoft","Director")).
 */
function save($table,$attributes,$values){
	for($i=0;$i<count($values);$i++){
		$values[$i] = "'".$values[$i]."'";
	}
	
	$this->Connect();	
	if(count($attributes)==count($values)){
		$this->SQLStatement = "INSERT INTO ".$table." (".implode(',',$attributes).") VALUES (".implode(',',$values).")";
		$results = mysql_query($this->SQLStatement);
	}
	$this->Disconnect();
	
	return $results;
}

function update($table,$keyattribute,$key,$attributes,$values){
	$key = "'".$key."'";
	
	$this->Connect();	
	if(count($attributes)==count($values)){
		for($i=0;$i<count($attributes);$i++){
		$this->SQLStatement = "UPDATE ".$table." SET ".$attributes[$i]."=".$values[$i]." WHERE ".$keyattribute."=".$key;
		$results = mysql_query($this->SQLStatement);
		}
	}
	$this->Disconnect();
	
	return $results;
}

/*
 * Deletes row/s that match the WHERE condition from table 
 */
function delete($table,$where){
	$this->Connect();	
	
	$this->SQLStatement = "DELETE FROM ".$table."  WHERE ".$where;
	$results = mysql_query($this->SQLStatement);
		
	$this->Disconnect();
	
	return $results;
}

/*
 * The NOW() function returns the current system date and time.
 */
function mysqlnow(){
	$this->Connect();
	$this->SQLStatement = "SELECT NOW() as now";
	$results = mysql_query($this->SQLStatement);
	$row = mysql_fetch_array($results);
	$this->Disconnect();
	
	return $row['now'];
}
}
?>