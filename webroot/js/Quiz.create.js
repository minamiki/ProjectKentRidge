// Create Quiz

// Quiz Validation Class
var QuizValidate = {
	// storage counters
	sprytextfield: new Array(),
	sprytextarea: new Array(),
	
	// init all validation objects
	init: function(){		
		// we scan through the page and identify all validation widgets
		textfield = this.sprytextfield;
		textarea = this.sprytextarea;
		$(".sprytextfield").each(function(i){ // textfields
			textfield[i] = new Spry.Widget.ValidationTextField($(this).attr('id'), "none", {validateOn:["change"]});
		});
		$(".sprytextarea").each(function(i){ // textareas
			textarea[i] = new Spry.Widget.ValidationTextarea($(this).attr('id'), {validateOn:["change"]});
			
		});
	},
	
	add: function(type, field_id){
		id = this.sprytextfield.length+1;
		if(type == "textfield"){
			this.sprytextfield[id] = new Spry.Widget.ValidationTextField($("#sprytextfield-"+field_id).attr('id'), "none", {validateOn:["change"]});
		}else{
			this.sprytextarea[id] = new Spry.Widget.ValidationTextarea($("#sprytextarea-"+field_id).attr('id'), {validateOn:["change"]});
		}
		return true;
	},
		
	remove: function(type, id){
		if(type == "textfield"){
			remover = new Spry.Widget.Utils.destroyWidgets("sprytextfield-"+id);
			return true;
		}else{
			remover = new Spry.Widget.Utils.destroyWidgets("sprytextarea-"+id);
			return true;
		}
	}
}

// Quiz Information class
var QuizInfo = {
	id: 0,
	key: '',
	// store the id
	init: function(id, key){
		this.id = id;
		this.key = key;
	}		
}

// Quiz Result class
var QuizResult = {
	resultCount: 0,
	
	init: function(){
		// populate the quiz
		$.ajax({
			type: "GET",
			url: "../modules/createResultObject.php?load",
			data: "resultNumber="+this.resultCount+"&unikey="+QuizInfo.key+"&id="+QuizInfo.id,
			async: false,
			success: function(data) {
				$("#createResultContainer").append(data);
			}
		});
		// count the number of results
		numResult = 0;
		$(".resultWidget").each(function(i){
			numResult++;
			QuizValidate.add("textfield", "result_title_"+i);	// result title
			QuizValidate.add("textarea", "result_description_"+i);	// result description
		});
		this.resultCount = numResult;
		// update the count
		this.updateCount();
		// init the images
		scanInitUploader();
		
	},
	
	add: function(){
		// add the result widget
		$.ajax({
			type: "GET",
			url: "../modules/createResultObject.php",
			data: "resultNumber="+this.resultCount+"&unikey="+QuizInfo.key,
			async: false,
			success: function(data) {
				$("#createResultContainer").append(data);
			}
		});
		QuizValidate.add("textfield", "result_title_"+this.resultCount);	// result title
		QuizValidate.add("textarea", "result_description_"+this.resultCount);	// result description
		// init the picture uploader
		initUploader(this.resultCount);

		// return the value and increment
		this.resultCount++;
		// update the count
		this.updateCount();
		return this.resultCount;
	},
	
	remove: function(id){
		// unregister the validators
		QuizValidate.remove("textfield", "result_title_"+id);		// remove title
		QuizValidate.remove("textarea", "result_description_"+id);	// remove description
		// is it already in the database ?
		if($("#ur"+id).val() != undefined){
			// remove from database
			$.ajax({
				type: "GET",
				url: "../modules/createResultObject.php?delete",
				data: "result="+$("#ur"+id).val()+"&id="+QuizInfo.id,
				async: false,
				success: function(data) {
					if(data != ""){
						alert(data);
					}
				}
			});
		}
		// just remove the question widget
		$("#r"+id).remove();
		//this.resultCount--;
		// update the count
		this.updateCount();
		return this.resultCount
	},
	
	updateCount: function(){
		$("#resultCount").val(this.resultCount);
	}
}

var QuizQuestion = {
	question: new Array(),
	
	init: function(){
		// populate the questions
		$.ajax({
			type: "GET",
			url: "../modules/createQuestionObject.php?load",
			data: "questionNumber="+this.numQuestions()+"&id="+QuizInfo.id,
			async: false,
			success: function(data) {
				$("#createQuestionContainer").append(data);
			}
		});		
		thisQuestion = this.question;
		// count the number of questions
		$(".questionWidget").each(function(i){
			thisQuestion[i] = new Array();
			QuizValidate.add("textfield", 'q'+i); // question
			// find out the number of options in a question
			$(".optionWidget-"+i).each(function(j){
				thisQuestion[i][j] = 'q'+i+'o'+j;
				QuizValidate.add("textfield", 'q'+i+'o'+j); // option
			});
		});		
		this.updateCount();
		this.getOptionValues();
	},
	
	add: function(){
		// add the question widget
		$.ajax({
			type: "GET",
			url: "../modules/createQuestionObject.php",
			data: "questionNumber="+this.numQuestions()+"&id="+QuizInfo.id,
			async: false,
			success: function(data) {
				$("#createQuestionContainer").append(data);
			}
		});
		// init the validators
		QuizValidate.add("textfield", 'q'+this.question.length); // question
		QuizValidate.add("textfield", 'q'+this.question.length+'o0'); // option 1
		QuizValidate.add("textfield", 'q'+this.question.length+'o1'); // option 2
		// update the array counts		
		this.question[this.question.length] = new Array();
		this.question[this.question.length-1][0] = 'q'+this.question.length+'o0';
		this.question[this.question.length-1][1] = 'q'+this.question.length+'o1';
		// update the page counts
		this.updateCount();
		this.getOptionValues();
		return this.question.length;
	},
	
	addOption: function(question_id){
		nextOption = this.question[question_id].length;
		// add an option widget
		$.ajax({
			type: "GET",
			url: "../modules/createOptionObject.php",
			data: "questionNumber="+question_id+"&optionNumber="+nextOption+"&id="+QuizInfo.id,
			async: false,
			success: function(data) {
				$("#optionContainer_"+question_id).append(data);
			}
		});
		// add an option
		this.question[question_id][nextOption] = 'q'+question_id+'o'+nextOption;
		QuizValidate.add("textfield", 'q'+question_id+'o'+nextOption); // one option
		return this.question[question_id].length;
	},
	
	remove: function(id){
		// find and remove the options in it
		for(i=0; i < this.question[id]; i++){
			this.removeOption(id, this.question[id][i]);
		}
		// unregister the validators
		QuizValidate.remove("textfield", "q"+id);
		// is it already in the database ?
		if($("#uq"+id).val() != undefined){
			// remove from database
			$.ajax({
				type: "GET",
				url: "../modules/createQuestionObject.php?delete",
				data: "question="+$("#uq"+id).val()+"&id="+QuizInfo.id,
				async: false,
				success: function(data) {
					if(data != ""){
						alert(data);
					}
				}
			});
		}
		// remove the question widget
		$("#q"+id).remove();
		delete this.question[id];
		this.updateCount();
		this.getOptionValues();
		
		return true;
	},
	
	removeOption: function(question, option){
		// unregister the validators
		QuizValidate.remove("textfield", 'q'+question+'o'+option);
		// is it already in the database ?
		if($('#uq'+question+'o'+option).val() != undefined){
			// remove from database
			$.ajax({
				type: "GET",
				url: "../modules/createOptionObject.php?delete",
				data: "option="+$('#uq'+question+'o'+option).val()+"&id="+QuizInfo.id,
				async: false,
				success: function(data) {
					if(data != ""){
						alert(data);
					}
				}
			});
		}
		// remove the option widget
		$('#cq'+question+'o'+option).remove();
		delete this.question[question][option];
		return true;
	},
	
	numQuestions: function(){
		return this.question.length;
	},
	
	getOptionValues: function(){
		var textString = "";

		for(i = 0; i < this.question.length; i++){
			if(this.question[i] != undefined){
				textString += this.question[i].length + '_';
			}else{
				textString += 0 + '_';
			}
		}
		returnVal = textString.substr(0, textString.length-1);
		$("#optionCounts").val(returnVal);
		return returnVal;
	},

	updateCount: function(){
		$("#questionCount").val(this.question.length);
	}
}

function submitCheck(value){
	// check if upload complete
	if(value && !checkIfUploading()){
		$('#submitBtn').attr("disabled", "disabled");
		$('#resultCount').val(QuizResult.resultCount);
		$('#questionCount').val(QuizQuestion.numQuestions());
		$("#optionCounts").val(QuizQuestion.getOptionValues());
		return true;
	}else{
		if(checkIfUploading()){
			alert("Photo uploads still in progress! Please wait for uploads to complete!");
		}
		if(!value){
			alert("Some of the required fields are empty! Please scroll up to check. Fields requiring attention will be highlighted red.");	
		}
		return false;
	}
}
