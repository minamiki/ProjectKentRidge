<?php
//----------------------------------------
// For game variables
// Prefix with "GAME_"
//----------------------------------------

// The base score for awarding of points
$GAME_BASE_POINT = 10;
// The multiplier amount for each increment
$GAME_MULTIPLIER = 1;
// Whether users get points for retaking quizzes
$GAME_REWARD_RETAKES = false;
// Whether users get to 'dislike' quizzes
$GAME_ALLOW_DISLIKES = false;

//----------------------------------------
// Facebook variables
// Prefix with "FB_"
//----------------------------------------

// Facebook App ID
$FB_APPID = "154849761223760";
// Facebook App Secret Key
$FB_SECRET = "26cfea224822c58cd618eae900d87f69";
// Facebook Canvas URL
$FB_CANVAS = "http://apps.facebook.com/quizroo/";

//----------------------------------------
// Quiz/Application variables
// Prefix with "VAR_"
//----------------------------------------

// Number of recommendations/popular quizzes to show
$VAR_NUM_LISTINGS = 4;
// The true URL for the application
$VAR_URL = "http://quizroo.nus-hci.com/";
// Whether to display the recent activity panel
$VAR_SHOW_RECENT = false;
// Whether to display the recent activity panel
$VAR_SHOW_TOPICS = false;
// Minimum number of quiz results allowed
$VAR_QUIZ_MIN_RESULT = 1;
// Minimum number of quiz questions allowed
$VAR_QUIZ_MIN_QUESTIONS = 1;
// Minimum number of quiz options allowed
$VAR_QUIZ_MIN_OPTIONS = 2;

?>