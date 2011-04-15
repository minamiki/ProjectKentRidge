<?php
require('../modules/quizrooDB.php');
require('../modules/variables.php');
require('../modules/member.php');

$member = new Member();

// Number of activities to display, with a maximum of all samples
$NUM_RECENT_DISPLAY = 10;
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
$query_user_results = sprintf("SELECT quiz_id, quiz_name, result_title, timestamp FROM q_store_result,q_quizzes,q_results WHERE q_store_result.fk_quiz_id=q_quizzes.quiz_id AND fk_result_id=result_id AND q_store_result.fk_member_id = %s ORDER BY q_store_result.timestamp DESC", $facebookID);
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
 * Get achievements user received
 */
$query_user_achievements = sprintf("SELECT name, description, type, timestamp FROM g_achievements_log, g_achievements WHERE g_achievements_log.fk_achievement_id=g_achievements.id AND fk_member_id = %s ORDER BY g_achievements_log.timestamp DESC", $facebookID);
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


$friends = $member->getFriendsArray();
// If user has at least 1 friend
if(count($friends)!=0){
	//format list of friends for SQL e.g.: (friend_id1,friend_id2)
	$friends = "(".implode(",",$friends).")";
	
	/*
 	 * Get all results of quizzes of friends taken within a 1 week interval (to limit results returned)
 	 */
	$query_all_results = sprintf("SELECT quiz_id, quiz_name, result_title, member_id, member_name, timestamp FROM q_store_result,q_quizzes,q_results,s_members WHERE q_store_result.fk_quiz_id=q_quizzes.quiz_id AND fk_result_id=result_id AND q_store_result.fk_member_id = member_id AND q_store_result.fk_member_id IN %s ORDER BY q_store_result.timestamp DESC",$friends);
	$all_results = mysql_query($query_all_results, $quizroo) or die(mysql_error());
	$row_all_results = mysql_fetch_assoc($all_results);
	$totalRows_all_results = mysql_num_rows($all_results);
	
	/*
	 * Add all friends results to recent activities array
	 */
	$friends_results = 0;
	$row_count = 0;
	
	if($totalRows_all_results != 0) { do {
		$timestamp = strtotime($row_all_results['timestamp']);
		$recent_activities[] = array('information'=>$row_all_results,'timestamp'=>(int) date('U',$timestamp),'type'=>0);
		$friends_results++;
	}while ($row_all_results = mysql_fetch_assoc($all_results));	
	}

	/*
	 * Get all achievements within a 1 week interval (to limit results returned)
	 */
	$query_all_achievements = sprintf("SELECT name, description, member_id, member_name, timestamp FROM g_achievements_log, g_achievements,s_members WHERE g_achievements_log.fk_achievement_id=g_achievements.id AND type<>3 AND g_achievements_log.fk_member_id = member_id AND g_achievements_log.fk_member_id IN %s ORDER BY g_achievements_log.timestamp DESC",$friends);
	$all_achievements = mysql_query($query_all_achievements, $quizroo) or die(mysql_error());
	$row_all_achievements = mysql_fetch_assoc($all_achievements);
	$totalRows_all_achievements = mysql_num_rows($all_achievements);
	
	/*
	 * Add all friends achievements to recent activities array
	 */
	$friends_achievements = 0;
	$row_count = 0;
	
	if($totalRows_all_achievements != 0) { do {
		$timestamp = strtotime($row_all_achievements['timestamp']);
		$recent_activities[] = array('information'=>$row_all_achievements,'timestamp'=>(int) date('U',$timestamp),'type'=>1);
		$friends_achievements++;
	}while ($row_all_achievements = mysql_fetch_assoc($all_achievements));	
	}
}

/*
 * Sort the recent activities 
 */
foreach($recent_activities as $key => $row) {
	$time[$key] = $row['timestamp'];
}
array_multisort($time, SORT_DESC, $recent_activities);

/*
 * For Pagination
 */ 
// get the entry and page
if(isset($_GET['rp'])){
	$current_page = $_GET['rp'];
	$current_entry = $current_page * 10;
}else{
	$current_page = 0;
	$current_entry = 0;
}
// store the total keys
$recent_total = sizeof($recent_activities);
$recent_total_pages = ceil($recent_total / (float)$NUM_RECENT_DISPLAY);
// slice the array according to page
$recent_activities = array_slice($recent_activities, $current_entry, $NUM_RECENT_DISPLAY, true);
// store the array keys
$recent_keys = array_keys($recent_activities);
$first_entry = $recent_keys[0];
$last_entry = $recent_keys[$NUM_RECENT_DISPLAY-1];
?>
<!--Display the panels-->
<?php 
$activity_count = 0;
if(count($recent_activities)!=0){
foreach ($recent_activities as $activity) { 
    if($activity_count==$NUM_RECENT_SHOW){	?>
<div id="recent-extended" style="display:block">	
<?php }
    $activity_count++;
    
    if($activity['type']==0){	
        if($activity['information']['member_id']){
        ?>			
        <div class="recent-feed"><span class="topic quiz">Quiz</span><div class="event  quiz-event"><div class="text-flow"><a href="viewMember.php?id=<?php echo $activity['information']['member_id']; ?>"><?php echo $activity['information']['member_name'] ?></a> got <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['result_title'] ?></a> in <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['quiz_name'] ?></a>.</div><div class="text-fade"></div></div>
            <div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
        </div>		
        <?php }else{?>
        <div class="recent-feed"><span class="topic quiz">Quiz</span><div class="event  quiz-event"><div class="text-flow">You got <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['result_title'] ?></a> in the quiz, <a href="previewQuiz.php?id=<?php echo $activity['information']['quiz_id']; ?>"><?php echo $activity['information']['quiz_name'] ?></a>.</div><div class="text-fade"></div></div>
            <div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
        </div>
        <?php } ?>
    <?php }else if($activity['type']==1){
        if($activity['information']['member_id']){ ?>
            <div class="recent-feed"><span class="topic achievement">Achievement</span><div class="event achievement-event"><div class="text-flow"><a href="viewMember.php?id=<?php echo $activity['information']['member_id']; ?>"><?php echo $activity['information']['member_name'] ?></a> unlocked the <a href="achievements.php"><?php echo $activity['information']['name'] ?></a> achievement.</div><div class="text-fade"></div></div>
                <div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
            </div>
        <?php }else{?>
            <?php if($activity['information']['type']==3){ ?>
                <div class="recent-feed"><span class="topic achievement">Level</span><div class="event achievement-event"><div class="text-flow"><?php echo $activity['information']['description'] ?>.</div><div class="text-fade"></div></div>
                    <div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
                </div>
            <?php } else {?>
  <div class="recent-feed"><span class="topic achievement">Achievement</span><div class="event achievement-event"><div class="text-flow">You unlocked the <a href="achievements.php"><?php echo $activity['information']['name'] ?></a> achievement.</div><div class="text-fade"></div></div>
                    <div class="recent-bottom"><div class="timestamp"><?php echo date("j M @ H:m ",$activity['timestamp']) ?></div></div>
        </div>
    <?php }?>
  <?php } ?>
  <?php }else if($activity['type']==2){?>
  <!-- Type 2 activitiy stuff here -->
  <?php }else if($activity['type']==3){?>
  <!-- Type 3 activity stuff here -->
  <?php }?>
<?php } ?>
<div id="paging">
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <td><?php if ($first_entry > 0) { // Show if not first page ?>
        <a href="javascript:;" class="paging-goto" rel="0">First</a>
        <?php }else{ ?>
        <a href="javascript:;" class="disabled">First</a>
        <?php } // Show if not first page ?></td>
      <td><?php if ($first_entry > 0) { // Show if not first page ?>
        <a href="javascript:;" class="paging-goto" rel="<?php echo $current_page-1; ?>">Previous</a>
        <?php }else{ ?>
        <a href="javascript:;" class="disabled">Previous</a>
        <?php } // Show if not first page ?></td>
      <td>&nbsp;</td>
      <td><?php // calculate the page range
			if($recent_total_pages > 9){
				if($current_page > 4){
					if($current_page + 4 <= $recent_total_pages){
						$startpage = $current_page - 4;
						$maxpage = $current_page + 5;
					}else{
						$maxpage = $recent_total_pages;
						$startpage = $recent_total_pages - 9;
					}
				}else{
					$startpage = 0;
					$maxpage = 9;
				}
			}else{
				$startpage = 0;
				$maxpage = $recent_total_pages;
			}
			// display the pages
			for($i = $startpage; $i < $maxpage; $i++){ ?>
        <?php if($i != $current_page){ ?>
        <a href="javascript:;" rel="<?php echo $i; ?>" class="paging-goto"><?php echo $i+1; ?></a>
        <?php }else{ ?>
        <a href="javascript:;" class="disabled"><?php echo $i+1; ?></a>
        <?php }} ?></td>
      <td>&nbsp;</td>
      <td><?php if ($current_page < $recent_total_pages-1) { // Show if not last page ?>
        <a href="javascript:;" class="paging-goto" rel="<?php echo $current_page+1; ?>">Next</a>
        <?php }else{ ?>
        <a href="javascript:;" class="disabled">Next</a>
        <?php } // Show if not last page ?></td>
      <td><?php if ($current_page < $recent_total_pages-1) { // Show if not last page ?>
        <a href="javascript:;" class="paging-goto" rel="<?php echo $recent_total_pages; ?>">Last</a>
        <?php }else{ ?>
        <a href="javascript:;" class="disabled">Last</a>
        <?php } // Show if not last page ?></td>
    </tr>
  </table>
</div>
</div>
<?php } ?>