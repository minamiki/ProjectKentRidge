<?php require('../modules/quizrooDB.php'); ?>
<?php require('../modules/variables.php');
// retrieve recommended quizzes
$query_recommendations = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, dislikes FROM q_quizzes, q_quiz_cat, s_members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 ORDER BY creation_date DESC LIMIT 0, %d", $VAR_NUM_LISTINGS);
$recommendations = mysql_query($query_recommendations, $quizroo) or die(mysql_error());
$row_recommendations = mysql_fetch_assoc($recommendations);
$totalRows_recommendations = mysql_num_rows($recommendations);

// retrieve popular quizzes
$query_popular = sprintf("SELECT * FROM (SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, dislikes FROM q_quizzes, q_quiz_cat, s_members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 ORDER BY quiz_score DESC LIMIT 0, %d) t ORDER BY RAND() LIMIT 0, %d", $VAR_NUM_POPULAR_POOL, $VAR_NUM_LISTINGS);
$popular = mysql_query($query_popular, $quizroo) or die(mysql_error());
$row_popular = mysql_fetch_assoc($popular);
$totalRows_popular = mysql_num_rows($popular);

// populate the topics panel
$query_getTopics = "SELECT * FROM q_quiz_cat";
$getTopics = mysql_query($query_getTopics, $quizroo) or die(mysql_error());
$row_getTopics = mysql_fetch_assoc($getTopics);
$totalRows_getTopics = mysql_num_rows($getTopics);
?>
<div id="dashboard-container">
  <?php if($VAR_SHOW_RECENT){ ?>
  <div id="recent" class="frame rounded"> <a href="javascript:;" id="recent-toggle">More</a>
    <h2>Recent Activity</h2>
    <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>
    <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>
    <div class="recent-feed"><span class="topic friend">Friend</span><a href="javascript:;">Chris Chua</a> has added you as a friend!</div>
    <div id="recent-extended">
      <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>
      <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>
      <div class="recent-feed"><span class="topic friend">Friend</span><a href="javascript:;">Chris Chua</a> has added you as a friend!</div>
      <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>
      <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>
    </div>
  </div>
  <?php } ?>
  <?php if($VAR_SYSTEM_MAINTENANCE){ ?>
  <div class="frame rounded">
  	<h2>Maintenance Mode is ON</h2>
    <p>Remember to turn it off after carrying out the required maintenance!</p>
  </div>
  <?php } ?>
  <?php if(isset($_GET['bar'])){ ?>
  <div id="topics-bar" class="rounded clear">
  	<ul>
    <?php do { ?>
    <li><a href="topics.php?topic=<?php echo $row_getTopics['cat_id']; ?>&bar" class="topicTitle"><?php echo $row_getTopics['cat_name']; ?></a></li>
  	<?php } while ($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
    </ul>
  </div>
  <?php } ?>
  <div class="clear">
    <div id="recommendations" class="frame rounded left-right">
      <h2>Latest</h2>
      <?php if($totalRows_recommendations != 0){ do { ?>
        <div class="quiz_box clear">
          <h3><a href="previewQuiz.php?id=<?php echo $row_recommendations['quiz_id']; ?>"><?php echo $row_recommendations['quiz_name']; ?></a></h3>
          <div class="thumb_box">
            <a href="previewQuiz.php?id=<?php echo $row_recommendations['quiz_id']; ?>"><img src="../quiz_images/imgcrop.php?w=90&amp;h=68&amp;f=<?php echo $row_recommendations['quiz_picture']; ?>" alt="<?php echo $row_recommendations['quiz_description']; ?>" width="90" height="68" border="0" title="<?php echo $row_recommendations['quiz_description']; ?>" /></a></div>
          <div class="quiz_details">
            <p class="description"><?php echo substr($row_recommendations['quiz_description'], 0, 120).((strlen($row_recommendations['quiz_description']) < 120)? "" : "..."); ?></p>
            <p class="source">from <a href="topics.php?topic=<?php echo $row_recommendations['fk_quiz_cat']; ?>"><?php echo $row_recommendations['cat_name']; ?></a>  by <a href="viewMember.php?id=<?php echo $row_recommendations['fk_member_id']; ?>"><?php echo $row_recommendations['member_name']; ?></a></p>
			<?php if(!$GAME_ALLOW_DISLIKES){ if($row_recommendations['likes'] > 0){ ?>
            <p class="rating"><span class="like"><?php echo $row_recommendations['likes']; ?></span> <?php echo ($row_recommendations['likes'] > 1) ? "people like" : "person likes"; ?> this</p>
			<?php }}else{ ?><p class="rating"><span class="like"><?php echo $row_recommendations['likes']; ?></span> likes, <span class="dislike"><?php echo $row_recommendations['dislikes']; ?></span> dislikes</p><?php } ?>
          </div>
        </div>
        <?php } while ($row_recommendations = mysql_fetch_assoc($recommendations)); }else{ ?>
        <p>There are no latest quizzes!</p>
        <?php } ?>
    </div>
    <div id="popular" class="frame rounded left-right clear">
      <h2>Popular</h2>
      <?php if($totalRows_popular !=0 ){ do { ?>
        <div class="quiz_box clear">
          <h3><a href="previewQuiz.php?id=<?php echo $row_popular['quiz_id']; ?>"><?php echo $row_popular['quiz_name']; ?></a></h3>
          <div class="thumb_box">
            <a href="previewQuiz.php?id=<?php echo $row_popular['quiz_id']; ?>"><img src="../quiz_images/imgcrop.php?w=90&amp;h=68&amp;f=<?php echo $row_popular['quiz_picture']; ?>" alt="<?php echo $row_popular['quiz_description']; ?>" width="90" height="68" border="0" title="<?php echo $row_popular['quiz_description']; ?>" /></a></div>
          <div class="quiz_details">
            <p class="description"><?php echo substr($row_popular['quiz_description'], 0, 120).((strlen($row_popular['quiz_description']) < 120)? "" : "..."); ?></p>
            <p class="source">from <a href="topics.php?topic=<?php echo $row_popular['fk_quiz_cat']; ?>"><?php echo $row_popular['cat_name']; ?></a> by <a href="viewMember.php?id=<?php echo $row_popular['fk_member_id']; ?>"><?php echo $row_popular['member_name']; ?></a>
			<?php if(!$GAME_ALLOW_DISLIKES){ if($row_popular['likes'] > 0){ ?>
            <p class="rating"><span class="like"><?php echo $row_popular['likes']; ?></span> <?php echo ($row_popular['likes'] > 1) ? "people like" : "person likes"; ?> this</p>
			<?php }}else{ ?><p class="rating"><span class="like"><?php echo $row_popular['likes']; ?></span> likes, <span class="dislike"><?php echo $row_popular['dislikes']; ?></span> dislikes</p><?php } ?>
          </div>
        </div>
        <?php } while ($row_popular = mysql_fetch_assoc($popular)); }else{ ?>
        <p>There are no popular quizzes for this topic!</p>
        <?php } ?>
    </div>
  </div>
  <?php if(!isset($_GET['bar'])){ ?>
  <div id="topics" class="frame rounded">
    <h2>Topics</h2>
    <p>Taken all the latest quizzes? You can also browse other quizzes by their topic!</p>
    <?php do { ?>
<a href="topics.php?topic=<?php echo $row_getTopics['cat_id']; ?>" class="topicTitle"><?php echo $row_getTopics['cat_name']; ?></a>
      <?php } while ($row_getTopics = mysql_fetch_assoc($getTopics)); ?>
  </div>
  <?php } ?>
  <div id="social" class="frame rounded">
    <h2>Social</h2>
    <p>Visit our <a href="http://www.facebook.com/apps/application.php?id=154849761223760" target="_blank">facebook page</a> for updates! Discuss what you like about Quizroo! <a href="http://www.twitter.com/quizroo" target="_blank"><img src="http://twitter-badges.s3.amazonaws.com/follow_us-b.png" alt="Follow minamiki on Twitter" border="0" align="absmiddle"/></a></p>
    <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fapps%2Fapplication.php%3Fid%3D154849761223760&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true"></iframe>
  </div>
</div>
<?php
mysql_free_result($recommendations);
mysql_free_result($popular);
mysql_free_result($getTopics);
?>
