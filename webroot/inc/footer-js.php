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

</script>