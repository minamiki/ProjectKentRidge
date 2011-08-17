<!-- Read Excel file and add to database-->
<?php
require('../modules/quizrooDB.php'); // database connection
// get the member info
require('../modules/member.php');
require('../modules/quiz.php');
$member = new Member();
?>
<?php
// get the unikey from the form
$key = $_POST['unikey'];

if ( $_FILES['file']['tmp_name'] )
{
 $dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
 $rows = $dom->getElementsByTagName( 'Row' );
 $first_row = true;
 foreach ($rows as $row)
 {
   $quiz_id = 0;
   if ( !$first_row ) // for checking whether it is the header row
   {
     // for quiz information
     $title = "";
     $des = "";
     $cat = "";
     $quiz_picture = "";
	 $memberid = "";
	 // for question
	 $question = "";
	 $question_picture = "";
	 // for option
	 $option = "";
	 $isCorrect = "";
	 // for result
	 $result_title = "";
	 $result_des = "";
	 $result_picture = "";
	 $range_max = "";
	 $range_min = "";
	 
     $index = 1;
     $cells = $row->getElementsByTagName( 'Cell' );
     foreach( $cells as $cell )
     {
       $ind = $cell->getAttribute( 'Index' );
       if ( $ind != null ) $index = $ind;

       if ( $index == 1 ) $title = $cell->nodeValue;
	   //check if title is null
	   if($title != null) {
		   // if title is not null, get quiz info
		   if ( $index == 2 ) $des = $cell->nodeValue;
		   if ( $index == 3 ) $cat = $cell->nodeValue;
		   if ( $index == 4 ) $quiz_picture = $cell->nodeValue;
		   if ( $index == 5 ) $memberid = $cell->nodeValue;
	   }
	   else{
		   if ( $index == 6 ) $question = $cell->nodeValue;
		   if ( $index == 7 ) $question_picture = $cell->nodeValue;
		   if ( $index == 8 ) $option = $cell->nodeValue;
		   if ( $index == 9 ) $isCorrect = $cell->nodeValue;
		   if ( $index == 10 ) $result_title = $cell->nodeValue;
		   if ( $index == 11 ) $result_des = $cell->nodeValue;
		   if ( $index == 12 ) result_picture = $cell->nodeValue;
		   if ( $index == 13 ) $range_max = $cell->nodeValue;
		   if ( $index == 14 ) $range_min = $cell->nodeValue;
	   }
	   $index += 1;
	 } // end going through cells
	 
	 //create quiz
	 $quiz = new Quiz();
	 //get the quiz_id here to use for inserting questions
	 $quiz_id = $quiz->create($title, $des, $cat, $quiz_picture, $memberid, 'vnh323hg');
	 //check if result is null. If not, insert into database.
	 if($result_title != null){
	 	$quiz->addTestTypeResult($result_title, $result_des, $result_picture, $range_max, $range_min, $quiz_id);
	 }
	 //check if the question is null. If not, insert into database.
	 if($question != null){
	 	$question_id = $quiz->addQuestion($question, $memberid);
	 }
	 //check if the option is null. If not, insert into database with reference
	 if($option != null){
	 	$quiz->addTestTypeOption($option, $isCorrect, $question_id);
	 } 
     } // end if first row
   } // end going through 1 row
   $first_row = false;
 }
}
?>
<!--table>
<tr>
<th>Title</th>
<th>Description</th>
<th>Cat</th>
<th>Quiz Picture</th>
<th>Member ID</th>
</tr>
<?php //foreach( $data as $row ) { ?>
<tr>
<td><?php //echo( $row['quiz_name'] ); ?></td>
<td><?php //echo( $row['quiz_description'] ); ?></td>
<td><?php //echo( $row['fk_quiz_cat'] ); ?></td>
<td><?php //echo( $row['quiz_picture'] ); ?></td>
<td><?php //echo( $row['fk_member_id'] ); ?></td>
</tr>
<?php } ?>
</table-->
<html>
<body>
Data has been added to the database.
</body>
</html>