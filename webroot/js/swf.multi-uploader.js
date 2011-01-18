var isUploading = false;
$(function(){
	$('#swfupload-control').swfupload({
		upload_url: "../modules/"+uploadfile+".php?unikey="+unikey,
		file_post_name: 'uploadfile',
		file_size_limit : "5120",
		file_types : "*.jpg;*.png;*.gif",
		file_types_description : "Image files",
		flash_url : "swf/swfupload.swf",
		button_image_url : 'img/uploadBtn.png',
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
			var pathtofile='<a href="javascript:;" onclick="setAsMain(\''+file.name+'\', \''+unikey+'\')">Set as quiz image</a>';
			item.addClass('success').find('p.status').html('Upload Complete! | '+pathtofile);
		})
		.bind('uploadComplete', function(event, file){
			// upload has completed, try the next one in the queue
			$(this).swfupload('startUpload');
			// set as default main image
			if($('#quiz_picture').val() == ""){
				$('#quiz_picture').val(unikey+'_'+file.name);
				$('#quizImagePreview').html('<img name="quizImagePreview" src="../quiz_images/imgcrop.php?w=180&h=120&f='+unikey+'_'+file.name+'" width="180" height="120" title="You can change this image in the upload image section" />');
				$('#queuestatus').text('"'+file.name+'" will be used as the quiz image.');
			}
			// upload flag
			isUploading = false;
		})
});	
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
