// Dashboard scripts

var Dashboard = {
	recentPanelState: 0,
	
	togglePanel: function(){
		if(this.recentPanelState){
			$('#recent-extended').slideUp();
			$('#recent-toggle').text("More");
			this.recentPanelState = 0;
		}else{
			$('#recent-extended').slideDown();
			$('#recent-toggle').text("Hide");
			this.recentPanelState = 1;
		}
	}
}

$(document).ready(function(){
	// dashboard handlers
	$('#recent-toggle').click(function(){
		Dashboard.togglePanel();
	});
});