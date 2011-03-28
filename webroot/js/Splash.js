var Splash = {

	imagepath: 'img/',

	display: function(new_achievements){
		// Check if there are any achievements to display
		if(new_achievements!=''){
			//Create div
			var splash = document.createElement('div');
			
			$('body').append(splash);
			$(splash).attr('id','splash');
			$(splash).html('<div id="splash-content"><div class="splash-info-subtitle">New Achievements Unlocked</div></div>');
			$('#splash-content').hide();
			// For each achievement, add it to the splash screen
			$.each(new_achievements,function(i,achievement){
				var image = achievement.image;
				var name = achievement.name;
				var desc = achievement.description;
				var type = achievement.type;
				
				if(type==3){
					$('#splash-content').prepend("<div class='splash-unit-large clear'><img src='"+Splash.imagepath+image+"' class='splash-thumbnail-large' alt='"+name+"' /><div class='splash-text-container-large'><div class='splash-name-large'>"+name+"</div><div class='splash-description-large'>"+desc+"</div></div><div class='splash-info-subtitle'>Current Progress</div>");
				}else{
					$('#splash-content').append("<div class='splash-unit-small clear'><img src='"+Splash.imagepath+image+"' class='splash-thumbnail-small' alt='"+name+"' /><div class='splash-text-container-small'><div class='splash-name-small'>"+name+"</div><div class='splash-description-small'>"+desc+"</div></div></div>");
				}
			});
			
			$('#splash-content').append('<div id="splash-dismiss">Dismiss</div>');
			$('#splash-content').slideDown('slow');
			$('#splash-content').click(function(){
				$('#splash-content').slideUp('slow');
				$('#splash').fadeOut();
			});
		}
	},
	
	displayBar: function(scorebar_details){
		var level = scorebar_details.achievements['level'];
		var score = scorebar_details.achievements['quiztaker_score'];		
		var levelScore = scorebar_details.achievements['levelscore'];
		var nextLevelScore = scorebar_details.achievements['nextlevelscore'];
		var ratio = 0;
		var text = '';
		
		ratio = (score-levelScore)/(nextLevelScore-levelScore);
		text = (score-levelScore)+' of '+(nextLevelScore-levelScore)+' <span style="font-size:11px;">points to next level</span>';
		
		if(ratio<0.5){
			$('#splash-scorebar-info').html(text);
		}else{
			$('#splash-scorebar-progress').html(text);			
		}
		
		// Update scorebar width if necessary. Current width is 460px.
		$('#splash-scorebar-progress').width(Math.min((440*ratio),440));
	},

}