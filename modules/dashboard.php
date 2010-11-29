<?php require_once('Connections/localhost.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$maxRows_recommendations = 4;
$pageNum_recommendations = 0;
if (isset($_GET['pageNum_recommendations'])) {
  $pageNum_recommendations = $_GET['pageNum_recommendations'];
}
$startRow_recommendations = $pageNum_recommendations * $maxRows_recommendations;

mysql_select_db($database_localhost, $localhost);
$query_recommendations = "SELECT quiz_id, quiz_name, quiz_description, quiz_picture, nickname, cat_name FROM q_quizzes, q_quiz_cat, members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat ORDER BY creation_date DESC";
$query_limit_recommendations = sprintf("%s LIMIT %d, %d", $query_recommendations, $startRow_recommendations, $maxRows_recommendations);
$recommendations = mysql_query($query_limit_recommendations, $localhost) or die(mysql_error());
$row_recommendations = mysql_fetch_assoc($recommendations);

if (isset($_GET['totalRows_recommendations'])) {
  $totalRows_recommendations = $_GET['totalRows_recommendations'];
} else {
  $all_recommendations = mysql_query($query_recommendations);
  $totalRows_recommendations = mysql_num_rows($all_recommendations);
}
$totalPages_recommendations = ceil($totalRows_recommendations/$maxRows_recommendations)-1;

$maxRows_popular = 4;
$pageNum_popular = 0;
if (isset($_GET['pageNum_popular'])) {
  $pageNum_popular = $_GET['pageNum_popular'];
}
$startRow_popular = $pageNum_popular * $maxRows_popular;

mysql_select_db($database_localhost, $localhost);
$query_popular = "SELECT quiz_id, quiz_name, quiz_description, quiz_picture, nickname, cat_name FROM q_quizzes, q_quiz_cat, members WHERE member_id = fk_member_id AND cat_id = fk_quiz_cat ORDER BY RAND()";
$query_limit_popular = sprintf("%s LIMIT %d, %d", $query_popular, $startRow_popular, $maxRows_popular);
$popular = mysql_query($query_limit_popular, $localhost) or die(mysql_error());
$row_popular = mysql_fetch_assoc($popular);

if (isset($_GET['totalRows_popular'])) {
  $totalRows_popular = $_GET['totalRows_popular'];
} else {
  $all_popular = mysql_query($query_popular);
  $totalRows_popular = mysql_num_rows($all_popular);
}
$totalPages_popular = ceil($totalRows_popular/$maxRows_popular)-1;
?>
<div id="dashboard-container">
  <div id="recent" class="frame">
    <h2>Recent Activity</h2>
    <div class="recent-feed"><span class="topic quiz">New Quiz</span><span class="event"><a href="javascript:;">Jing Ting</a> has created a new quiz, <a href="javascript:;">&quot;Why like that?&quot;</a> under <a href="javascript:;">Entertainment</a>.</span></div>
    <div class="recent-feed"><span class="topic achievement">New Achievement</span><span class="event"><a href="javascript:;">Kristal</a> has been awarded the &quot;Rookie</span>&quot; achievement!</div>
    <div class="recent-feed"><span class="topic friend">Friend</span><a href="javascript:;">Chris Chua</a> has added you as a friend!</div>
  </div>
  <div id="recommendations" class="left-right-frame">
    <h2>Recommendations</h2>
    <?php do { ?>
      <div class="quiz_box clear">
        <div class="thumb_box">
          <div class="quiz_rating">★★★</div>
        <a href="previewQuiz.php?id=<?php echo $row_recommendations['quiz_id']; ?>"><img src="quiz_images/imgcrop.php?w=200&amp;h=150&amp;f=<?php echo $row_recommendations['quiz_picture']; ?>" alt="<?php echo $row_recommendations['quiz_description']; ?>" width="80" height="60" border="0" title="<?php echo $row_recommendations['quiz_description']; ?>" /></a></div>
        <div class="quiz_details">
          <h3><?php echo $row_recommendations['quiz_name']; ?></h3>
          <p class="description"><?php echo substr($row_recommendations['quiz_description'], 0, 120).((strlen($row_recommendations['quiz_description']) < 120)? "" : "..."); ?></p>
          <p class="source">from <a href="javascript:;"><?php echo $row_recommendations['cat_name']; ?></a> Category by <a href="javascript:;"><?php echo $row_recommendations['nickname']; ?></a></p>
        </div>
      </div>
      <?php } while ($row_recommendations = mysql_fetch_assoc($recommendations)); ?>
  </div>
  <div id="popular" class="left-right-frame">
    <h2>Popular</h2>
    <?php do { ?>
      <div class="quiz_box clear">
        <div class="thumb_box">
          <div class="quiz_rating">★★★</div>
        <a href="previewQuiz.php?id=<?php echo $row_popular['quiz_id']; ?>"><img src="quiz_images/imgcrop.php?w=200&amp;h=150&amp;f=<?php echo $row_popular['quiz_picture']; ?>" alt="<?php echo $row_popular['quiz_description']; ?>" width="80" height="60" border="0" title="<?php echo $row_popular['quiz_description']; ?>" /></a></div>
        <div class="quiz_details">
          <h3><?php echo $row_popular['quiz_name']; ?></h3>
          <p class="description"><?php echo substr($row_popular['quiz_description'], 0, 120).((strlen($row_popular['quiz_description']) < 120)? "" : "..."); ?></p>
          <p class="source">from <a href="javascript:;"><?php echo $row_popular['cat_name']; ?></a> Category by <a href="javascript:;"><?php echo $row_popular['nickname']; ?></a></p>
        </div>
      </div>
      <?php } while ($row_popular = mysql_fetch_assoc($popular)); ?>
  </div>
</div>
<?php
mysql_free_result($recommendations);
mysql_free_result($popular);
?>
