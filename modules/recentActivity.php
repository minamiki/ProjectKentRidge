<?php require('../modules/quizrooDB.php'); ?>
<?php require('../modules/variables.php'); ?>
<?php
// Number of activities per sub-type
$NUM_RECENT_SAMPLES = 10;
// Number of activities to display, with a maximum of all samples
$NUM_RECENT_DISPLAY = 20;
// Minimum number of activities to show when hidden
$NUM_RECENT_SHOW = 3;


/*
 * Types of activities to retrieve
 * Type 0 : Quizzes
 * - Recent quizzes that user took
 * - Recent quizzes that user's friends took
 * Type 1 : Achievements
 * - Recent achievements user received
 * - Recent achievements user's friends received
 */
$recent_activities = array();
$facebookID = $member->id;

/*
 * Get results of quizzes user took
 */
$query_user_results = sprintf("SELECT quiz_id, quiz_name, result_title, timestamp FROM q_store_result,q_quizzes,q_results WHERE q_store_result.fk_quiz_id=q_quizzes.quiz_id AND fk_result_id=result_id AND q_store_result.fk_member_id = %s ORDER BY q_store_result.timestamp DESC LIMIT %d", $facebookID, $NUM_RECENT_SAMPLES);
$user_results = mysql_query($query_user_results, $quizroo) or die(mysql_error());
$row_user_results = mysql_fetch_assoc($user_results);
$totalRows_user_results = mysql_num_rows($user_results);

/*
 * Add results of users to recent activities array
 */
if($totalRows_user_results != 0) { do {
	$timestamp = strtotime($row_user_results['timestamp']);
	$recent_activities[] = array('information'=>$row_user_results,'timestamp'=>(int) date('U',$timestamp),'type'=>0);
}while (($row_user_results = mysql_fetch_assoc($user_results)));	
}

/*
 * Get all results of quizzes taken within a 1 week interval (to limit results returned)
 */
$query_all_results = sprintf("SELECT quiz_id, quiz_name, result_title, member_id, member_name, timestamp FROM q_store_result,q_quizzes,q_results,s_members WHERE q_store_result.fk_quiz_id=q_quizzes.quiz_id AND fk_result_id=result_id AND q_store_result.fk_member_id = member_id AND timestamp > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY q_store_result.timestamp DESC");
$all_results = mysql_query($query_all_results, $quizroo) or die(mysql_error());
$row_all_results = mysql_fetch_assoc($all_results);
$totalRows_all_results = mysql_num_rows($all_results);

/*
 * Check if results belong to user's friends.
 * If it does, add to recent activities array
 */
$friends_results = 0;
$row_count = 0;

if($totalRows_all_results != 0) { do {
	if($member->isFriend($row_all_results['member_id'])){
		$timestamp = strtotime($row_all_results['timestamp']);
		$recent_activities[] = array('information'=>$row_all_results,'timestamp'=>(int) date('U',$timestamp),'type'=>0);
		$friends_results++;
	}
}while ($friends_results < $NUM_RECENT_SAMPLES && ($row_all_results = mysql_fetch_assoc($all_results)));	
}

/*
 * Get achievements user received
 */
$query_user_achievements = sprintf("SELECT name, description, type, timestamp FROM g_achievements_log, g_achievements WHERE g_achievements_log.fk_achievement_id=g_achievements.id AND fk_member_id = %s ORDER BY g_achievements_log.timestamp DESC LIMIT %d", $facebookID, $NUM_RECENT_SAMPLES);
$user_achievements = mysql_query($query_user_achievements, $quizroo) or die(mysql_error());
$row_user_achievements = mysql_fetch_assoc($user_achievements);
$totalRows_user_achievements = mysql_num_rows($user_achievements);

/*
 * Add achievements of users to recent activities array
 */
if($totalRows_user_achievements != 0) { do {
	$timestamp = strtotime($row_user_achievements['timestamp']);
	$recent_activities[] = array('information'=>$row_user_achievements,'timestamp'=>(int) date('U',$timestamp),'type'=>1);
} while (($row_user_achievements = mysql_fetch_assoc($user_achievements)));	
}

/*
 * Get all achievements within a 1 week interval (to limit results returned)
 */
$query_all_achievements = sprintf("SELECT name, description, member_id, member_name, timestamp FROM g_achievements_log, g_achievements,s_members WHERE g_achievements_log.fk_achievement_id=g_achievements.id AND type<>3 AND g_achievements_log.fk_member_id = member_id AND timestamp > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY g_achievements_log.timestamp DESC");
$all_achievements = mysql_query($query_all_achievements, $quizroo) or die(mysql_error());
$row_all_achievements = mysql_fetch_assoc($all_achievements);
$totalRows_all_achievements = mysql_num_rows($all_achievements);

/*
 * Check if achievement belong to user's friends.
 * If it does, add to recent activities array
 */
$friends_achievements = 0;
$row_count = 0;

if($totalRows_all_achievements != 0) { do {
	if($member->isFriend($row_all_achievements['member_id'])){
		$timestamp = strtotime($row_all_achievements['timestamp']);
		$recent_activities[] = array('information'=>$row_all_achievements,'timestamp'=>(int) date('U',$timestamp),'type'=>1);
		$friends_achievements++;
	}
}while ($friends_achievements < $NUM_RECENT_SAMPLES && ($row_all_achievements = mysql_fetch_assoc($all_achievements)));	
}

/*
 * Sort the recent activities 
 */
foreach($recent_activities as $key => $row) {
	$time[$key] = $row['timestamp'];
}
array_multisort($time, SORT_DESC, $recent_activities);

$recent_activities = array_slice($recent_activities,0,$NUM_RECENT_DISPLAY);
?>

<div id="recent" class="framePanel rounded"> <a href="javascript:;" id="recent-toggle">More</a>
	<h2>Recent Activity</h2>
	<div class="content-container">
	<?php 
	$activity_count = 0;
	if(count($recent_activities)!=0){
	foreach ($recent_activities as $activity) { 
		if($activity_count==$NUM_RECENT_SHOW){	?>
			<div id="recent-extended">	
	<?php }
		$activity_count++;
		
		if($activity['type']==0){	
			if($activity['information']['member_id']){
			?>			
			<div class="recent-feed"><span class="topic quiz">Quiz</span><span class="event"><a href="viewMember.php?id=<?php echo $activity['information']['member_id']; ?>"><?php echo $activity['information']['member_name'] ?></a> got <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['result_title'] ?></a> in <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['quiz_name'] ?></a>.</span>
				<div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
			</div>		
			<?php }else{?>
			<div class="recent-feed"><span class="topic quiz">Quiz</span><span class="event">You got <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['result_title'] ?></a> in the quiz, <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['quiz_name'] ?></a>.</span>
				<div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
			</div>
			<?php } ?>
		<?php }else if($activity['type']==1){
			if($activity['information']['member_id']){ ?>
				<div class="recent-feed"><span class="topic achievement">Achievement</span><span class="event"><a href="viewMember.php?id=<?php echo $activity['information']['member_id']; ?>"><?php echo $activity['information']['member_name'] ?></a> unlocked the <a href="achievements.php"><?php echo $activity['information']['name'] ?></a> achievement.</span>
					<div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
				</div>
			<?php }else{?>
				<?php if($activity['information']['type']==3){ ?>
					<div class="recent-feed"><span class="topic achievement">Level</span><span class="event"><?php echo $activity['information']['description'] ?>.</span>
						<div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
					</div>
				<?php } else {?>
					<div class="recent-feed"><span class="topic achievement">Achievement</span><span class="event">You unlocked the <a href="achievements.php"><?php echo $activity['information']['name'] ?></a> achievement.</span>
						<div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
					</div>
				<?php }?>
			<?php } ?>
		<?php }else if($activity['type']==2){?>
		
		<?php }else if($activity['type']==3){?>	
		<?php }?>
	<?php }
	}else{ ?>
		<script>
			$('#recent').hide();
		</script>
	<?php }?>
	</div>
	</div>
</div>
<!--<div id="recent" class="frame rounded"> <a href="javascript:;" id="recent-toggle">More</a>-->
<!--	<h2>Recent Activity</h2>-->
<!--    <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>-->
<!--    <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>-->
<!--    <div class="recent-feed"><span class="topic friend">Friend</span><a href="javascript:;">Chris Chua</a> has added you as a friend!</div>-->
<!--    <div id="recent-extended">-->
<!--      <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>-->
<!--      <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>-->
<!--      <div class="recent-feed"><span class="topic friend">Friend</span><a href="javascript:;">Chris Chua</a> has added you as a friend!</div>-->
<!--      <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>-->
<!--      <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>-->
<!--    </div>-->
<!--</div>-->