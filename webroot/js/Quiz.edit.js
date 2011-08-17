<!-- Creating/Editing Quiz - similar to Quiz.create but different. This is for editing of Quiz. -->
<!--     selectImage, addResult, updateResult, getResultText, getOptionValues, addQuestion, addOption, submitCheck, removeField-->


// variables
var uploadfile = "uploadQuiz";

// storage counters
var resultCount = 0;
var questionCount = 0;
var checkTextField = 0;
var checkTextArea = 0;

// storage arrays
var sprytextfield = new Array();
var sprytextarea = new Array();
var resultArray = new Array();
var questionArray = new Array();

// Object Classes
function questionClass(){
	this.numOptions = 2;
}
function resultClass(){
	this.result = "";
}

// functions
$(document).ready(function(){
	// set default checks
	sprytextfield[checkTextField] = new Spry.Widget.ValidationTextField("sprytextfield"+checkTextField, "none", {validateOn:["change"]});
	sprytextarea[checkTextArea] = new Spry.Widget.ValidationTextarea("sprytextarea"+checkTextArea, {validateOn:["change"]});
	checkTextField++;
	checkTextArea++;
});

function selectImage(result, filename, id){
	$("#showResultImage_"+result).text("Image '"+filename+"' selected");
	$("#result_picture_"+result).val(filename);
	$("#pictureChoser_"+result+" a img").removeClass("selectedBorder").addClass("selectImage");
	$("#"+id).removeClass("selectImage").addClass("selectedBorder");
}

function addResult(){
	$.ajax({
		type: "GET",
		url: "createResultObject.php",
		data: "resultNumber="+resultCount+"&unikey="+unikey+"&checkTextField="+checkTextField+"&checkTextArea="+checkTextArea,
		async: false,
		success: function(data) {
			$("#createResultContainer").append(data);
			resultArray[resultCount++] = new resultClass();
			sprytextfield[checkTextField] = new Spry.Widget.ValidationTextField("sprytextfield"+checkTextField, "none", {validateOn:["change"]});
			sprytextarea[checkTextArea] = new Spry.Widget.ValidationTextarea("sprytextarea"+checkTextArea, {validateOn:["change"]});
			checkTextField++;
			checkTextArea++;
		}
	});
}

function updateResult(result){
	resultArray[result-1].result = $("#result_title_"+result).val();
}

function getResultText(){
	var textString = "";
	for(i = 0; i < resultArray.length; i++){
		textString += resultArray[i].result + '_';
	}
	return textString.substr(0, textString.length-1);
}
function getOptionValues(){
	var textString = "";
	for(i = 0; i < questionArray.length; i++){
		textString += questionArray[i].numOptions + '_';
	}
	return textString.substr(0, textString.length-1);
}

function addQuestion(){
	if(resultCount < 1){
		alert("You need to have at least 1 result before you can create a Quiz question!");
	}else{
		$.ajax({
			type: "GET",
			url: "createQuestionObject.php",
			data: "questionNumber="+questionCount+"&checkTextField="+checkTextField+"&results="+escape(getResultText()),
			async: false,
			success: function(data) {
				$("#createQuestionContainer").append(data);
				questionArray[questionCount++] = new questionClass();
				for(i = 0; i < 3; i++){
					//A Spry Validation Text Field widget is a text field that displays valid or invalid states when the site visitor enters text. For example, you can add a Validation Text Field widget to a form in which visitors type their e-mail addresses. If they fail to type the @ sign and a period (.) in the e-mail address, the widget returns a message stating that the information the user entered is invalid.
					sprytextfield[checkTextField] = new Spry.Widget.ValidationTextField("sprytextfield"+checkTextField, "none", {validateOn:["change"]});
					checkTextField++;			
				}
			}
		});
	}
}

function addOption(question){
	$.ajax({
		type: "GET",
		url: "createOptionObject.php",
		data: "questionNumber="+question+"&checkTextField="+checkTextField+"&optionNumber="+questionArray[question-1].numOptions+"&results="+escape(getResultText()),
		async: false,
		success: function(data) {
			$("#optionContainer_"+question).append(data);
			questionArray[question-1].numOptions++;
			sprytextfield[checkTextField] = new Spry.Widget.ValidationTextField("sprytextfield"+checkTextField, "none", {validateOn:["change"]});
			checkTextField++;
		}
	});
}

function submitCheck(value){
	var checkFields = true;
	// check if min fields are met
	if(questionCount < 1){
		checkFields = false;
	}
	if(resultCount < 1){
		checkFields = false;
	}
	
	// check if upload complete
	if(value && !isUploading && checkFields){
		$('#submitBtn').attr("disabled", "disabled");
		$('#resultCount').val(resultCount);
		$('#questionCount').val(questionCount);
		$("#optionCounts").val(getOptionValues());
		return true;
	}else{
		if(isUploading){
			alert("Photo uploads still in progress! Please wait for uploads to complete!");
		}
		if(!checkFields){
			alert("You need to have a minimum of 1 result and 1 quiz question!");	
		}
		if(!value){
			alert("Some of the required fields are empty! Please scroll up to check. Fields requiring attention will be highlighted red.");	
		}
		return false;
	}
}

function removeField(removeID, type, num){
	$("#"+removeID).remove();
	
	if(type == 'o'){
		questionArray[num-1].numOptions--;
	}
	if(type == 'r'){
		resultCount--;
	}
	if(type == 'q'){
		questionCount--;
	}
	
}