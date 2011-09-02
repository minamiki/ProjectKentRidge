<?php
// get the list of achievements
$getAchievementsQuery = sprintf("SELECT name, description, image, timestamp FROM g_achievements_log, g_achievements WHERE fk_achievement_id = g_achievements.id AND fk_member_id = %s AND g_achievements.type != 3 ORDER BY timestamp DESC", $member->id);
$getAchievements = mysql_query($getAchievementsQuery, $quizroo) or die(mysql_error());
$row_getAchievements = mysql_fetch_assoc($getAchievements);
$totalRows_getAchievements = mysql_num_rows($getAchievements);

// get the list of achievements not yet achieved
$getOtherAchievementsQuery = sprintf("SELECT name, description, image, `type` FROM g_achievements WHERE id NOT IN (SELECT fk_achievement_id FROM g_achievements_log, g_achievements WHERE fk_achievement_id = g_achievements.id AND fk_member_id = %s) AND `type`=4", $member->id);
$getOtherAchievements = mysql_query($getOtherAchievementsQuery, $quizroo) or die(mysql_error());
$row_getOtherAchievements = mysql_fetch_assoc($getOtherAchievements);
$totalRows_getOtherAchievements = mysql_num_rows($getOtherAchievements);

?>
<!-- Display title and description -->
<div id="achievements-preamble" class="frame rounded">
  <h2>Achievements</h2>
  <p>Find out what achievements you have unlocked so far. For each achievement, you can also find out when and how you unlocked these achievements.</p>
  <p>You can also find out some of the achievements you have yet to unlock and how to unlock them.</p> 
</div>
<!-- Display side bar (fun facts) -->
<div class="clear">
  <div id="fun-facts" class="framePanel rounded left">
    <h2>Fun Facts</h2>
    <div class="content-container">
    <p class="fact">You've got</p>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
      <div class="factValue"><?php echo sprintf("%d", $member->getStats('achievements')); ?></div>
      <p class="factDesc">Achievements</p>
    </div>
    <div class="factbox rounded">
      <p class="unit">a total of</p>
      <div class="factValue"><?php echo sprintf("%d", $member->getStats('taker_points') + $member->getStats('creator_points')); ?></div>
      <p class="factDesc">Points</p>
    </div>
    <p class="fact">You are currently</p>
    <div class="factbox rounded">
      <p class="unit">at level</p>
      <div class="factValue"><?php echo sprintf("%d", $member->getStats('level')); ?></div>
      <p class="factDesc"></p>
    </div>
    </div>
  </div>
  
  <!-- Display achievements -->
  <div class="framePanel rounded right">
    <h2>Your Achievements</h2>
    <div class="content-container">
    <?php if($totalRows_getAchievements != 0){ do{ ?>
    <div class="achievement-box clear">
	    <div class="achievement-time"><span><?php echo date('d M Y @ H:m:s',strtotime($row_getAchievements['timestamp'])); ?></span></div>
    	<div class="achievement-image">
    		<img src="../webroot/img/<?php echo $row_getAchievements['image']; ?>" width="70" height="70" alt="<?php echo $row_getAchievements['name']; ?>" />
    	</div>
    	<div class="achievement-details">
    		<span class="achievement-name"><?php echo $row_getAchievements['name']; ?></span>
    		<span class="achievement-description"><?php echo $row_getAchievements['description']; ?></span>
    	</div>
    </div>
	<?php }while($row_getAchievements = mysql_fetch_assoc($getAchievements)); }else{ ?>
    <div id="none-box">You have no achievements yet!</div>
    <?php } ?>
    </div>
  </div>
  
    <!-- Display achievements not yet achieved-->
  <div class="framePanel rounded right stayright">
    <h2>Achievements To Unlock</h2>
    <div class="content-container">
    <?php if($totalRows_getOtherAchievements != 0){ do{ ?>
    <div class="achievement-box clear">
    	<div class="achievement-image">
    		<img src="../webroot/img/default-achievement-icon.png" width="70" height="70" alt="<?php echo $row_getOtherAchievements['name']; ?>" />
    	</div>
    	<div class="achievement-details">
    		<span class="achievement-name"><?php echo $row_getOtherAchievements['name']; ?></span>
    		<span class="achievement-description"><?php echo $row_getOtherAchievements['description']; ?></span>
    	</div>
    </div>
	<?php }while($row_getOtherAchievements = mysql_fetch_assoc($getOtherAchievements)); }else{ ?>
    <div id="none-box"><p>To find out more about how to get more achievements, <a href="http://www.twitter.com/quizroo" target="_blank"><img src="http://twitter-badges.s3.amazonaws.com/follow_us-b.png" alt="Follow Quizroo on Twitter" border="0" align="absmiddle"/></a></p></div>
    <?php } ?>
    </div>
  </div>
</div>