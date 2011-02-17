// track whether widget is uploading
var isUploading = new Array();
//var widgetCount = 0;

function scanInitUploader(){
	// find out the number of uploading widgets
	$('.swfupload-control').each(function(i){
		initUploader(i);
		selectImage(i, $('#result_picture_'+i).val());
	});
}

function initUploader(targetField){
	$('#swfupload-control-'+targetField).swfupload({
		upload_url: "../modules/uploadQuiz.php?unikey="+unikey,
		file_post_name: 'uploadfile',
		file_size_limit : "2048",
		file_types : "*.jpg;*.png;*.gif",
		file_types_description : "Image files",
		flash_url : "swf/swfupload.swf",
		button_image_url : 'img/uploadBtn.png',
		button_width : 81,
		button_height : 33,
		button_placeholder : $('#uploader-'+targetField)[0],
		debug: false
	})
	.bind('fileQueued', function(event, file){
		var targetWidget = file.id.split("_")[1];
		var listitem='<li id="'+file.id+'" >'+
			'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
			'<div class="progressbar" ><div class="progress" ></div></div>'+
			'<p class="status" >Pending</p>'+
			'<span class="cancel">&nbsp;</span>'+
			'</li>';
		$('#log-'+targetWidget).append(listitem);
		$('li#'+file.id+' .cancel').bind('click', function(){
			var swfu = $.swfupload.getInstance('#swfupload-control-'+targetWidget);
			swfu.cancelUpload(file.id);
			$('li#'+file.id).slideUp('slow');
		});
		// start the upload since it's queued
		$(this).swfupload('startUpload');
	})
	.bind('fileQueueError', function(event, file, errorCode, message){
		alert('Size of the file '+file.name+' is greater than limit');
	})
	.bind('uploadStart', function(event, file){
		var targetWidget = file.id.split("_")[1];
		$('#log-'+targetWidget+' li#'+file.id).find('p.status').text('Uploading...');
		$('#log-'+targetWidget+' li#'+file.id).find('span.progressvalue').text('0%');
		$('#log-'+targetWidget+' li#'+file.id).find('span.cancel').hide();
		isUploading[targetWidget] = true;
	})
	.bind('uploadProgress', function(event, file, bytesLoaded){
		//Show Progress
		var targetWidget = file.id.split("_")[1];
		var percentage=Math.round((bytesLoaded/file.size)*100);
		$('#log-'+targetWidget+' li#'+file.id).find('div.progress').css('width', percentage+'%');
		$('#log-'+targetWidget+' li#'+file.id).find('span.progressvalue').text(percentage+'%');
		isUploading[targetWidget] = true;
	})
	.bind('uploadSuccess', function(event, file, serverData){
		var targetWidget = file.id.split("_")[1];
		var item=$('#log-'+targetWidget+' li#'+file.id);
		item.find('div.progress').css('width', '100%');
		item.find('span.progressvalue').text('100%');
		//var pathtofile='<a href="javascript:;" onclick="setAsMain(\''+file.name+'\', \''+unikey+'\')">Set as quiz image</a>';
		item.addClass('success').find('p.status').html('Upload Complete!');
	})
	.bind('uploadComplete', function(event, file){
		var targetWidget = file.id.split("_")[1];
		// upload has completed, try the next one in the queue
		$(this).swfupload('startUpload');
		// set as default main image
		//if($('#'+targetField).val() == ""){
			$('#result_picture_'+targetField).val(unikey+'_'+file.name);
			updateWidgets();
			$('#quizImagePreview-'+targetWidget).html('<img name="quizImagePreview-'+targetWidget+'" src="../quiz_images/imgcrop.php?w=180&h=120&f='+unikey+'_'+file.name+'" width="180" height="120" title="You can change this image in the upload image section" />');
			// update the result image
			$('#queuestatus-'+targetWidget).text('"'+file.name+'" will be used as the quiz image.');
			$('#selected-image-'+targetWidget).html('<img src="../quiz_images/imgcrop.php?w=100&amp;h=75&amp;f='+unikey+'_'+file.name+'" alt="" width="100" height="75" />');
		//}
		$('li#'+file.id).slideUp('slow');
		updateWidgets();
		// upload flag
		isUploading[targetWidget] = false;
	})
	//widgetCount++;
}

function updateWidgets(){
	if($("#resultCount").val() != undefined){
		resultCount = $("#resultCount").val()+1;
		for(i = 0; i < resultCount; i++){
			$.ajax({
				type: "GET",
				url: "../modules/createQuizImagePool.php",
				data: "resultNumber="+i+"&unikey="+unikey,
				async: false,
				success: function(data) {
					$("#pictureChoser_"+i).html(data);
				}
			});	
		}
	}else{
		$.ajax({
			type: "GET",
			url: "../modules/createQuizImagePool.php",
			data: "resultNumber=0&unikey="+unikey,
			async: false,
			success: function(data) {
				$("#pictureChoser_0").html(data);
			}
		});	
	}
}

function setAsMain(filename, unikey){
	$('#quiz_picture').val(unikey+"_"+filename);
	$('#quizImagePreview').html('<img name="quizImagePreview" src="../quiz_images/imgcrop.php?w=180&h=120&f='+unikey+'_'+filename+'" width="180" height="120" title="You can change this image in the upload image section" />');
	$('#queuestatus').text('"'+filename+'" will be used as the quiz image.');
}	
function successSubmit(value)
{
	// check if upload complete
	if(value && !isUploading){
		$("#postStatus").css({'display' : 'block'});
		$('#nextBtn').attr("disabled", "disabled");
		return true;
	}else{
		if(isUploading){
			alert("Photo uploads still in progress! Please wait for uploads to complete!");
		}
		return false;
	}
}

function selectImage(result, filename){
	$("#showResultImage_"+result).text("Image '"+filename+"' selected");
	$("#result_picture_"+result).val(filename);
	imagefilename = filename.substring(9);
	if(imagefilename != ""){
		$('#queuestatus-'+result).text('"'+imagefilename+'" will be used as the quiz image.');
	}else{
		$('#queuestatus-'+result).text('No image was chosen for this result.');
	}	
	$('#selected-image-'+result).html('<img src="../quiz_images/imgcrop.php?w=100&amp;h=75&amp;f='+filename+'" alt="" width="100" height="75" />');
}

function checkIfUploading(){
	if($.inArray(true, isUploading) == -1){
		return false;
	}else{
		return true;
	}
}
