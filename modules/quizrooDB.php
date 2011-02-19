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
?>