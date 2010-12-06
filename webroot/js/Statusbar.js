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
				var score = parseInt(Statusbar.achievements['quiztaker']['quiztaker_score'])+parseInt(Statusbar.achievements['quizcreator']['quizcreator_score']);
				$('#statusbar-info').html("<div class='statusbar-score-text'>Total Achievement Points: "+score+"</div><div class='statusbar-info-title'>Most Recent Achievements</div>");
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
			}else if(option=='statusbar-quiztaker'){
				var data = Statusbar.achievements['quiztaker'];
				var score = data['quiztaker_score'];
				var today = data['quiztaker_score_today'];
				
				$('#statusbar-info').html("<div class='statusbar-line'><div class='statusbar-score-text'>Total Quiz Taker Points: "+score+"</div><div class='statusbar-score-text'>Today's Points: "+today+"</div></div>");
			}else if(option=='statusbar-quizcreator'){
				var data = Statusbar.achievements['quizcreator'];
				var score = data['quizcreator_score'];
				var today = data['quizcreator_score_today'];
				
				$('#statusbar-info').html("<div class='statusbar-line'><div class='statusbar-score-text'>Total Quiz Creator Points: "+score+"</div><div class='statusbar-score-text'>Today's Points: "+today+"</div></div>");
			}
	},
	
	updateNotifications: function(){
		Statusbar.updateAchievements();
		Statusbar.updateSystemNotification();
	},
	
	updateAchievements: function(){
		$.getJSON('modules/updateStatus.php/',{method:'achievements'},function(data){
			Statusbar.achievements = data;
			var totalscore = parseInt(data['quiztaker']['quiztaker_score'])+parseInt(data['quizcreator']['quizcreator_score']);
			$('#statusbar-achievements-count').html(totalscore);
			$('#statusbar-quiztaker-count-total').html(data['quiztaker']['quiztaker_score']);
			$('#statusbar-quiztaker-count-today').html(data['quiztaker']['quiztaker_score_today']);
			$('#statusbar-quizcreator-count-total').html(data['quizcreator']['quizcreator_score']);
			$('#statusbar-quizcreator-count-today').html(data['quizcreator']['quizcreator_score_today']);
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