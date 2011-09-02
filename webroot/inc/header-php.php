<?php
header('P3P:CP="QZR"');
require("../modules/member.php");
require("../modules/variables.php");

// create the member object
$member = new Member();

if($VAR_SYSTEM_MAINTENANCE){
	if(!$member->isAdmin()){
		header("Location: maintenance.php");
		exit();
	}
}

?>