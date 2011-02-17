<!--<script src="http://connect.facebook.net/en_US/all.js"></script>
<div id="fb-root"></div>
<script>
  FB.init({ appId:<?php echo $FB_APPID ?>, cookie:true, xfbml:true });
</script>-->
<div id='user-actions-container' class='frame rounded clear'>
	<div class='share-like'>
    	<fb:like show_faces="no" href="http://apps.facebook.com/quizroo/previewQuiz.php?<?php echo $quiz->quiz_id; ?>" width="450" height="80"></fb:like>
	</div>
	<div class='recommend-dialog'>
		<fb:serverFbml width="625px">
		  <script type="text/fbml">
		  <fb:fbml>
		   <fb:request-form
		    method='POST' 
		    action='<?php echo $VAR_URL ?>'
			invite=true
			type='Quiz'
			content='Try this quiz out. <?php echo htmlentities("<fb:req-choice url=\"http://apps.facebook.com/quizroo/previewQuiz.php?id=".$quiz->quiz_id."\" label=\"Try quiz\"") ?>' >
			<fb:multi-friend-selector cols=5 rows=3 
			actiontext="Recommend this quiz to your friends"
			bypass="cancel"
			max=20 />
			</fb:request-form>
		   </fb:fbml>
		   </script>
		</fb:serverFbml>
	</div>
</div>
