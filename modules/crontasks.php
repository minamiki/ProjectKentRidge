<?php
//scheduler that automates executing these tasks
require('quizrooDB.php');
require('system.php');

$system = new System();

// display stats
$system->displayStats();

// log the member stats
$system->logMemberStats();

// reset the daily scores
$system->resetDailyScore();

?>