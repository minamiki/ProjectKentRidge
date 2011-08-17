/*******************************
 *Dashboard scripts: consists of:
 *	-toggle panel
 *	-dashboard handler
 *******************************/

var Dashboard = {
	recentPanelState: 0,
	
	togglePanel: function(){
		if(this.recentPanelState){
			// toggle sliding up a page and display another page when user clicks "More"
			$('#recent-extended').slideUp();
			$('#recent-toggle').text("More");
			this.recentPanelState = 0;
		}else{
			// toggle sliding down a page when user clicks "Hide"
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