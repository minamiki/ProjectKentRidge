var Statusbar = {
	
	achievements: '',
	systemNotification: '',
	imagepath: 'webroot/img/',
	
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
			
			if(option=='statusbar-achievements-logo'){
				var data = Statusbar.achievements['overview'];
				$('#statusbar-info').html('');
				$.each(data,function(i,achievement){
					var image = achievement['image'];
					var name = achievement['name'];
					var desc = achievement['description'];
					
					if(i==0){
						$('#statusbar-info').append("<div class='statusbar-unit-large clear'><img src='"+Statusbar.imagepath+image+"' class='statusbar-thumbnail-large' alt='"+name+"' /><div class='statusbar-text-container-large'><div class='statusbar-name-large'>"+name+"</div><div class='statusbar-description-large clear'>"+desc+"</div></div></div>");
					}else{
						$('#statusbar-info').append("<div class='statusbar-unit-small clear'><img src='"+Statusbar.imagepath+image+"' class='statusbar-thumbnail-small' alt='"+name+"' /><div class='statusbar-text-container-small'><div class='statusbar-name-small'>"+name+"</div><div class='statusbar-description-small'>"+desc+"</div></div></div>");
					}
				});
			}else if(option=='notification-system'){
				var data = Statusbar.systemNotification;
				$('#statusbar-info').html('');
				$.each(data,function(i,systemnote){
					var note = systemnote['notification'];
					var label = systemnote['label'];
					var color = systemnote['color'];			
					
					$('#statusbar-info').append("<div class='statusbar-unit-line clear'><div class='statusbar-label' style='background-color: #"+color+"'>"+label+"</div><div class='statusbar-system-notification>"+note+"</div></div>");
				});
				if(data==''){
					$('#statusbar-info').append("<div class='statusbar-unit-line'>There are no new notifications</div>");
				}
				Statusbar.clearSystemNotification();
			}else if(option=='statusbar-quiz-taker'){
				
			}
	},
	
	updateNotifications: function(){
		Statusbar.updateAchievements();
		Statusbar.updateSystemNotification();
	},
	
	updateAchievements: function(){
		$.getJSON('modules/updateStatus.php/',{method:'achievements'},function(data){
			Statusbar.achievements = data;
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

	readAchievements: function(){
		$.getJSON('modules/updateStatus.php/',{method:'read-achievements'},function(data){
		if(data=='success'){
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