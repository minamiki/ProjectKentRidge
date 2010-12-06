<div id="fb-root"></div>
<script type="text/javascript" src="webroot/js/jquery-1.4.min.js"></script>
<script type="text/javascript" src="webroot/js/Statusbar.js"></script>
<script type="text/javascript" src="webroot/js/Dashboard.js"></script>
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
	
	// dashboard handlers
	$('#recent-toggle').click(function(){
		Dashboard.togglePanel();
	});
});

</script>