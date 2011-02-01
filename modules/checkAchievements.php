<?php
require 'database.php';
/**
 * Checks through criteria for all achievements to determine if user has attained achievements.
 * 
 * Achievements can be categorised as
 * - points based
 * - condition based
 * 
 * @param $memberid
 */
function checkAchievements($memberid){
	
	global $achievement_array;
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
	 * Explorer
	 * - Check if achievement has been attained
	 * - If not attained, check if user has taken quizzes in 2 different categories
	 * - If user has met criteria
	 *   - Create achievement in achievement log
	 *   - Push achievement to be displayed
	 */
	if(!in_array('52',$achievements)){
		$results = $database->query('SELECT COUNT(DISTINCT fk_quiz_cat) as categories FROM q_store_result LEFT JOIN q_quizzes ON fk_quiz_id=quiz_id WHERE q_store_result.fk_member_id='.$memberid);
		if($results[0]['categories']==2){
			$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,52));
			$achievement_array[] = 52;
		}
	}
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
			$levelscore = $database->get('g_levels',array('points'),'id='.$result[0]['level'].' OR id='.($result[0]['level']+1).' OR id='.($result[0]['level']-1));
			array_push($return_array, array('image'=>$result[0]['image'],'name'=>$result[0]['name'],'description'=>$result[0]['description'],'level'=>$result[0]['level'],'levelscore'=>$levelscore[0]['points'],'nextlevelscore'=>$levelscore[1]['points']));
		}else{
			array_push($return_array,$result[0]);
		}		
	}	
	
	return json_encode($return_array);
}
?>