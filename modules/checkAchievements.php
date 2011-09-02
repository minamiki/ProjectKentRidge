<?php
require 'database.php';
/**
 * Checks through criteria for all achievements to determine if user has attained achievements.
 * 
 * Achievements can be categorised as
 * - points based
 * - condition based
 * 
 * @param $memberid, $achievement_array
 */
function checkAchievements($memberid,$achievement_array){
	
	// Use a common database connection
	$database = new Database();
	
	/*
	 * Points based achievements
	 */
	
	/*
	 * Ranks
	 * - Find out current rank
	 * - Find out current level
	 * - Check if current level match rank
	 * - Otherwise increment rank
	 * - Update rank in member table
	 * - Create achievement in achievement log
	 * - Push achievement to be displayed
	 */
	
	$results = $database->get('s_members',array('level,rank'),'member_id='.$memberid);
	$db_rank = $results[0]['rank'];
	$level = $results[0]['level'];
	
	$results = $database->query('SELECT fk_id FROM g_ranks WHERE min<='.$level.' ORDER BY min DESC LIMIT 1');
	$rank = $results[0]['fk_id'];
	
	if($db_rank!=$rank){
		$database->update('s_members','member_id',$memberid,array('rank'),array($rank));
		$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,$rank));
		$achievement_array[] = $rank;
	}
		
	// End Ranks
	
	/*
	 * Condition based achievements
	 * Fetch all achievements of the user to determine if user has attained achievements
	 */
	
	$results = $database->get('g_achievements_log',array('fk_achievement_id'),'fk_member_id='.$memberid);
	$achievements = array();
	foreach($results as $achievement){
		$achievements[]=$achievement['fk_achievement_id'];
	}
	
	/*
	 * - Check if achievement has been attained
	 * - If not attained, check if user has taken quizzes in 2 different categories
	 * - If user has met criteria
	 *   - Create achievement in achievement log
	 *   - Push achievement to be displayed
	 */
	
	/*
	 * Category based achievements
	 */
	
	/*
	 *  Curiousity killed the cat
	 *  Completed at least 1 quiz each from 2 categories
	 */
	if(!in_array('52',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_cat) as categories FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid);		
		$categories = $results[0]['categories'];
		if($categories>=2){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,52));
			$achievement_array[] = 52;
		}
	}
	
	/*
	 * Born an Explorer
	 * Completed  at least 1 quiz each from 4 categories
	 */
	if(!in_array('53',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_cat) as categories FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid);		
		$categories = $results[0]['categories'];
		if($categories>=4){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,53));
			$achievement_array[] = 53;
		}
	}
	
	/*
	 * Jack of all Trades
	 * Completed at least 1 quiz each from 8 categories
	 */
	if(!in_array('54',$achievements)){
		if($categories>=8){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,54));
			$achievement_array[] = 54;
		}
	}
	
	/*
	 * Generalist
	 * Completed 10 quizzes from the General category
	 */
	if(!in_array('55',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=1');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,55));
			$achievement_array[] = 55;
		}
	}
	
	
	/*
	 * General Specialist
	 * Completed 25 quizzes from the General category
	 */
	if(!in_array('56',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=1');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,56));
			$achievement_array[] = 56;
		}
	}
	
	/*
	 * Budding Geek
	 * Completed 10 quizzes from the Technology category
	 */
	if(!in_array('57',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=2');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,57));
			$achievement_array[] = 57;
		}
	}
	
	
	/*
	 * Most likely a Geek
	 * Completed 25 quizzes from the Technology category
	 */
	if(!in_array('58',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=2');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,58));
			$achievement_array[] = 58;
		}
	}
	
	/*
	 * Enjoying life
	 * Completed 10 quizzes from the Lifestyle category
	 */
	if(!in_array('59',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=3');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,59));
			$achievement_array[] = 59;
		}
	}
	
	/*
	 * Life is a breeze
	 * Completed 25 quizzes from the Lifestyle category
	 */
	if(!in_array('60',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=3');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,60));
			$achievement_array[] = 60;
		}
	}
	
	/*
	 * Becoming a Leader
	 * Completed 10 quizzes from the Politics category
	 */
	if(!in_array('61',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=4');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,61));
			$achievement_array[] = 61;
		}
	}
	
	/*
	 * Consider being a Minister
	 * Completed 25 quizzes from the Politics category
	 */
	if(!in_array('62',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=4');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,62));
			$achievement_array[] = 62;
		}
	}	
	
	/*
	 * Wildlife Explorer
	 * Completed 10 quizzes from the Animals category
	 */
	if(!in_array('63',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=5');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,63));
			$achievement_array[] = 63;
		}
	}
	
	/*
	 * Be a Zoologist
	 * Completed 25 quizzes from the Animals category
	 */
	if(!in_array('64',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=5');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,64));
			$achievement_array[] = 64;
		}
	}
	
	/*
	 * Finding your role
	 * Completed 10 quizzes from the Movies category
	 */
	if(!in_array('65',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=6');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,65));
			$achievement_array[] = 65;
		}
	}	
		
	/*
	 * The Producer
	 * Completed 25 quizzes from the Movies category
	 */
	if(!in_array('66',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=6');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,66));
			$achievement_array[] = 66;
		}
	}	
	
	/*
	 * Getting social
	 * Completed 10 quizzes from the Social category
	 */
	if(!in_array('67',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=7');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,67));
			$achievement_array[] = 67;
		}
	}		
	
	/*
	 * Becoming a Sociologist
	 * Completed 25 quizzes from the Social category
	 */
	if(!in_array('68',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=7');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,68));
			$achievement_array[] = 68;
		}
	}
	
	/*
	 * Getting Entertained
	 * Completed 10 quizzes from the Entertainment category
	 */
	if(!in_array('69',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=8');
		$taken = $results[0]['quizzes'];
		if($taken>=10){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,69));
			$achievement_array[] = 69;
		}
	}
	
	/*
	 * Info-tainment!
	 * Completed 25 quizzes from the Entertainment category
	 */
	if(!in_array('70',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid.' AND fk_quiz_cat=8');
		$taken = $results[0]['quizzes'];
		if($taken>=25){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,70));
			$achievement_array[] = 70;
		}
	}
	
	/*
	 * End category based achievements
	 */
	
	/*
	 * Special Achievements
	 */
	
	/*
	 * Officially initiated
	 * Completed quiz taking and quiz creation
	 */
	if(!in_array('51',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) as quizzes FROM q_store_result WHERE fk_member_id='.$memberid);
		$taken = $results[0]['quizzes'];
		$results = $database->query('SELECT COUNT(DISTINCT quiz_id) as quizzes FROM q_quizzes WHERE fk_member_id='.$memberid);
		$created = $results[0]['quizzes'];
		if($taken>0 && $created>0){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,51));
			$achievement_array[] = 51;
		}
	}
	
	/*
	 * 6 quizzes in a row
	 * Achieved a 1.5x multiplier
	 */
	if(!in_array('71',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) AS count FROM `q_store_result` WHERE `fk_member_id`='.$memberid.' AND DATE(`timestamp`) = DATE(NOW())');
		$count = $results[0]['count'];
		if($count>=6){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,71));
			$achievement_array[] = 71;
		}
	}
	
	/*
	 * 2 timer
	 * Achieved a 2x multiplier
	 */
	if(!in_array('72',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) AS count FROM `q_store_result` WHERE `fk_member_id`='.$memberid.' AND DATE(`timestamp`) = DATE(NOW())');
		$count = $results[0]['count'];
		if($count>=11){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,72));
			$achievement_array[] = 72;
		}
	}
	
	/*
	 * 3 times!
	 * Achieved a 3x multiplier
	 */
	if(!in_array('73',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) AS count FROM `q_store_result` WHERE `fk_member_id`='.$memberid.' AND DATE(`timestamp`) = DATE(NOW())');
		$count = $results[0]['count'];
		if($count>=21){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,73));
			$achievement_array[] = 73;
		}
	}

	/*
	 * Top of the charts
	 * Become the 1st on the Leaderboard
	 */
	
	/*
	 * Amazing comeback
	 * Regain 1st on the Leaderboard
	 */
	
	/*
	 * End Special Achievements
	 */
	
	/*
	 * Duration based
	 */
	
	/*
	 * Half Quiz-a-thon
	 * Completed 21 quizzes in 24 hours
	 */
	if(!in_array('76',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) AS count FROM `q_store_result` WHERE `fk_member_id`='.$memberid.' AND `timestamp` > DATE_SUB(NOW(), INTERVAL 1 DAY)');
		$count = $results[0]['count'];
		if($count>=21){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,76));
			$achievement_array[] = 76;
		}
	}
	
	/*
	 * Quiz-a-thon
	 * Completed 42 quizzes in 24 hours
	 */	
	if(!in_array('77',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_id) AS count FROM `q_store_result` WHERE `fk_member_id`='.$memberid.' AND `timestamp` > DATE_SUB(NOW(), INTERVAL 1 DAY)');
		$count = $results[0]['count'];
		if($count>=42){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,77));
			$achievement_array[] = 77;
		}
	}
	
	/*
	 * 168 hours
	 * Continuously active in Quizroo for 1 week
	 */
	
	/*
	 * 730.48 hours
	 * Continuously active in Quizroo for 1 month
	 */
	
	/*
	 * End Duration based
	 */
	
	return $achievement_array;
}

function retrieveAchievements($array){
	$return_array = array();
	$database = new Database();
	
	foreach($array as $achievement){
		$result = $database->query('SELECT * FROM g_achievements WHERE id='.$achievement);

		/*
		 * If the achievement is rank related, fetch level data.
		 */
		if($result[0]['type']=='3'){
			$levelscore = $database->get('g_levels',array('points'),'id='.$achievement.' OR id='.($achievement+1).' OR id='.($achievement-1));
			array_push($return_array, array('image'=>$result[0]['image'],'name'=>$result[0]['name'],'description'=>$result[0]['description'],'level'=>$achievement,'levelscore'=>$levelscore[0]['points'],'nextlevelscore'=>$levelscore[1]['points']));
		}else{
			array_push($return_array,$result[0]);
		}		
	}	
	
	return json_encode($return_array);
}
?>