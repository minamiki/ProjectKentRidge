// Quiz Slider

$(document).ready(function() {
	// question class
	function Quiz(q, o, t){
		this.question = q;
		this.option = o;
		this.time = t;
	}
	
	var logtime = new Array();
	var logCount = 1;
	
	var quizTime = new Date();
	var startTime = quizTime.getTime();	// get the quiz start time;
	var serverTime = $("#logtime").val();
	logtime[0] = new Array(0, serverTime, startTime);
	// number of questions completed
	var numCompleted = 0;
	
	// set the margin for the indicators
	var numQuestions = $("#question_paging a").length
	var margin = (690 - (numQuestions * 16)) / numQuestions;
	
	// also store the number of questions for error checking later
	var questionArray = new Array(numQuestions);
	
	// fill it with zeros
    for(var i = 0; i < questionArray.length; i++){
        questionArray[i] = 0;
    }
	
	$("#question_paging a").css('marginRight', Math.floor(margin));
	
	//Set Default State of each portfolio piece
	$("#question_paging").show();
	$("#question_paging a:first").addClass("active");
	
	//Get size of images, how many there are, then determin the size of the image reel.
	var imageWidth = $("#questionContainer").width();
	var imageSum = $("#question_reel div").size();
	var imageReelWidth = imageWidth * imageSum;
	
	//Adjust the image reel to its new size
	$("#question_reel").css({'width' : imageReelWidth});
	
	//Paging + Slider Function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Get number of times to slide
		var image_reelPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		$("#question_paging a").removeClass('active'); //Remove all active class
		$active.addClass('active'); //Add active class (the $active is declared in the rotateSwitch function)
		
		//Slider Animation
		$("#question_reel").animate({ 
			left: -image_reelPosition
		}, 500 );
	
	}; 
	
	// update the progress of the quiz
	function updateProgress(){
		if(typeof($active) != 'undefined'){
			// check if question is answered
			if($("#takeQuiz input[name='q"+$active.attr("rel")+"']:checked").val() != undefined){
				if(!$active.hasClass('completed')){
					$active.addClass('completed');
					questionArray[$active.attr("rel")-1] = 1;
					numCompleted++;
				}
			}
		}
		
		// update the state of the submit button
		if(submitCheck()){
			$("#finishQuiz").removeClass("btnDisabled");
			$("#incomplete").slideUp("fast");
			$("#final-bulb").addClass('activeBulb');
		}else{
			$("#finishQuiz").addClass("btnDisabled");
		}
			
		/* remove progress bar due to HCI issues
		var length = (710/imageSum * numCompleted);
		//$("#progress").css({'width': length});
		$("#progress").animate({ width: length }, 200);
		$("#progress_percentage").text(Math.round(100/imageSum * numCompleted));
		*/
	}
	updateProgress();
	
	// check if the quiz is ready for submission
	function submitCheck(){
		var status = true;
		// check if there are unanswered questions
		for(var i = 0; i < questionArray.length; i++){
			if(questionArray[i] == 0){
				status = false;
			}
		}
		return status;
	}
	
	// Next Button
	$("[id^=nextBtn]").click(function() {
		//console.log("Going to next slide");
		$active = $('#question_paging a.active');
		updateProgress();
		$active = $('#question_paging a.active').next();
		if($active.length === 0){ //If paging reaches the end...
			$active = $('#question_paging a.active');
		}
		rotate(); //Trigger rotation immediately
		return false; //Prevent browser jump to link anchor
	});
	
	// Previous Button
	$("[id^=prevBtn]").click(function() {
		//console.log("Going to next slide");
		$active = $('#question_paging a.active');
		updateProgress();
		$active = $('#question_paging a.active').prev();
		if($active.length === 0){ //If paging reaches the end...
			$active = $('#question_paging a.active');
		}
		rotate(); //Trigger rotation immediately
		return false; //Prevent browser jump to link anchor
	});
	
	//On Click
	$("#question_paging a").click(function() {
		//console.log("Jumping to slide");
		$active = $(this);
		updateProgress();
		rotate(); //Trigger rotation immediately
		return false; //Prevent browser jump to link anchor
	});
	
	// On option choosen
	$("input[type='radio']").click(function(){
		$active = $('#question_paging a.active');
		updateProgress();
		var questionNum = $(this).attr("name").substr(1);
		var optionValue = $(this).val();
		quizTime = new Date();
		logtime[logCount++] = new Array(questionNum, optionValue, quizTime.getTime());
	});
	
	// On form submit
	$("#takeQuiz").submit(function(){
		$("#logtime").val(logtime.toString());
		// update the progress on last time (for visual purposes)
		updateProgress();
		// check if all questions are answered
		if(submitCheck()){
			$("#question_paging a").removeClass('active'); //Remove all active class
			$("#final-bulb").addClass('activeBulb');
			return true;
		}else{
			$("#incomplete").slideDown("fast");
			return false;
		}
	});
});