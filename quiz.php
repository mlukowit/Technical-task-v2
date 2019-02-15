<?php
require_once 'inc/db.php';
require_once 'class.Quiz.php';
$quiz = Test($db);
?>
<link href="css/bootstrap.min.css" rel="stylesheet"/>
<link href="css/bootstrap-glyphicons.css" rel="stylesheet" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.flipcountdown.js"></script>
<link href="css/jquery.flipcountdown.css" rel="stylesheet"/>

<div class="col-md-3"></div>
<div class="col-md-6" style="margin:auto;padding-bottom:10px;border:3px solid #006ddb">
<div class="quizHeader">Test Asscessment</div>
<div class="quizRegister">
<form onsubmit="event.preventDefault();">

    <?php
    if(true)
        echo "<div class=form-group><label class='label-input' for=nick >Enter Name: </label> <input id=name placeholder='Enter your Name '/></div>"; ?>
    <label class="label-input">Select Categories :</label>
    <label><input type=checkbox value=0 checked/>All</label>
    
    <?php
    foreach($quiz->getCategories() as $category){
        echo "<label><input type=checkbox value={$category->id} />{$category->title}</label>";
    }
?>
    <div class="text-center">
	<input id="startQuiz" type="button" value="Start Test" class="block btn btn-danger" />
    </div>
</form>
</div>
<div class="quiz">
    <div class="score">
        <span id="correctL">Correct:</span><span id="correct"></span>
        <span id="wrongL">Wrong:</span><span id="wrong"></span>
        <span id="timeL">Time:</span><span class="time"></span>
        <a id="Qrestart" class="btn btn-danger pull-right">Restart Quiz</a>
    </div>
    <div class="status"></div>
    <div class="clearfix"></div>
    <div class="quizarea">
        <div class="question"></div>
        <div class="options"></div>
        <a id="Qskip" class=" btn btn-warning">Skip</a>
        <a id="Qanswer" class="pull-right btn btn-success">Answer</a>
    </div>
</div>
<div class="quizResults">
    <h2 style="text-align: center;">Results</h2>
    <div class="block" id="resultMsg"></div>
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div>Total: <span id="total"></span></div>
        <div>Correct: <span id="correct"></span></div>
        <div>Wrong: <span id="wrong"></span></div>
        <div>Skipped: <span id="skipped"></span></div>
        <div>Time taken: <span id="time"></span></div>
        <div>Score :<span id="score"></div>
    </div>
    <div class="col-md-2"></div>
    <div class="block"></div>
    <button id="restartQuiz" class="btn btn-default pull-right">Restart Quiz !</button>
    <div class="block"></div>
</div>
</div>
<script src="js/quiz.js"></script>
<link href="css/quiz.css" rel="stylesheet" />
