<div id="fb-root"></div>
<script>
//======================================================
// Load and initialize the facebook javascript framework
//======================================================
window.fbAsyncInit = function(){
	FB.init({appId: '<?php echo $FB_APPID; ?>', session: <?php echo json_encode($member->session); ?>, status: true, cookie: true, xfbml: true});
	// Enable canvas height auto-resize
	FB.Canvas.setSize({ height: 400 });
	FB.Canvas.setAutoResize();
};
(function(){
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
	document.getElementById('fb-root').appendChild(e);
}());
function gotoTop(){
	//FB.Canvas.setSize({ height: 400 });
	//FB.Canvas.setAutoResize();
}
</script>
<script type="text/javascript" src="js/jquery-1.4.4.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.9.js"></script>
<script type="text/javascript" src="js/jquery.stretch-0.9.3.min.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/Statusbar.js"></script>
<script type="text/javascript" src="js/Share.js"></script>
<?php // load repective scripts only when required (single scripts)
require("../modules/variables.php");
switch(basename($_SERVER['SCRIPT_NAME'])){
	case "index.php": $src = "Dashboard.js"; break;
	case "takeQuiz.php": $src = "Quiz.js"; break;
	default: $src = "";
}

if($src != ""){
?><script type="text/javascript" src="js/<?php echo $src; ?>"></script><?php } ?>
<script>
//======================================================
// Includes javascript to be executed on document ready
//======================================================
$(document).ready(function(){
	// status bar handlers
	Statusbar.update();
	//Share.results({'quiz_id':19,'result_id':35});
	
	$('#notification-system').click(function(){
		Statusbar.updateSystemNotification();
		Statusbar.displayInformation('notification-system');
	});
	$('#statusbar-achievements-logo').click(function(){
		Statusbar.displayInformation('statusbar-achievements-logo');
	});
	$('#statusbar-scores').click(function(){
		Statusbar.displayInformation('statusbar-scores');
	});
	$('#statusbar-quizcreator').click(function(){
		Statusbar.displayInformation('statusbar-quizcreator');
	});

	$('#statusbar-quiz').click(function(){
		Statusbar.displayInformation('statusbar-quiz');
	});
	$('#statusbar-friends').click(function(){
		Statusbar.displayInformation('statusbar-friends');
	});
	$('#statusbar-profile').click(function(){
		Statusbar.displayInformation('statusbar-profile');
	});
	$('#statusbar-about').click(function(){
		Statusbar.displayInformation('statusbar-about');
	});
	$('#statusbar-search').click(function(){
		Statusbar.displayInformation('statusbar-search');
	});
	// Handles click outside of statusbar.
	$(document).click(function(event){
		Statusbar.triggerHideInfo(event);
	});
	$('#splash').height($('body').height());
});

</script>