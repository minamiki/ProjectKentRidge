<?php include("variables.php"); ?>
<div id='user-actions-container' class='frame rounded clear'>
	<div class='share-like'>
        <fb:like href="http://quizroo.nus-hci.com/webroot/quiz.php?id=<?php echo $quiz->quiz_id; ?>" show_faces="false" width="450"></fb:like>
	</div>
    <script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<script type="text/javascript">
        /*
         * Subscribe to Facebook Like event to handle it for our own data. 
         */
        FB.Event.subscribe('edge.create', function(response) {
            Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'type':1});
        });
        
        /*
         * Subscribe to Facebook Unlike event to handle it for our own data. 
         */
        FB.Event.subscribe('edge.remove', function(response) {
            Share.rate($('#user-actions-container'),{'quiz_id': <?php echo $quiz->quiz_id ?>,'type':-1});
        });
	</script>
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