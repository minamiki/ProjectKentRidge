<?php include("variables.php"); ?>
<?php
// get the number of members
//$getMembersQuery = sprintf("SELECT COUNT(member_id) as count FROM s_members WHERE isActive=1");
//$getMembers = mysql_query($getMembersQuery, $quizroo) or die(mysql_error());
//$row_getAchievements = mysql_fetch_assoc($getMembers);
?>
<!-- Display title and description -->
<div class="frame rounded">
  <h2>Invite your friends</h2>
  <p>Share our app with your friends to get more people to take your quizzes. More people in Quizroo means more quizzes to take.</p> 
  <p>You can invite your friends through a wall post, or by sending them a request. You can also invite them to take a quiz through any of the quiz preview pages.</p>
</div>
<!-- Display side bar (fun facts) -->
<div class="clear">
  <div id="fun-facts" class="frame rounded left">
    <h2>Fun Facts</h2>
    <!--
    <p class="fact">We've got</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
      <div class="factValue"><?php echo sprintf("%d", $getMembers); ?></div>
      <p class="factDesc">Members</p>
    </div>
    -->
    <p class="fact">Quizroo</p>
    <div class="factbox rounded">
      <p class="unit">a product of</p>
      <div class="factValue">NUS-HCI</div>
      <p class="factDesc">Labs</p>
    </div>
  </div>
  
  <!-- Display sharing options -->
  <div class="frame rounded right">
  <h2>Share Quizroo via your wall</h2>
  <div id="share-quizroo-button" class="share-button">Post a message on your wall</div>
  </div>
  <div class="frame rounded right">
  <h2>Share Quizroo via requests</h2>
  <div id="request-quizroo-button" class="share-button">Send requests to your friends</div>
  </div>
</div>
<script type="text/javascript">
	/**
	 * Share quizroo - Publish to wall
	 */
	$('#share-quizroo-button').click(function(){
	FB.init({appId: <?php echo $FB_APPID ?>, status: true, cookie: true, xfbml: true});
	FB.ui(
	   {
		 method: 'feed',
		 name: ('I enjoyed Quizroo, try it out too!'),
		 link: ('<?php echo $FB_CANVAS ?>'),
		 picture: ('http://quizroo.nus-hci.com/webroot/img/quizroo-square-logo.png'),
		 caption: ('Quizroo'),
		 description: ('Quizroo is a platform for taking and creating fun and interesting quizzes. The platform also features other interesting activities that you can do while taking and creating quizzes. You can earn points to compete against other users or collect achievements and brag about it. Unlike other quiz applications, Quizroo is also a platform for researchers aimed at providing researchers with a space to conduct surveys and experiments and to collect useful data for research.'),
	   },
	   function(response) {
		 if (response && response.post_id) {
		   //alert('Post was published.');
		   $('#share-quizroo-button').html('Your post has been published').unbind('click');
		   $('#share-quizroo-button').css('background-color','#FFF').css('color','#000');
		 } else {
		   //alert('Post was not published.');
		 }
	   }
	);
	});

	/**
	 * Share quizroo - Send Request
	 */
    $('#request-quizroo-button').click(function(){
	     FB.init({appId: <?php echo $FB_APPID ?>, cookie:true, status:true, xfbml:true});
	     FB.ui(
			{
				method: 'apprequests',  
	       		message: 'I enjoyed Quizroo, try it out too!'
		    },
			function(response) {
			 if (response && response.post_id) {
			   //alert('Post was published.');
			   $('#request-quizroo-button').html('Your requests have been sent');
			   $('#request-quizroo-button').css('background-color','#FFF').css('color','#000');
			 } else {
			   //alert('Post was not published.');
			 }
		   }
	 	); 	
    });
</script>