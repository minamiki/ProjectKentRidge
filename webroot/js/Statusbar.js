var Statusbar = {
	
	pathToSrc: '../modules/',
	achievements: '',
	systemNotification: '',
	imagepath: 'img/',
	months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	
	displayInformation: function(option){
		
		$('#statusbar-info').removeClass('statusbar-menu').html('');
		
		/*
		 * Fill the drop down menu with the appropriate content.
		 */
		
		 /*
		  * Display information 
		  */
		if(option=='statusbar-achievements-logo'){
			var data = Statusbar.achievements['overview'];
			var totalachievements = Statusbar.achievements['achievements']['score'];
			var score = parseInt(Statusbar.achievements['quiztaker']['quiztaker_score'])+parseInt(Statusbar.achievements['quizcreator']['quizcreator_score']);
			$('#statusbar-info').html(
				"<div class='statusbar-info-title'>Achievements<div class='statusbar-info-more'><a href=''>more</a></div></div>"+
				"<div class='statusbar-score-text'>Number of Achievements: "+totalachievements+"</div>"+
				"<div class='statusbar-score-text'>Total Quiz Points: "+score+"</div>"+
				"<div class='statusbar-info-subtitle'>Most Recent Achievements</div>"
			);
			$.each(data,function(i,achievement){
				var image = achievement['image'];
				var name = achievement['name'];
				var desc = achievement['description'];
				
				if(i==0){
					$('#statusbar-info').append(
						"<div class='statusbar-unit-large clear'>"+
							"<img src='"+Statusbar.imagepath+image+"' class='statusbar-thumbnail-large' alt='"+name+"' />"+
							"<div class='statusbar-text-container-large'>"+
								"<div class='statusbar-name-large'>"+name+"</div>"+
								"<div class='statusbar-description-large clear'>"+desc+"</div>"+
							"</div>"+
						"</div>"
					);
				}else{
					$('#statusbar-info').append(
						"<div class='statusbar-unit-small clear'>"+
							"<img src='"+Statusbar.imagepath+image+"' class='statusbar-thumbnail-small' alt='"+name+"' />"+
							"<div class='statusbar-text-container-small'>"+
								"<div class='statusbar-name-small'>"+name+"</div>"+
								"<div class='statusbar-description-small'>"+desc+"</div>"+
							"</div>"+
						"</div>"
					);
				}
			});
		}else if(option=='notification-system'){
			var data = Statusbar.systemNotification;
			var others = data.others;
			var system = data.system;
			
			$('#statusbar-info').append(
			"<div class='statusbar-info-title'>"+
				"Notifications"+
				"<div class='statusbar-info-more'>"+
					"<a href=''>more</a>"+
				"</div>"+
			"</div>"
			);
			
			$.each(system,function(i,systemnote){
				var note = systemnote['notification'];
				var label = systemnote['label'];
				var color = systemnote['color'];			
				var date = Statusbar.convertDate(systemnote['timestamp']);
				var newstring = "";
				
				if(Statusbar.checkNotWeekOld(systemnote['timestamp'])){
					newstring = "<div class='statusbar-newtext'>NEW</div>";
				}
				
				$('#statusbar-info').append(
					"<div class='statusbar-unit-line clear'>"+
						"<div class='statusbar-time'>"+Statusbar.displayDate(date,'notification')+" "+newstring+"</div>"+
						"<div class='statusbar-label' style='background-color: #"+color+"'>"+label+"</div>"+
						"<div class='statusbar-system-notification>"+note+"</div>"+
					"</div>"
				);
			});
			
			$.each(others,function(i,systemnote){
				var note = systemnote['notification'];
				var label = systemnote['label'];
				var color = systemnote['color'];			
				var date = Statusbar.convertDate(systemnote['timestamp']);
				var newstring = "";
				
				if(systemnote.isRead==0){
					newstring = "<div class='statusbar-newtext'>NEW</div>";
				}
				
				$('#statusbar-info').append(
					"<div class='statusbar-unit-line clear'>"+
						"<div class='statusbar-time'>"+Statusbar.displayDate(date,'notification')+" "+newstring+"</div>"+
						"<div class='statusbar-label' style='background-color: #"+color+"'>"+label+"</div>"+
						"<div class='statusbar-system-notification>"+note+"</div>"+
					"</div>"
				);
			});
			
			if(others=='' && system==''){
				$('#statusbar-info').append(
					"<div class='statusbar-unit-line'>There are no recent notifications</div>"
				);
			}
			Statusbar.clearSystemNotification();
		}else if(option=='statusbar-scores'){
			var qt_data = Statusbar.achievements['quiztaker'];
			var qt_score = qt_data['quiztaker_score'];
			var qt_today = qt_data['quiztaker_score_today'];

			var qc_data = Statusbar.achievements['quizcreator'];
			var qc_score = qc_data['quizcreator_score'];
			var qc_today = qc_data['quizcreator_score_today'];
			
			var rank = Statusbar.achievements['rank'];
			var rankname = rank['name'];
			var rankdesc = rank['description'];
			var level = rank['level'];
			var image = rank['image'];
			
			$('#statusbar-info').html(
				"<div class='statusbar-quiztaker-container'>"+
					"<div class='statusbar-info-title'>Quiz Taker</div>"+
					"<div class='statusbar-line'>"+
						"<div class='statusbar-score-text'>Total Points: "+qt_score+"</div>"+
						"<div class='statusbar-score-text'>Today's Quiz Taker Points: "+qt_today+"</div>"+
					"</div>"+
				"</div>"+
				"<div class='statusbar-quizcreator-container'>"+
					"<div class='statusbar-info-title'>Quiz Creator<div class='statusbar-info-more'><a href=''>more</a></div></div>"+
					"<div class='statusbar-line'>"+
						"<div class='statusbar-score-text'>Total Popularity Points: "+qc_score+"</div>"+
						"<div class='statusbar-score-text'>Today's Quiz Creator Points: "+qc_today+"</div>"+
					"</div>"+
				"</div>"+
				"<div class='statusbar-info-subtitle'>Current Rank</div>"+
				"<div class='statusbar-unit-large clear'><img src='"+Statusbar.imagepath+image+"' class='statusbar-thumbnail-large' alt='"+rankname+"' />"+
					"<div class='statusbar-text-container-large'>"+
						"<div class='statusbar-name-large'>"+rankname+"</div>"+
						"<div class='statusbar-description-large clear'>Level "+level+"</div>"+
					"</div>"+
				"</div>"+
				"<div class='statusbar-info-subtitle'>Current Progress</div>"+
				"<div id='scorebar-container'>"+
					"<div class='scorebar-text-left'>"+level+"</div>"+
					"<div class='scorebar-text-right'>"+(parseInt(level)+1)+"</div>"+
					"<div id='scorebar'><div id='scorebar-progress'></div><div id='scorebar-info'</div></div>"+
				"</div>"
			);
			
			Statusbar.displayBar();
		
		/*
		 * Display menus
		 */
		}else if(option=='statusbar-quiz'){			
			$('#statusbar-info').addClass('statusbar-menu').html('');
			
			var createQuiz = document.createElement('div');
			$('#statusbar-info').append(createQuiz);
			$(createQuiz).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Create</div>"+
				"<div class='statusbar-menu-desc'>Create a new quiz</div>"
			);
			$(createQuiz).click(function(){
				goToURL('createQuiz.php');
				$(createQuiz).css('background-color','#333');
			});
			
			var manageQuiz = document.createElement('div');
			$('#statusbar-info').append(manageQuiz);
			$(manageQuiz).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Manage</div>"+
				"<div class='statusbar-menu-desc'>Manage quizzes you have created</div>"
			);
			$(manageQuiz).click(function(){
				goToURL('manageQuiz.php');
				$(manageQuiz).css('background-color','#333');
			});
			
			/*
			var viewQuiz = document.createElement('div');
			$('#statusbar-info').append(viewQuiz);
			$(viewQuiz).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Topics</div>"+
				"<div class='statusbar-menu-desc'>View all quiz topics</div>"
			);
			$(viewQuiz).click(function(){
				//goToURL('viewQuiz.php');
				//$(viewQuiz).css('background-color','#333');
				featureUnavailable();
			});
			*/
		}else if(option=='statusbar-friends'){
			$('#statusbar-info').addClass('statusbar-menu').html("");
			
			var overview = document.createElement('div');
			$('#statusbar-info').append(overview);
			$(overview).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Overview</div>"+
				"<div class='statusbar-menu-desc'>View information about your friends</div>"
			);
			$(overview).click(function(){
				featureUnavailable();	
			});
			
			var leaderboard = document.createElement('div');
			$('#statusbar-info').append(leaderboard);
			$(leaderboard).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Leaderboard</div>"+
				"<div class='statusbar-menu-desc'>View the top-ranked players</div>"
			);
			$(leaderboard).click(function(){
				goToURL('leaderBoard.php');
				$(leaderboard).css('background-color','#333');
			});
			
			var invite = document.createElement('div');
			$('#statusbar-info').append(invite);
			$(invite).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Invite</div>"+
				"<div class='statusbar-menu-desc'>Invite your friends to Quizroo</div>"
			);
			$(invite).click(function(){
				//goToURL('inviteFriends.php');
				//$(invite).css('background-color','#333');
				featureUnavailable();
			});
			
		}else if(option=='statusbar-profile'){
			$('#statusbar-info').addClass('statusbar-menu').html("");
			
			var stats = document.createElement('div');
			$('#statusbar-info').append(stats);
			$(stats).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Statistics</div>"+
				"<div class='statusbar-menu-desc'>View statistics for your activities</div>"
			);
			$(stats).click(function(){
				goToURL('statistics.php');
				$(stats).css('background-color','#333');
			});
			
			var history = document.createElement('div');
			$('#statusbar-info').append(history);
			$(history).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>History</div>"+
				"<div class='statusbar-menu-desc'>View information about what you have done</div>"
			);
			$(history).click(function(){
				//goToURL('history.php');
				//$(history).css('background-color','#333');
				featureUnavailable();
			});
			
			var settings = document.createElement('div');
			$('#statusbar-info').append(settings);
			$(settings).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Settings</div>"+
				"<div class='statusbar-menu-desc'>Change settings for your Quizroo Account</div>"
			);
			$(settings).click(function(){
				//goToURL('settings.php');
				//$(settings).css('background-color','#333');
				featureUnavailable();
			});
		}else if(option=='statusbar-about'){
			$('#statusbar-info').addClass('statusbar-menu').html(
			""
			);
			
			var help = document.createElement('div');
			$('#statusbar-info').append(help);
			$(help).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Help</div>"+
				"<div class='statusbar-menu-desc'>Get help using Quizroo</div>"
			);
			$(help).click(function(){
				goToURL('previewQuiz.php?id=1');
				//$(help).css('background-color','#333');
				//featureUnavailable();
			});
			
			var quizroo = document.createElement('div');
			$('#statusbar-info').append(quizroo);
			$(quizroo).addClass('statusbar-menu-item').html(
				"<div class='statusbar-menu-title'>Quizroo</div>"+
				"<div class='statusbar-menu-desc'>Know more about Quizroo</div>"
			);
			$(quizroo).click(function(){
				//goToURL('quizroo.php');
				//$(quizroo).css('background-color','#333');
				featureUnavailable();
			});
		}else if(option=='statusbar-search'){
			/*
			$('#statusbar-searchmenu-button').toggleClass('statusbar-searchmenu-button-selected');
			$('#statusbar-info').addClass('statusbar-menu').addClass('statusbar-menu-search').html(
			"<div class='statusbar-search-bar'><input id='statusbar-search-textfield' type='text'/><div id='statusbar-search-button'><div class='statusbar-search-icon'><span>search</span></div></div></div>"
			);
			*/
			featureUnavailable();
		}
		
		/*
		 * Animate and display the drop down menu. Hide the drop down menu if it is already showing.
		 */
		if(option==$('.statusbar-highlighted').attr('id')){
			// Hide drop down menu since it is already displayed.
			$('.statusbar-highlighted').removeClass('statusbar-highlighted');
			$('#statusbar-info').slideUp('fast');
		}else if($('#statusbar-info').css('display')!='none'){
			// Show drop down menu but update highlighted option since menu is already displayed
			// and another menu is chosen.
			$('.statusbar-highlighted').removeClass('statusbar-highlighted');
			$('#'+option).addClass('statusbar-highlighted');
			$('#'+option).attr("type","statusbar");
			$('#'+option).find('*').attr("type","statusbar");
			$('#statusbar-info').attr("type","statusbar");
			$('#statusbar-info').find('*').attr("type","statusbar");
		}else{
			// Show drop down menu with animation sine it is hidden.
			$('#'+option).addClass('statusbar-highlighted');
			$('#statusbar-info').slideDown('fast');
			$('#'+option).attr("type","statusbar");	
			$('#'+option).find('*').attr("type","statusbar");	
			$('#statusbar-info').attr("type","statusbar");
			$('#statusbar-info').find('*').attr("type","statusbar");				
		}
			
	},
	
	/*
	 * Hides drop down menu.
	 */
	hideInfo: function(){
		$('.statusbar-highlighted').removeClass('statusbar-highlighted');
			$('#statusbar-info').slideUp('fast');
	},
	
	/*
	 * Dismiss drop down menu if a click is performed outside of statusbar elements.
	 */
	triggerHideInfo: function(event){	
		if(!($(event.target).attr("type")=="statusbar")){
			Statusbar.hideInfo();
		}
	},
	
		
	update: function(){
		Statusbar.updateAchievements();
		Statusbar.updateSystemNotification();
	},
	
	/*
	 * Retrieves achievements from database and stores it locally. Updates ui accordingly.
	 */
	updateAchievements: function(){
		$.getJSON(Statusbar.pathToSrc+'updateStatus.php/',{method:'achievements'},function(data){
			Statusbar.achievements = data;
			$('#statusbar-achievements-count').html(Statusbar.handleCount(data['achievements']['score'])).contents().stretch({max:18});
			$('#statusbar-quiztaker-count-total').html(Statusbar.handleCount(data['quiztaker']['quiztaker_score'])).contents().stretch({max:14});
			$('#statusbar-quiztaker-count-today').html(Statusbar.handleCount(data['quiztaker']['quiztaker_score_today'])).contents().stretch({max:14});
			$('#statusbar-quizcreator-count-total').html(Statusbar.handleCount(data['quizcreator']['quizcreator_score'])).contents().stretch({max:14});
			$('#statusbar-quizcreator-count-today').html(Statusbar.handleCount(data['quizcreator']['quizcreator_score_today'])).contents().stretch({max:14});
		});
	},
	
	/*
	 * Retrieves system notifications from database and stores it locally. Updates ui accordingly.
	 */
	updateSystemNotification: function(){
		$.getJSON(Statusbar.pathToSrc+'updateStatus.php/',{method:'system-notification'},function(data){
			var others = data.others;
			var system = data.system;
			var count = 0;
			
			$.each(others,function(i,othersnote){
				if(othersnote.isRead==0){
					count++
				}
			});
			
			$.each(system,function(i,systemnote){
				if(Statusbar.checkNotWeekOld(systemnote.timestamp)){
					count++;
				}
			});
			
			if(count==0){
				$('#notification-system-count').hide();	
			}else{
				$('#notification-system-count').show();
			}
			$("#notification-system-count").html('NEW');
			Statusbar.systemNotification = data;
		});
	},

	/*
	 * Updates the database to mark all achievemtents as read. (not used)
	 */
	readAchievements: function(){
		$.getJSON(Statusbar.pathToSrc+'updateStatus.php/',{method:'read-achievements'},function(data){
		if(data=='success'){
		}
		});
	},
	
	/*
	 * Updates the database to mark all system notifications as read and updates ui.
	 */
	clearSystemNotification: function(){
		$.getJSON(Statusbar.pathToSrc+'updateStatus.php/',{method:'clear-system-notification'},function(data){
		});
	},
	
	/*
	 * Checks if a timestamp is less than a week old.
	 */
	checkNotWeekOld: function(timestamp){
		now = new Date();
		return (now.getTime()-Statusbar.convertDate(timestamp).getTime())<604800000
	},
	
	/*
	 * Converts a SQL timestamp (e.g. 2010-08-11 23:12:32) to a date object.
	 */
	convertDate: function(string){
		var splitString = string.split(" ");
		var date = new Date(splitString[0]);
		var time = splitString[1];
		time = time.split(":");
		date.setHours(time[0],time[1],time[2]);
		
		return date;
	},
	
	/*
	 * Displays a date object in a format specified.
	 */
	displayDate: function(date, format){
		var dateString = '';
		
		if(format=='notification'){
			var time = date.getHours()+":";
			if(date.getMinutes()<10){
				time+= '0'+ date.getMinutes();
			}else{
				time+= date.getMinutes();
			}
			dateString = date.getDate()+" "+Statusbar.shortMonths[date.getMonth()]+" @ "+ time;
		}
		
		return dateString;
	},
	
	handleCount: function(count){
		if(count>999){
			count = (count/1000).toFixed(1);
			return count+'K';
		}else{
			return count;
		}
	},
	
	calculateLevel: function(score){
		if(score==0){
			return 0;
		}else if(score<11){
			return 1;
		}else{
			var level = 2;
			for(i=2,calculated=1;calculated<score;i++){
				calculated+=Math.floor(15*Math.log(level));
				level = i;
			}
			return level;
		}
	},
	
	displayBar: function(){
		var level = Statusbar.achievements['rank']['level'];
		var score = (parseInt(Statusbar.achievements['quiztaker']['quiztaker_score'])+parseInt(Statusbar.achievements['quizcreator']['quizcreator_score']));		
		var levelScore = Statusbar.achievements['rank']['levelscore'];
		var nextLevelScore = Statusbar.achievements['rank']['nextlevelscore'];
		var ratio = 0;
		var text = '';
		
		ratio = (score-levelScore)/(nextLevelScore-levelScore);
		text = (score-levelScore)+' of '+(nextLevelScore-levelScore)+' <span style="font-size:11px;">points to next level</span>';
		
		if(ratio<0.5){
			$('#scorebar-info').html(text);
		}else{
			$('#scorebar-progress').html(text);			
		}
		
		// Update scorebar width if necessary. Current width is 460px.
		$('#scorebar-progress').width(Math.min((440*ratio),440));
	},
}