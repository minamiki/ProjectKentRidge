<!-- display form for sharing quiz to friends when recommend quiz is clicked after taking a quiz
http://localhost/Quizroo/webroot/previewQuiz.php -->
<?php include("variables.php"); ?>

<div id='user-actions-container' class='clear'>
    <script>isSharing = true</script>
    <div class='share-like'>
        <!-- XFBML -->
        <fb:like href="http://quizroo.nus-hci.com/webroot/quiz.php?id=<?php echo $quiz->quiz_id; ?>" show_faces="false" width="450"></fb:like>
        <!-- iframe 
        <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fquizroo.nus-hci.com%2Fwebroot%2Fquiz.php%3Fid%3D<?php echo $quiz->quiz_id; ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe>-->
	</div>
	<div class='recommend-dialog'>
        <fb:serverFbml width="625px">
		  <script type="text/fbml">
		  <fb:fbml>
		   <fb:request-form
		    method='POST' 
		    action='<?php echo $VAR_URL ?>index.php'
			invite=true
			type='Quiz'
			content='Hey.. try out this quiz, "<?php echo htmlentities($quiz->quiz_name); ?>". <?php echo htmlentities("<fb:req-choice url=\"http://apps.facebook.com/quizroo/previewQuiz.php?id=".$quiz->quiz_id."\" label=\"Try quiz\"") ?>' >
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