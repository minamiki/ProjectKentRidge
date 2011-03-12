<?php
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