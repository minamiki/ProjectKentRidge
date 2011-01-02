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
	
	$results = $database->get('member',array('level,rank'),'member_id='.$memberid);
	$db_rank = $results[0]['rank'];
	$level = $results[0]['level'];
	
	$results = $database->query('SELECT fk_id FROM g_ranks WHERE min<'.$level.' ORDER BY min DESC LIMIT 1');
	$rank = $results[0]['fk_id'];
	
	if($db_rank!=$rank){
		$database->update('member','member_id',$memberid,array('rank'),array($rank));
		$database->save('g_achievements_log',array('fk_member_id','fk_achievement_id'),array($memberid,$rank));
		$achievement_array[] = $rank;		
	}
	// End Ranks
	
}

?>