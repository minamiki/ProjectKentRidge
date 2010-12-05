var Statusbar = {
	
	achievementsNotification: '',
	systemNotification: '',
	
	displayInformation: function(option){
			if(option==$('.statusbar-highlighted').attr('id')){
				$('.statusbar-highlighted').removeClass('statusbar-highlighted');
				$('#statusbar-info').slideUp('fast');
			}else if($('#statusbar-info').css('display')!='none'){
				$('.statusbar-highlighted').removeClass('statusbar-highlighted');
				$('#'+option).addClass('statusbar-highlighted');
			}else{
				$('#'+option).addClass('statusbar-highlighted');
				$('#statusbar-info').slideDown('fast');				
			}
			
			if(option=='notification-achievements'){
				var data = Statusbar.achievementsNotification;
				$('#statusbar-info').html('');
				$.each(data,function(i,achievement){
					var desc = achievement['description'];
					$('#statusbar-info').append("<div class='statusbar-unit'>"+desc+"<div>");
				});
			}else if(option=='notification-system'){
				var data = Statusbar.systemNotification;
				$('#statusbar-info').html('');
				$.each(data,function(i,systemnote){
					var note = systemnote['notification'];
					$('#statusbar-info').append("<div class='statusbar-unit'>"+note+"<div>");
				});
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
			$('#notification-achievements-count').html(count);
			Statusbar.achievementsNotification = data;
		});
	},
	
	updateSystemNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'system-notification'},function(data){
			var count = data.length;
			if(count>9){
				count = "···"
			}
			$("#notification-system-count").html(count);
			Statusbar.systemNotification = data;
		});
	},

}