<?php require_once('Connections/kuizzroo.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "register")) {
if (!isset($_SESSION)) {
  session_start();
}
  $insertSQL = sprintf("INSERT INTO qb_members (memberEmail, nickname, password) VALUES (%s, %s, md5(%s))",
                       GetSQLValueString($_POST['reg_memberEmail'], "text"),
                       GetSQLValueString($_POST['reg_nickname'], "text"),
                       GetSQLValueString($_POST['reg_password'], "text"));

  mysql_select_db($database_kuizzroo, $kuizzroo);
  $Result1 = mysql_query($insertSQL, $kuizzroo) or die(mysql_error());
  
	// find the member id
	$querySQL = "SELECT LAST_INSERT_ID() AS insertID";
	$resultID = mysql_query($querySQL, $kuizzroo) or die(mysql_error());
	$row_resultID = mysql_fetch_assoc($resultID);
	$memberID = $row_resultID['insertID'];
	mysql_free_result($resultID);
  
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
	//declare two session variables and assign them
	$_SESSION['MM_Username'] = $_POST['reg_memberEmail'];
	$_SESSION['MM_MemberID'] = $row_getMember['member_id'];
	$_SESSION['MM_Nickname'] = $_POST['reg_nickname'];
	$_SESSION['MM_UserGroup'] = "";

  $insertGoTo = "createQuiz.php?justRegistered";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['memberEmail'])) {
  $loginUsername=$_POST['memberEmail'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php?fail";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_kuizzroo, $kuizzroo);
  
  $LoginRS__query=sprintf("SELECT member_id, memberEmail, password, nickname FROM qb_members WHERE memberEmail=%s AND password=md5(%s)",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $kuizzroo) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  $row_getMember = mysql_fetch_assoc($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
	$_SESSION['MM_MemberID'] = $row_getMember['member_id'];
	$_SESSION['MM_Nickname'] = $row_getMember['nickname'];
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kuizzroo - create, publish and share quizzes online!</title>
<link href="styles/main.css" rel="stylesheet" type="text/css" />
<script src="scripts/jquery-1.4.min.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationPassword.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationConfirm.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php include("header.php"); ?>
<div id="tBarContainer">
  <div id="tBar">
    <h3 id="title_login">Login</h3>
    You can login to your email to access your existing kuizzroo account. You can also create a new account if this is your first time here!
  </div></div>
<div id="contentContainer">
  <div id="content">
    <?php if(isset($_GET['fail'])){ ?>
    <div id="alert">Login unsuccessful! Did you enter an invalid username or password?</div>
    <?php } ?>
    <form id="login" name="login" method="POST" action="<?php echo $loginFormAction; ?>">
      <fieldset>
        <h4>Login to existing account</h4>
        <p>Login with your email and password if you already have an account at kuizzroo</p>
        <table width="500" border="0" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th width="150" scope="row"><label for="memberEmail">Email</label></th>
            <td><span id="sprytextfield1">
              <input type="text" name="memberEmail" id="memberEmail" />
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <th width="150" scope="row"><label for="password">Password</label></th>
            <td><span id="sprypassword1">
              <input type="password" name="password" id="password" />
            <span class="passwordRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <th width="150" scope="row">&nbsp;</th>
            <td><input type="submit" name="button" id="button" value="Login" /></td>
          </tr>
        </table>
      </fieldset>
    </form>
    <form id="register" name="register" method="POST" action="<?php echo $editFormAction; ?>">
      <fieldset>
        <h4>Register a new account</h4>
        <p>Create a new kuizzroo account. With a kuizzroo account, you can create, manage and view statistics of your quizzes.</p>
        <table width="500" border="0" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th width="150" scope="row"><label for="reg_memberEmail">Email</label></th>
            <td><span id="sprytextfield2">
              <input type="text" name="reg_memberEmail" id="reg_memberEmail" />
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <th width="150" scope="row"><label for="reg_password">Password</label></th>
            <td><span id="sprypassword2">
              <input type="password" name="reg_password" id="reg_password" />
            <span class="passwordRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <th width="150" scope="row"><label for="reg_repassword">Re-type Password</label></th>
            <td><span id="spryconfirm1">
              <input type="password" name="reg_repassword" id="reg_repassword" />
            <span class="confirmRequiredMsg">A value is required.</span><span class="confirmInvalidMsg">The values don't match.</span></span></td>
          </tr>
          <tr>
            <th width="150" scope="row"><label for="reg_nickname">Nickname</label></th>
            <td><span id="sprytextfield3">
              <input type="text" name="reg_nickname" id="reg_nickname" />
            <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <th scope="row">&nbsp;</th>
            <td><input type="submit" name="button2" id="button2" value="Create account" /></td>
          </tr>
        </table>
      </fieldset>
      <input type="hidden" name="MM_insert" value="register" />
    </form>
<p>&nbsp;</p>
  </div>
</div>
<?php include("footer.php"); ?>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none");
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");
var sprypassword2 = new Spry.Widget.ValidationPassword("sprypassword2");
var spryconfirm1 = new Spry.Widget.ValidationConfirm("spryconfirm1", "reg_password", {validateOn:["blur"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
</script>
</body>
</html>