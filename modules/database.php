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
$this->servername = "hci-apps.ddns.comp.nus.edu.sg";
$this->username = "root";
$this->password = "gjtgmcjw";
$this->database = "quizroo";
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

function get($table,$attributes,$where){
	
	$this->Connect();	
	
	$this->SQLStatement = "SELECT ".implode(',',$attributes)." FROM ".$table."  WHERE ".$where;
	$results = mysql_query($this->SQLStatement);
	
	$return = array();
	
	while($row = mysql_fetch_array($results)){
		array_push($return,$row);
	}
	
	$this->Disconnect();
	
	return $return;
}

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
	
	return $this->SQLStatement;
}

function update($table,$keyattribute,$key,$attributes,$values){
	$key = "'".$key."'";
	
	$this->Connect();	
	if(count($attributes)==count($values)){
		for($i=0;$i<count($attributes);$i++){
		$this->SQLStatement = "UPDATE ".$table." SET ".$attributes[$i]." = ".$values[$i]." WHERE ".$keyattribute." = ".$key;
		$results = mysql_query($this->SQLStatement);
		}
	}
	$this->Disconnect();
	
	return $this->SQLStatement;
}

function mysqlnow(){
	$this->Connect();
	$this->SQLStatement = "SELECT NOW()";
		$results = mysql_query($this->SQLStatement);
	$this->Disconnect();
	
	return $results;
}
}
?>