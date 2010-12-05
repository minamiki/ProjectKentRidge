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
				Statusbar.clearAchievementsNotification();
			}else if(option=='notification-system'){
				var data = Statusbar.systemNotification;
				$('#statusbar-info').html('');
				$.each(data,function(i,systemnote){
					var note = systemnote['notification'];
					$('#statusbar-info').append("<div class='statusbar-unit'>"+note+"<div>");
				});
				Statusbar.clearSystemNotification();
			}
	},
	
	updateNotifications: function(){
		Statusbar.updateAchievementsNotification();
		Statusbar.updateSystemNotification();
	},
	
	updateAchievementsNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'achievements-notification'},function(data){
			var count = data.length;
			if(count==0){
				$('#notification-achievements-count').hide();	
			}else if(count>9){
				$('#notification-achievements-count').show();				
				count = "···"
			}else{
				$('#notification-achievements-count').show();
			}
			$('#notification-achievements-count').html(count);
			Statusbar.achievementsNotification = data;
		});
	},
	
	updateSystemNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'system-notification'},function(data){
			var count = data.length;
			if(count==0){
				$('#notification-system-count').hide();	
			}else if(count>9){
				$('#notification-system-count').show();
				count = "···"
			}else{
				$('#notification-system-count').show();
			}
			$("#notification-system-count").html(count);
			Statusbar.systemNotification = data;
		});
	},

	clearAchievementsNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'clear-achievements-notification'},function(data){
		if(data=='success'){
			$('#notification-achievements-count').html(0);
			$('#notification-achievements-count').hide();
		}
		});
	},
	
	clearSystemNotification: function(){
		$.getJSON('modules/updateStatus.php/',{method:'clear-system-notification'},function(data){
		if(data=='success'){
			$('#notification-system-count').html(0);
			$('#notification-system-count').hide();
		}
		});
	},
}