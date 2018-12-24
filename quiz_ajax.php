<?php
ini_set('display_errors', 1);
require_once 'inc/db.php';
require_once 'class.Quiz.php';
$quiz = new Quiz($db);
$out = array();
if(isset($_POST['restart'])){
    $quiz->endQuiz();
    die();
}
if(isset($_POST['over'])){
// -1 -> show register page , 1->over(show results) , 0->running quiz
    if($quiz->isRunning()){
        $over = $quiz->isOver();
        $out['over'] = $over;
        if($over){
            $out['results'] = $quiz->getResults();
        }
    } else {
        $out['over'] = '-1';
    }
    die(json_encode($out));
}

if(!$quiz->isRunning()){
    $nick = isset($_POST['nick'])? $_POST['nick'] : 'Guest';
    $category = isset($_POST['category']) ? $_POST['category'] : "0";
    $category = explode(',',$category);
    $quiz->startQuiz($nick,$category);
    $out['question'] = $quiz->showQuestion();
    $out['results'] = $quiz->getResults();
} else {
    if(isset($_POST['answer'])){
        $quiz->submitAnswer($_POST['answer']);
    } else {
        $quiz->submitAnswer();
    }
    if($_SESSION['quiz']['total']==10){
        $quiz->calculateScore();
    }
    if(!$quiz->isOver() && $_SESSION['quiz']['total'] <10){
         $que = $quiz->showQuestion();
         if($que)
             $out['question'] = $que;
         else{
             $quiz->over();
         }
    } else {
         $quiz->over();
    }
    $out['results'] = $quiz->getResults();
    $out['results']['last'] = $quiz->getLast();
}

echo json_encode($out);
