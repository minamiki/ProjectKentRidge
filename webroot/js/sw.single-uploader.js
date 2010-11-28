
function createUploadObject(formname){
	this.isUploading = false;
	$('#swfupload-control').swfupload({
		upload_url: uploadfile+".php?unikey="+unikey,
		file_post_name: 'uploadfile',
		file_size_limit : "5120",
		file_types : "*.jpg;*.png;*.gif",
		file_types_description : "Image files",
		file_upload_limit : 1,
		flash_url : "scripts/swfupload/swfupload.swf",
		button_image_url : 'scripts/swfupload/uploadBtn.png',
		button_width : 81,
		button_height : 33,
		button_placeholder : $('#uploader')[0],
		debug: false
	})
		.bind('fileQueued', function(event, file){
			var listitem='<li id="'+file.id+'" >'+
				'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
				'<div class="progressbar" ><div class="progress" ></div></div>'+
				'<p class="status" >Pending</p>'+
				'<span class="cancel">&nbsp;</span>'+
				'</li>';
			$('#log').append(listitem);
			$('li#'+file.id+' .cancel').bind('click', function(){
				var swfu = $.swfupload.getInstance('#swfupload-control');
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
			$('#log li#'+file.id).find('p.status').text('Uploading...');
			$('#log li#'+file.id).find('span.progressvalue').text('0%');
			$('#log li#'+file.id).find('span.cancel').hide();
			isUploading = true;
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
			//Show Progress
			var percentage=Math.round((bytesLoaded/file.size)*100);
			$('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
			$('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
			isUploading = true;
		})
		.bind('uploadSuccess', function(event, file, serverData){
			var item=$('#log li#'+file.id);
			item.find('div.progress').css('width', '100%');
			item.find('span.progressvalue').text('100%');
			//var pathtofile='<a href="javascript:;" onclick="setAsMain(\''+file.name+'\')">Set as main image</a>';
			item.addClass('success').find('p.status').html('Upload Complete!');
		})
		.bind('uploadComplete', function(event, file){
			// upload has completed, try the next one in the queue
			$(this).swfupload('startUpload');
			// upload flag
			$('#imageKey').val('<?php echo $unikey ?>');
			isUploading = false;
		})
}
function successSubmit(value)
{
	// check if uploads are complete
	
	// get array of formnames
	var uploadArray = $("#uploadArray").val();
	var uploads = uploadArray.split(" ");
	
	var isUploading = false;
	
	// loop through all uploads
	for(i = 0; i < isUploading.length; i++){
		if(uploads[i].isUploading == true){
			isUploading = true;
		}
	}
	
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
