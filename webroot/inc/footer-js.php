<div id="fb-root"></div>
<script type="text/javascript" src="js/jquery-1.4.min.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/Statusbar.js"></script>
<?php if(basename($_SERVER['SCRIPT_NAME']) == "index.php"){ // load repective scripts only when required ?>
<script type="text/javascript" src="js/Dashboard.js"></script>
<?php }elseif(basename($_SERVER['SCRIPT_NAME']) == "takeQuiz.php"){ ?>
<script type="text/javascript" src="js/Quiz.js"></script>
<?php } ?>
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
	Statusbar.updateNotifications();
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
});

</script>