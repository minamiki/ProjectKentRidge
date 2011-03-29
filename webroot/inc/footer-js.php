<?php require("../modules/variables.php"); ?>
<script type="text/javascript" src="js/Statusbar.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/Share.js"></script>
<script>
//======================================================
// Includes javascript to be executed on document ready
//======================================================
$(document).ready(function(){
	// status bar handlers
	Statusbar.update(<?php echo $member->id; ?>);
	
	$('#notification-system').click(function(){
		Statusbar.updateSystemNotification(<?php echo $member->id; ?>);
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

//======================================================
// Load and initialize the facebook javascript framework
//======================================================
window.fbAsyncInit = function(){
	FB.init({appId: '<?php echo $FB_APPID; ?>', session: <?php echo json_encode($member->session); ?>, status: true, cookie: true, xfbml: true});

	// Enable canvas height auto-resize
	FB.Canvas.setAutoResize();
	
	if(typeof(isSharing) != 'undefined'){
		if(isSharing){
			// Subscribe to Facebook Like event to handle it for our own data. 
			FB.Event.subscribe('edge.create', function(response) {
				Share.rate($('#user-actions-container'),{'quiz_id': <?php echo (isset($quiz)) ? $quiz->quiz_id : 0; ?>,'type':1});
			});
			// Subscribe to Facebook Unlike event to handle it for our own data. 
			FB.Event.subscribe('edge.remove', function(response) {
				Share.rate($('#user-actions-container'),{'quiz_id': <?php echo (isset($quiz)) ? $quiz->quiz_id : 0; ?>,'type':-1});
			});
		}
	}
};

//======================================================
// Loads the Facebook Javacript API
//======================================================
(function(){
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
	document.getElementById('fb-root').appendChild(e);
}());
</script>