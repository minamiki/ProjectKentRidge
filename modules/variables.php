<?php include("quizrooDB.php");
// load the values from database
$queryVars = "SELECT * FROM `s_variables`";
$getVars = mysql_query($queryVars, $quizroo) or die(mysql_error());
$row_getVars = mysql_fetch_assoc($getVars);

//----------------------------------------
// For game variables
// Prefix with "GAME_"
//----------------------------------------

// The base score for awarding of points
$GAME_BASE_POINT = $row_getVars['GAME_BASE_POINT'];
// The multiplier amount for each increment
$GAME_MULTIPLIER = $row_getVars['GAME_MULTIPLIER'];
// Whether users get points for retaking quizzes
$GAME_REWARD_RETAKES = $row_getVars['GAME_REWARD_RETAKES'];
// Whether users get to 'dislike' quizzes
$GAME_ALLOW_DISLIKES = $row_getVars['GAME_ALLOW_DISLIKES'];

//----------------------------------------
// Facebook variables
// Prefix with "FB_"
//----------------------------------------

// Facebook App ID
$FB_APPID = $row_getVars['FB_APPID'];
// Facebook App Secret Key
$FB_SECRET = $row_getVars['FB_SECRET'];
// Facebook Canvas URL
$FB_CANVAS = $row_getVars['FB_CANVAS'];

//----------------------------------------
// Quiz/Application variables
// Prefix with "VAR_"
//----------------------------------------

// Turn on maintenance mode
$VAR_SYSTEM_MAINTENANCE = $row_getVars['VAR_SYSTEM_MAINTENANCE'];
// Number of recommendations/popular quizzes to show
$VAR_NUM_LISTINGS = $row_getVars['VAR_NUM_LISTINGS'];
// Number of quizzes to include in the pool of popular quizzes
$VAR_NUM_POPULAR_POOL = $row_getVars['VAR_NUM_POPULAR_POOL'];
// The true URL for the application
$VAR_URL = $row_getVars['VAR_URL'];
// Whether to display the recent activity panel
$VAR_SHOW_RECENT = $row_getVars['VAR_SHOW_RECENT'];
// Whether to display the recent activity panel
$VAR_SHOW_TOPICS = $row_getVars['VAR_SHOW_TOPICS'];
// Minimum number of quiz results allowed
$VAR_QUIZ_MIN_RESULT = $row_getVars['VAR_QUIZ_MIN_RESULT'];
// Minimum number of quiz questions allowed
$VAR_QUIZ_MIN_QUESTIONS = $row_getVars['VAR_QUIZ_MIN_QUESTIONS'];
// Minimum number of quiz options allowed
$VAR_QUIZ_MIN_OPTIONS = $row_getVars['VAR_QUIZ_MIN_OPTIONS'];

mysql_free_result($getVars);
?>