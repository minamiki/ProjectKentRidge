// CSS/Javascript Slideshow

$(document).ready(function() {
	// number of questions completed
	var numCompleted = 0;
	
	//Set Default State of each portfolio piece
	$("#question_paging").show();
	$("#question_paging span:first").addClass("active");
	
	//Get size of images, how many there are, then determin the size of the image reel.
	var imageWidth = $("#questionContainer").width();
	var imageSum = $("#question_reel div").size();
	var imageReelWidth = imageWidth * imageSum;
	
	//Adjust the image reel to its new size
	$("#question_reel").css({'width' : imageReelWidth});
	
	//Paging + Slider Function
	rotate = function(){	
		var triggerID = $active.attr("title") - 1; //Get number of times to slide
		var image_reelPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		$("#question_paging span").removeClass('active'); //Remove all active class
		$active.addClass('active'); //Add active class (the $active is declared in the rotateSwitch function)
		
		//Slider Animation
		$("#question_reel").animate({ 
			left: -image_reelPosition
		}, 500 );
	
	}; 
	
	
	function updateProgress(){
		if(typeof($active) != 'undefined'){
			// check if question is answered
			if($("#takeQuiz input[name='q"+$active.attr("title")+"']:checked").val() != undefined){
				if(!$active.hasClass('completed')){
					$active.addClass('completed');
					numCompleted++;
				}
			}
		}
		var length = (710/imageSum * numCompleted);
		//$("#progress").css({'width': length});
		$("#progress").animate({ width: length }, 200);
		$("#progress_percentage").text(Math.round(100/imageSum * numCompleted));
	}
	updateProgress();
	
	//Rotation + Timing Event
	/*
	rotateSwitch = function(){		
		play = setInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			$active = $('#question_paging a.active').next();
			if ( $active.length === 0) { //If paging reaches the end...
				$active = $('#question_paging a:first'); //go back to first
			}
			rotate(); //Trigger the paging and slider function
		}, 5000); //Timer speed in milliseconds (3 seconds)
	};*/
	
	//rotateSwitch(); //Run function on launch
	
	//On Click
	/*
	$("#question_paging a").click(function() {
		$active = $(this); //Activate the clicked paging
		rotate(); //Trigger rotation immediately
		updateProgress()
		return false; //Prevent browser jump to link anchor
	});*/
	
	// Next Button
	$("[id^=nextBtn]").click(function() {
		$active = $('#question_paging span.active');
		updateProgress();
		$active = $('#question_paging span.active').next();
		if($active.length === 0){ //If paging reaches the end...
			$active = $('#question_paging span.active');
		}
		rotate(); //Trigger rotation immediately
		return false; //Prevent browser jump to link anchor
	});
	
	// Next Button
	$("[id^=prevBtn]").click(function() {
		$active = $('#question_paging span.active');
		updateProgress();
		$active = $('#question_paging span.active').prev();
		if($active.length === 0){ //If paging reaches the end...
			$active = $('#question_paging span.active');
		}
		rotate(); //Trigger rotation immediately
		return false; //Prevent browser jump to link anchor
	});
	
	$("input[type='radio']").click(function(){
		$active = $('#question_paging span.active');
		updateProgress();
	});
});