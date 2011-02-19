<?php require('quizrooDB.php');
require("quiz.php");

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_listQuiz = 10;
$pageNum_listQuiz = 0;
if (isset($_GET['pageNum_listQuiz'])) {
  $pageNum_listQuiz = $_GET['pageNum_listQuiz'];
}
$startRow_listQuiz = $pageNum_listQuiz * $maxRows_listQuiz;

$query_listQuiz = sprintf("SELECT quiz_id FROM q_quizzes WHERE fk_member_id = %d AND isPublished != 3 ORDER BY creation_date DESC", $member->id);
$query_limit_listQuiz = sprintf("%s LIMIT %d, %d", $query_listQuiz, $startRow_listQuiz, $maxRows_listQuiz);
$listQuiz = mysql_query($query_limit_listQuiz, $quizroo) or die(mysql_error());
$row_listQuiz = mysql_fetch_assoc($listQuiz);

if (isset($_GET['totalRows_listQuiz'])) {
  $totalRows_listQuiz = $_GET['totalRows_listQuiz'];
} else {
  $all_listQuiz = mysql_query($query_listQuiz);
  $totalRows_listQuiz = mysql_num_rows($all_listQuiz);
}
$totalPages_listQuiz = ceil($totalRows_listQuiz/$maxRows_listQuiz)-1;

$queryString_listQuiz = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_listQuiz") == false && 
        stristr($param, "totalRows_listQuiz") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_listQuiz = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_listQuiz = sprintf("&totalRows_listQuiz=%d%s", $totalRows_listQuiz, $queryString_listQuiz);
?>
<div class="frame rounded">
  <h3>Manage Quizzes</h3>
  <p>You can make changes to your quizzes by clicking the <img src="../webroot/img/edit.png" alt="Modify" width="16" height="16" border="0" title="Modify Quiz" /> button associated with that quiz. You can also publish or unpublish your quizzes.</p>
</div>
<div class="frame rounded">
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" id="checkQuizTable" class="rounded">
      <tr>
        <th width="80">&nbsp;</th>
        <th>Quiz Information</th>
        <th width="100">Score (Likes)</th>
        <th width="120">Created on</th>
        <th width="80">Action</th>
      </tr>
      <?php do { $quizloop = new Quiz($row_listQuiz['quiz_id']); 
	  // prepare the formatted status
	  switch($quizloop->isPublished){
		  case 0: $status = '<em>Status:</em> <span class="draft">Unpublished Draft</span>'; break;
		  case 1: $status = '<em>Status:</em> <span class="published">Published</span>'; break;
		  case 2: $status = '<em>Status:</em> <span class="modified">Unpublished Modification</span>'; break;
		  case 3: $status = '<em>Status:</em> <span class="archived">Archived</span>'; break;
		  default: $status = '<em>Status:</em> Limbo :/'; break;
	  }	  
	  ?>
        <tr>
          <td width="80" align="center"><img src="../quiz_images/imgcrop.php?w=70&amp;h=52&amp;f=<?php echo $quizloop->quiz_picture; ?>" alt="<?php echo $quizloop->quiz_picture; ?>" width="70" height="52" border="0" title="<?php echo $quizloop->quiz_name; ?>" /></td>
          <td><p class="name"><?php echo (strlen($quizloop->quiz_name) > 90) ? substr($quizloop->quiz_name, 0, 90)."..." : $quizloop->quiz_name ; ?></p><p class="properties">
          <?php echo "<em>Results:</em> ".$quizloop->getResults("count").", <em>Questions:</em> ".$quizloop->getQuestions("count"); ?></p>
          <p class="status"><?php echo $status; ?></p></td>
          <td width="100" align="center"><?php echo $quizloop->quiz_score; ?> (<?php echo $quizloop->likes; ?>)</td>
          <td width="120" align="center"><?php echo date("F j, Y g:ia", strtotime($quizloop->creation_date)); ?></td>
          <td width="80" align="center">
		  	<?php if($quizloop->isPublished >= 1){ ?><a href="modifyQuiz.php?id=<?php echo $quizloop->quiz_id; ?>"><img src="../webroot/img/edit.png" alt="Modify" width="16" height="16" border="0" title="Modify Quiz" /></a>
            <?php }else{ ?><a href="createQuiz.php?step=1&id=<?php echo $quizloop->quiz_id; ?>"><img src="../webroot/img/edit.png" alt="Resume Creating" width="16" height="16" border="0" title="Resume Creating" /></a><?php } ?>&nbsp;
			<?php if($quizloop->isPublished == 1){ ?><a href="publishQuiz.php?unlist&id=<?php echo $quizloop->quiz_id; ?>"><img src="../webroot/img/unpublish.png" alt="Unpublish" width="16" height="16" border="0" title="Unpublish Quiz" /></a>
			<?php }else{ ?><a href="publishQuiz.php?id=<?php echo $quizloop->quiz_id; ?>"><img src="../webroot/img/publish.png" alt="Publish" width="16" height="16" border="0" title="Publish Quiz" /></a><?php } ?>&nbsp;
            <a href="deleteQuiz.php?id=<?php echo $quizloop->quiz_id; ?>"><img src="../webroot/img/delete.png" alt="Delete" width="16" height="16" border="0" title="Delete Quiz" /></a>
          </td>
        </tr>
        <?php } while ($row_listQuiz = mysql_fetch_assoc($listQuiz)); ?>
    </table>
  <br />
    <table width="200" border="0" align="center" cellpadding="5" cellspacing="0" class="paging-table" >
      <tr>
        <td><?php if ($pageNum_listQuiz > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, 0, $queryString_listQuiz); ?>">First</a>
            <?php }else{ ?>First<?php } ?></td>
        <td><?php if ($pageNum_listQuiz > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, max(0, $pageNum_listQuiz - 1), $queryString_listQuiz); ?>">Previous</a>
            <?php }else{ ?>Previous<?php } ?></td>
        <td><?php if ($pageNum_listQuiz < $totalPages_listQuiz) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, min($totalPages_listQuiz, $pageNum_listQuiz + 1), $queryString_listQuiz); ?>">Next</a>
            <?php }else{ ?>Next<?php } ?></td>
        <td><?php if ($pageNum_listQuiz < $totalPages_listQuiz) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, $totalPages_listQuiz, $queryString_listQuiz); ?>">Last</a>
            <?php }else{ ?>Last<?php } ?></td>
      </tr>
    </table>
<p class="center">Quizzes <?php echo ($startRow_listQuiz + 1) ?> to <?php echo min($startRow_listQuiz + $maxRows_listQuiz, $totalRows_listQuiz) ?> of <?php echo $totalRows_listQuiz ?></p>
</div>
<?php
mysql_free_result($listQuiz);
?>
