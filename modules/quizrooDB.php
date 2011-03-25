<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_quizroo = "localhost";
$database_quizroo = "quizroo";
if($_SERVER['SERVER_NAME'] == "localhost"){ // if on localhost, automatically toggle facebook debug
	$username_quizroo = "root";
	$password_quizroo = "";
}else{
	$username_quizroo = "quizthecat";
	$password_quizroo = "kuizz4roo";
}
$quizroo = mysql_pconnect($hostname_quizroo, $username_quizroo, $password_quizroo) or trigger_error(mysql_error(),E_USER_ERROR);
// use the quizroo database
mysql_select_db($database_quizroo, $quizroo);

// MySQL SQL-injection prevention
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if (!function_exists("getCURL")) {
function getCURL($url){
	$options = array(
	CURLOPT_RETURNTRANSFER => true, // return web page
	CURLOPT_HEADER => false, // don't return headers
	CURLOPT_ENCODING => "", // handle all encodings
	CURLOPT_USERAGENT => "spider", // who am i
	CURLOPT_AUTOREFERER => true, // set referer on redirect
	CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
	CURLOPT_TIMEOUT => 120, // timeout on response
	CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
	);
	
	$ch = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	$err = curl_errno( $ch );
	$errmsg = curl_error( $ch );
	$header = curl_getinfo( $ch );
	curl_close( $ch );
	
	$header['errno'] = $err;
	$header['errmsg'] = $errmsg;
	$header['content'] = $content;
	return $header;
}
}
?>