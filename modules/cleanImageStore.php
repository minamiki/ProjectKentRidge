<?php // imagestore cleaner
require('quizrooDB.php');
// go through all the images

$linked = 0;
$orphan = 0;
$system = 0;
foreach(glob("../quiz_images/*") as $filename){
	
	$unikey = substr(basename($filename), 0, 8);
	
	mysql_select_db($database_quizroo, $quizroo);
	// check if image is used
	if(basename($filename) != "none.gif" && basename($filename) != "imgcrop.php"){
		$queryQuiz = sprintf("SELECT quiz_id FROM q_quizzes WHERE quiz_key = %s", GetSQLValueString($unikey, "text"));
		$getQuiz = mysql_query($queryQuiz, $quizroo) or die(mysql_error());
		$row_getQuiz = mysql_fetch_assoc($getQuiz);
		$totalRows_getQuiz = mysql_num_rows($getQuiz);
		
		if($totalRows_getQuiz != 0){
			$linked++;
		}else{
			if(isset($_GET["remove"])){
				unlink($filename);
			}
			$orphan++;
		}
	}else{
		$system++;
	}
}
if(isset($_GET["remove"])){
	echo sprintf("Kept %d linked files. Removed %d orphaned files. Kept %d system files.", $linked, $orphan, $system);
}else{
	echo sprintf("Found %d linked files. Found %d orphaned files. Found %d system files.", $linked, $orphan, $system);
}

?>