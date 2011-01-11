var Splash = {

	imagepath: 'img/',

	display: function(achievements){
		// Check if there are any achievements to display
		if(achievements!=''){
			//Create div
			var splash = document.createElement('div');
			
			$('body').append(splash);
			$(splash).attr('id','splash');
			$(splash).html('<div id="splash-content"><div class="splash-info-subtitle">New Achievements Unlocked</div></div>');
			
			// For each achievement, add it to the splash screen
			$.each(achievements,function(i,achievement){
				var image = achievement.image;
				var name = achievement.name;
				var desc = achievement.description;
				var type = achievement.type;
				
				if(type==3){
					$('#splash-content').prepend("<div class='splash-unit-large clear'><img src='"+Splash.imagepath+image+"' class='splash-thumbnail-large' alt='"+name+"' /><div class='splash-text-container-large'><div class='splash-name-large'>"+name+"</div><div class='splash-description-large'>"+desc+"</div></div></div>");
				}else{
					$('#splash-content').append("<div class='splash-unit-small clear'><img src='"+Splash.imagepath+image+"' class='splash-thumbnail-small' alt='"+name+"' /><div class='splash-text-container-small'><div class='splash-name-small'>"+name+"</div><div class='splash-description-small'>"+desc+"</div></div></div>");
				}
			});
			
			$('#splash-content').append('<div id="splash-dismiss">Dismiss</div>');
			$('#splash-content').click(function(){
				$('#splash').hide();
			});
		}
	},

}