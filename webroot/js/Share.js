var Share = {
	rootPath: 'http://apps.facebook.com/quizroo/',
	pathToSrc: '../modules/',
	appId: '154849761223760',
	
	/**
	 * Share quiz results - Publish to wall
	 * Provide opts in the following format:
	 * e.g. Share.results({'quiz_id':35,'result_id':19});
	 */
	results: function(parent,opts){
		var button = document.createElement('div');
		$(button).addClass('share-button');
		$(button).html('Share your results');
		$(parent).prepend(button);
		$(button).attr('id','share-results-button');
		$(button).click(function(){
			$.getJSON(Share.pathToSrc+'share.php/',{method:'results',quiz_id:opts.quiz_id,result_id:opts.result_id},function(data){
			var quizdetails = data.quiz_details;
			quizdetails = quizdetails[0];
			var resultdetails = data.result_details;
			resultdetails = resultdetails[0];
			Share.createResultsFeed(quizdetails.quiz_name,quizdetails.quiz_id,resultdetails.result_title,resultdetails.result_picture,resultdetails.result_description);
			});
		});
	},
	
	createResultsFeed: function(quiz_name,quiz_id,result_title,result_picture,result_description){
		FB.init({appId: Share.appId, status: true, cookie: true, xfbml: true});
		FB.ui(
		   {
			 method: 'feed',
			 name: ('I got '+result_title+' in '+quiz_name),
			 link: (Share.rootPath+'previewQuiz.php?id='+quiz_id),
			 picture: ('http://www.ewsme.com/Quizroo/quiz_images/'+result_picture),
			 //Share.rootPath+'quiz_images/imgcrop.php?w=320&amp;h=213&amp;f='+result_picture
			 caption: (result_title),
			 description: (result_description),
		   },
		   function(response) {
			 if (response && response.post_id) {
			   //alert('Post was published.');
			   $('#share-results-button').html('Your post has been published').unbind('click');
			   $('#share-results-button').css('background-color','#FFF').css('color','#000');
			 } else {
			   //alert('Post was not published.');
			 }
		   }
		);
	},

	/**
	 * Recommend to friend - Publish to friends wall
	 */
	recommend: function(parent,opts){
		var button = document.createElement('div');
		$(button).addClass('share-button');
		$(button).html('Recommend to friends');
		$(parent).prepend(button);
		$(button).click(function(){
			if($('.recommend-dialog').css('display')=='none'){
				$(button).css('background-color','#666');			
				$('.recommend-dialog').slideDown('fast');
				$(button).html('Click to here to cancel');
			}else{
				$(button).css('background-color','#3B5998');
				$('.recommend-dialog').hide();
				$(button).html('Recommend to friends');
			}
		});// end button.click
		$('.recommend-dialog').hide();
	},
	
	checkPublished: function(published){
		if(published==false){
			$('.share').hide();
		}
	},
	/**
	 * Rate quiz - Handle like event for logic
	 */
	 rate: function(parent,opts){
		$.getJSON(Share.pathToSrc+'share.php/',{method:'rate',quiz_id:opts.quiz_id,type:opts.type},function(data){alert('liked')});
		/*
		 * Legacy code to suppose both Like and dislike
		 */ 
	/*	var published = opts.published;
		if(published){
			var status = opts.status;
			if(status>0){
				$(like_button).html('Unlike');
			}
			else if(status<0){
				$(dislike_button).toggleClass('share-button-dislike');
			}
	*/		
	/*
			var dislike_button = document.createElement('div');
			$(dislike_button).addClass('share-button');
			$(dislike_button).html('Dislike');
			$(parent).prepend(dislike_button);
			$(dislike_button).click(function(){
				$(like_button).removeClass('share-button-like');
				$(dislike_button).toggleClass('share-button-dislike');
			});// end dislike_button.click
				
			var like_button = document.createElement('div');
			$(like_button).addClass('share-button');
			$(like_button).html('Like');
			$(parent).prepend(like_button);
			$(like_button).click(function(){
				//$(dislike_button).removeClass('share-button-dislike');
				if(status>0){
					status = 0;
					$(like_button).html('Unlike');
				}else{
					status = 1;
					$(like_button).html('Like');
				}
			});// end like_button.click		
		}
	*/
	 },
	 
}