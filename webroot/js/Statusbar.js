var Statusbar = {
	
	displayInformation: function(option){
			if(option==$('.statusbar-highlighted').attr('id')){
				$('.statusbar-highlighted').removeClass('statusbar-highlighted');
				$('#statusbar-information').hide();
			}else if($('#statusbar-information').css('display')!='none'){
				$('.statusbar-highlighted').removeClass('statusbar-highlighted');
				$('#'+option).addClass('statusbar-highlighted');
			}else{
				$('#'+option).addClass('statusbar-highlighted');
				$('#statusbar-information').show();				
			}
	},
	
	updateNotifications: function(){
		Statusbar.updateAchievementsNotification();
		Statusbar.updateSystemNotification();
	},
	
	updateAchievementsNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'achievements-notification'},function(data){
			var count = data.length;
			if(count>9){
				count = "···"
			}
			$("#notification-achievements-count").html(count);
		});
	},
	
	updateSystemNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'system-notification'},function(data){
			var count = data.length;
			if(count>9){
				count = "···"
			}
			$("#notification-system-count").html(count);
		});
	},

}