<div id="fb-root"></div>
<script type="text/javascript" src="js/jquery-1.4.min.js"></script>
<script type="text/javascript" src="js/jquery.stretch-0.9.3.min.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/Statusbar.js"></script>
<?php // load repective scripts only when required (single scripts)
switch(basename($_SERVER['SCRIPT_NAME'])){
	case "index.php": $src = "Dashboard.js"; break;
	case "takeQuiz.php": $src = "Quiz.js"; break;
	default: $src = "";
}

if($src != ""){
?><script type="text/javascript" src="js/<?php echo $src; ?>"></script><?php } ?>
<script>

//======================================================
// Load and initialize the facebook javascript framework
//======================================================
window.fbAsyncInit = function(){
	FB.init({appId: '2fdae40a1de2190372e48d07a1c85c79', status: true, cookie: true, xfbml: true});
	// Enable canvas height auto-resize
	FB.Canvas.setAutoResize();	 
};
(function(){
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
	document.getElementById('fb-root').appendChild(e);
}());

//======================================================
// Includes javascript to be executed on document ready
//======================================================

$(document).ready(function(){
	// status bar handlers
	Statusbar.update();

	$('#notification-system').click(function(){
		Statusbar.updateSystemNotification();
		Statusbar.displayInformation('notification-system');
	});
	$('#statusbar-achievements-logo').click(function(){
		Statusbar.displayInformation('statusbar-achievements-logo');
	});
	$('#statusbar-quiztaker').click(function(){
		Statusbar.displayInformation('statusbar-quiztaker');
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
});

</script>