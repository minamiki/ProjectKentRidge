<?php
require('../modules/quizrooDB.php'); 
require('../modules/inc/class.stemmer.inc'); 

// check if query is blank
if(isset($_GET['searchQuery'])){
	if($_GET['searchQuery'] != ""){
		$currentPage = $_SERVER["PHP_SELF"];
		
		$searchQuery = $_GET['searchQuery'];
		
		// split it up into tokens
		// $tokenArray = explode(' ', $searchQuery);
		
		// prepare the Porter Stemmer
		$stemmer = new Stemmer();
		$tokenArray = $stemmer->stem_list($searchQuery);
		
		// search modifiers
		if(isset($_GET['searchType'])){
			$searchType = $_GET['searchType'];
		}else{
			$searchType = 0;
		}
		if(isset($_GET['question_option'])){
			$searchOption = $_GET['question_option'];
		}else{
			$searchOption = 0;
		}
		
		$maxRows_listQuiz = 10;
		$pageNum_listQuiz = 0;
		$maxPage_listQuiz = 10;	// Maximum paging value
		if (isset($_GET['pageNum_listQuiz'])) {
		  $pageNum_listQuiz = $_GET['pageNum_listQuiz'];
		}
		$startRow_listQuiz = $pageNum_listQuiz * $maxRows_listQuiz;
		//different search type and options
		if($searchType == 1){
			if($searchOption == 0){
				$query_listQuiz = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, (MATCH(quiz_name, quiz_description) AGAINST(%s)) AS score FROM q_quizzes, q_quiz_cat, s_members WHERE MATCH(quiz_name, quiz_description) AGAINST(%s) AND member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 ORDER BY score DESC", GetSQLValueString($_GET['searchQuery'], "text"), GetSQLValueString($_GET['searchQuery'], "text"));
			}else{
				$query_listQuiz = sprintf("SELECT quiz_id, quiz_name, quiz_description, quiz_picture, fk_quiz_cat, member_name, fk_member_id, cat_name, likes, (MATCH(quiz_name, quiz_description) AGAINST(%s WITH QUERY EXPANSION)) AS score FROM q_quizzes, q_quiz_cat, s_members WHERE MATCH(quiz_name, quiz_description) AGAINST(%s WITH QUERY EXPANSION) AND member_id = fk_member_id AND cat_id = fk_quiz_cat AND isPublished = 1 ORDER BY score DESC", GetSQLValueString($_GET['searchQuery'], "text"), GetSQLValueString($_GET['searchQuery'], "text"));
			}
		}else{
			if($searchOption == 0){
				$query_sql = "";
				// loop through the token array and add it to the sql string
				foreach($tokenArray as $keyword){
					$liked_query = GetSQLValueString("[[:<:]]".$keyword.".*[[:>:]]", "text");
					$query_sql .= sprintf("q.quiz_name REGEXP %s OR q.quiz_description REGEXP %s OR ", $liked_query, $liked_query);
				}
				$query_sql = substr($query_sql, 0, -4);
				$query_listQuiz = sprintf("SELECT q.quiz_id, q.quiz_name, q.quiz_description, q.quiz_picture, q.fk_quiz_cat, m.member_name, fk_member_id, c.cat_name, q.likes FROM q_quizzes q, q_quiz_cat c, s_members m WHERE (%s) AND m.member_id = q.fk_member_id AND c.cat_id = q.fk_quiz_cat AND q.isPublished = 1", $query_sql);
			}else{
				$query_sql_1 = "";
				$query_sql_2 = "";
				$query_sql_3 = "";
				// loop through the token array and add it to the sql string
				foreach($tokenArray as $keyword){
					$liked_query = GetSQLValueString("[[:<:]]".$keyword.".*[[:>:]]", "text");
					$query_sql_1 .= sprintf("q.quiz_name REGEXP %s OR q.quiz_description REGEXP %s OR ", $liked_query, $liked_query);
					$query_sql_2 .= sprintf("question REGEXP %s OR ", $liked_query);
					$query_sql_3 .= sprintf("`option` REGEXP %s OR ", $liked_query);
				}
				$query_sql_1 = substr($query_sql_1, 0, -4);
				$query_sql_2 = substr($query_sql_2, 0, -4);
				$query_sql_3 = substr($query_sql_3, 0, -4);

				$query_listQuiz = sprintf("SELECT q.quiz_id, q.quiz_name, q.quiz_description, q.quiz_picture, q.fk_quiz_cat, m.member_name, fk_member_id, c.cat_name, q.likes FROM q_quizzes q, q_quiz_cat c, s_members m WHERE (%s OR q.quiz_id IN(SELECT quiz_id FROM q_quizzes, q_questions WHERE (%s) AND fk_quiz_id = quiz_id) OR q.quiz_id IN(SELECT quiz_id FROM q_quizzes, q_questions, q_options WHERE (%s) AND fk_quiz_id = quiz_id AND fk_question_id = question_id)) AND m.member_id = q.fk_member_id AND c.cat_id = q.fk_quiz_cat AND q.isPublished = 1", $query_sql_1, $query_sql_2, $query_sql_3);
			}
		}
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
	}
}else{
	$searchQuery = "";
	$searchType = 0;
	$searchOption = 0;
}
?>
<link href="../webroot/css/paging.css" rel="stylesheet" type="text/css" />

<div id="search-preamble" class="framePanel rounded">
  <h2>Search</h2>
  <div class="content-container">
    <form id="search" name="search" method="get" action="search.php">
      <?php if(isset($_GET['sql'])){ ?><input type="hidden" name="sql" id="sql" value="on" /><?php } ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td><span id="sprytextfield1"> <!-- display for search page-->
            <input type="text" name="searchQuery" id="searchQuery" value="<?php echo $searchQuery; ?>" />
          <span class="textfieldRequiredMsg">Your search query should not be blank!</span></span></td>
          <td width="100" align="right" valign="top"><input type="submit" name="searchBtn" id="searchBtn" value="Search" /></td>
        </tr>
      </table>
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr> <!-- display for options of search types to allow user to choose-->
          <td width="20" align="right"><input <?php if (!(strcmp($searchType,0))) {echo "checked=\"checked\"";} ?> name="searchType" type="radio" id="queryType0" value="0" /></td>
          <td width="350"><label for="queryType0" title="use a simple boolean search">standard keyword search</label></td>
          <td width="20" align="right"><input <?php if (!(strcmp($searchType,1))) {echo "checked=\"checked\"";} ?> type="radio" name="searchType" id="queryType1" value="1" /></td>
          <td><label for="queryType1" title="use term weighting and scoring">natural language search</label></td>
        </tr>
        <tr>
          <td width="20" align="right"><input <?php if (!(strcmp($searchOption,1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="question_option" id="question_option0" value="1" <?php if (!(strcmp($searchType,1))) {echo "disabled=\"disabled\"";} ?> /></td>
          <td width="350"><label title="Include text in quiz questions and options" for="question_option0">search within questions and options</label></td>
          <td width="20" align="right"><input <?php if (!(strcmp($searchOption,1))) {echo "checked=\"checked\"";} ?> name="question_option" type="checkbox" id="question_option1" value="1"  <?php if (!(strcmp($searchType,0))) {echo "disabled=\"disabled\"";} ?> /></td>
          <td><label for="question_option1" title="use relavance feedback in the form of query expansion">include recommendations</label></td>
        </tr>
        <?php if($searchQuery != "" && $totalRows_listQuiz != 0){ ?>
        <tr> <!-- display for #results found-->
          <td colspan="4"><p>Your search returned <?php echo ($totalRows_listQuiz > 1) ? $totalRows_listQuiz." quizzes." : $totalRows_listQuiz." quiz."; ?></p></td>
        </tr>
        <?php } ?>
        <?php if($searchQuery != "" && isset($_GET['sql'])){ ?>
        <tr>
          <td colspan="4"><p class="sql"><?php echo $query_listQuiz; ?></p></td>
        </tr>
        <?php } ?>
      </table>
    </form>
  </div>
</div>
<div id="results" class="framePanel rounded">
  <h2>Results</h2> <!-- show preview quiz boxes of search results -->
  <div class="content-container">
    <?php if($totalRows_listQuiz != 0){ do{ ?>
    <div class="quiz_box clear">
      <h3><a href="previewQuiz.php?id=<?php echo $row_listQuiz['quiz_id']; ?>"><?php echo $row_listQuiz['quiz_name']; ?></a></h3>
      <div class="thumb_box"> <a href="previewQuiz.php?id=<?php echo $row_listQuiz['quiz_id']; ?>"><img src="../quiz_images/imgcrop.php?w=90&amp;h=68&amp;f=<?php echo $row_listQuiz['quiz_picture']; ?>" alt="<?php echo $row_listQuiz['quiz_description']; ?>" width="90" height="68" border="0" title="<?php echo $row_listQuiz['quiz_description']; ?>" /></a></div>
      <div class="quiz_details">
        <p class="description"><?php echo substr($row_listQuiz['quiz_description'], 0, 250).((strlen($row_listQuiz['quiz_description']) < 250)? "" : "..."); ?></p>
        <p class="source">from <a href="topics.php?topic=<?php echo $row_listQuiz['fk_quiz_cat']; ?>"><?php echo $row_listQuiz['cat_name']; ?></a> by <a href="viewMember.php?id=<?php echo $row_listQuiz['fk_member_id']; ?>"><?php echo $row_listQuiz['member_name']; ?></a></p>
        <p class="rating"><span class="like"><?php echo $row_listQuiz['likes']; ?></span> <?php echo ($row_listQuiz['likes'] > 1) ? "people like" : "person likes"; ?> this</p>
      </div>
    </div>
    <?php }while($row_listQuiz = mysql_fetch_assoc($listQuiz)); ?>
    <div id="paging">
      <table border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <td><?php if ($pageNum_listQuiz > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, 0, $queryString_listQuiz); ?>">First</a>
            <?php }else{ ?>
            <a href="javascript:;" class="disabled">First</a>
            <?php } // Show if not first page ?></td>
          <td><?php if ($pageNum_listQuiz > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, max(0, $pageNum_listQuiz - 1), $queryString_listQuiz); ?>">Previous</a>
            <?php }else{ ?>
            <a href="javascript:;" class="disabled">Previous</a>
            <?php } // Show if not first page ?></td>
          <td>&nbsp;</td>
          <td><?php // check if max pages reached
	if($totalPages_listQuiz > $maxPage_listQuiz){
		if($pageNum_listQuiz > $maxPage_listQuiz-2 && $pageNum_listQuiz < $totalPages_listQuiz-$maxPage_listQuiz+2){
			if($pageNum_listQuiz - floor($maxPage_listQuiz/2) >= 0){
				$startpage = $pageNum_listQuiz - floor($maxPage_listQuiz/2);
			}else{
				$startpage = $pageNum_listQuiz;
			}
			if($pageNum_listQuiz + ceil($maxPage_listQuiz/2) <= $totalPages_listQuiz){
				$maxpage = $pageNum_listQuiz + ceil($maxPage_listQuiz/2);
			}else{
				$maxpage = $pageNum_listQuiz;
			}
		}else{
			if($pageNum_listQuiz < $maxPage_listQuiz){
				$startpage = 0;
				$maxpage = $maxPage_listQuiz;
			}else{
				$startpage = $totalPages_listQuiz - $maxPage_listQuiz+1;
				$maxpage = $totalPages_listQuiz+1;
			}
		}			
	}else{
		$startpage = 0;
		$maxpage = $totalPages_listQuiz+1;
	}
	for($i = $startpage; $i < $maxpage; $i++){ ?>
            <?php if($i != $pageNum_listQuiz){ ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, $i, $queryString_listQuiz); ?>"><?php echo $i+1; ?></a>
            <?php }else{ ?>
            <a href="javascript:;" class="disabled"><?php echo $i+1; ?></a>
            <?php }} ?></td>
          <td>&nbsp;</td>
          <td><?php if ($pageNum_listQuiz < $totalPages_listQuiz) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, min($totalPages_listQuiz, $pageNum_listQuiz + 1), $queryString_listQuiz); ?>">Next</a>
            <?php }else{ ?>
            <a href="javascript:;" class="disabled">Next</a>
            <?php } // Show if not last page ?></td>
          <td><?php if ($pageNum_listQuiz < $totalPages_listQuiz) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_listQuiz=%d%s", $currentPage, $totalPages_listQuiz, $queryString_listQuiz); ?>">Last</a>
            <?php }else{ ?>
            <a href="javascript:;" class="disabled">Last</a>
            <?php } // Show if not last page ?></td>
        </tr>
      </table>
    </div>
    <?php }else{ ?>
    <div id="loader-box">No Quizzes were found. Try other keywords!</div>
    <?php } ?>
  </div>
</div>
