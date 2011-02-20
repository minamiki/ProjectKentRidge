<?php
require('quizrooDB.php');
require('system.php');

$system = new System();

// display stats
$system->displayStats();

// reset the daily scores
$system->resetDailyScore();

?>