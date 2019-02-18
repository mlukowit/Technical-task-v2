<?php
//simple php quiz, main. (evaluation for Technical Task (v2) 

/* $_SESSION['SessionPage'] store the type of session
 * unset-0: user registration / quiz choose
 * 1--(n): question screen
 * (n+1): provide results
 * -1: error, session need to be restarted
 * -2: missing data, session cannot proceed
 */
session_start();
if (!isset($_SESSION['SessionPage'])){
    $_SESSION['SessionPage']=0;
}
if ($_SESSION['SessionPage']<0){
    $_SESSION['SessionPage']=0;
}
if (!isset($_SESSION['QuizID'])){
    $_SESSION['QuizID']=0;
}
if (!isset($_SESSION['QuizName'])){
    $_SESSION['QuizName']=0;
}
if (!isset($_SESSION['NumOfQuest'])){
    $_SESSION['NumOfQuest']=0;
}
/*API*/
/*this same file is reached by ajax requests and reply with the required information*/ 
$errorFlag = 0;

// all user interactions excluding registration
if ($_SESSION['SessionPage']>=1){
if (isset($_POST["UserID"])){
    $UserID=filter_var($_POST["UserID"],FILTER_SANITIZE_NUMBER_INT);
    }
if (isset($_POST["QuestionID"])){
    $QuestionID=filter_var($_POST["QuestionID"],FILTER_SANITIZE_NUMBER_INT);
    }
if (isset($_POST["AnswerOption"])){
    $AnswerOption=filter_var($_POST["AnswerOption"],FILTER_SANITIZE_NUMBER_INT);
    }

if (isset($_POST["QuestionText"])){
    $QuestionText=preg_replace('/[^A-Za-z0-9 \?!.;: ]/','',$_POST["QuestionText"]);
    }
    
if (isset($_POST["AnswerText"])){
    $AnswerText=preg_replace('/[^A-Za-z0-9 \?!.;: ]/','',$_POST["AnswerText"]);
    }

if (isset($UserID) && isset($QuestionID) && isset($AnswerOption) && isset($QuestionText) && isset($AnswerText)){
    if (($UserID !== 0) && ($QuestionID !== 0) && ($UserID !== '') && ($QuestionID !== '')
        && ($AnswerOption !== 0) && ($QuestionText !== 0) && ($AnswerOption !== '') && ($QuestionText !== '')
        && ($AnswerText !== 0) && ($AnswerText !== '')){
    //if these parameters are set and !=0 or '', and questions are still missing to complete the test,
    //standard users interactions are provided (questions and answers)
        if (($_SESSION['SessionPage']-1)<$_SESSION['NumOfQuest']){
        $_SESSION['SessionPage']++;
        }
    }
  } else $_SESSION['SessionPage']=-1; //if these parameters are not set or ==0 or '', there is an error
}

// first user interaction - registration and quiz choose
if (isset($_POST["QuizId"])){
    $_SESSION['QuizID']=filter_var($_POST["QuizId"],FILTER_SANITIZE_NUMBER_INT);
    }
if (isset($_POST["UserName"])){
    $UserName=preg_replace("/[^a-zA-Z ]/", "", $_POST["UserName"]);
}
if (isset($_POST["NumOfQuest"])){
    $NumOfQuest=filter_var($_POST["NumOfQuest"],FILTER_SANITIZE_NUMBER_INT);
    }

if (isset($_POST["QuizName"])){
    $_SESSION['QuizName']=preg_replace("/[^a-zA-Z0-9\.,:;? ]/", "", $_POST["QuizName"]);
}

if (isset($UserName) &&($_SESSION['SessionPage']<1)){
    if (($_SESSION['QuizID'] !== 0) && ($UserName !== 0) && ($_SESSION['QuizID'] !== '') && ($UserName !== '')){
    //if these parameters are set and !=0 or '', user is registered and quiz initialized
    $_SESSION['SessionPage']++;
    $_SESSION['NumOfQuest']=$NumOfQuest;
    $_SESSION['UserName']=$UserName;
    } else {
        $_SESSION['SessionPage']=-2; //else there is an error or missing data)
    }
}
/*END API*/

//define class for extracting and printing Quiz DropDown
class QuizSelection{
    public $QuizSelectionValue = "Please wait DB connection.";

        public function getProperty()
        {
            $NewBdConnection = new DbConnect;
            $NewBdConnection->ConnectDB();
            if (($result = $NewBdConnection->myconn->query("Select IDQuiz, QuizName, NumberOfQuest from quizname"))) {
                $QuizSelectionValue = '';
                $f=0;    
                while ($row = $result->fetch_array(MYSQLI_NUM)){
                $QuizSelectionValue .= '<option value="'.$row[0].'-'.$row[2].'">'.$row[1].'['.$row[2].']</option>';
                $f++;
                }
            } else {
                $QuizSelectionValue="noresult";
              }
            $NewBdConnection->CloseConnectDB();
            return $QuizSelectionValue;
        }
}

//define class for extracting and printing Answers options
//Uploading and recording user chosen answer
class QuizQuestion{
    public $QuestionValue = "Please wait DB connection.";
    
        public function setProperty($QuestionID,$UserID,$AnswerProgNumber,$QuestionText,$AnswerText)
        {
            $NextQuestionFlag = 0;
            $NewBdConnection = new DbConnect;
            $NewBdConnection->ConnectDB();
            if (($NewBdConnection->myconn->query("INSERT INTO `useranswers`(`UserID`, `QuestionID`, `AnswerProgNumber`, `QuestionText`, `AnswerText`, `Timestamp`) VALUES ('".$UserID."','".$QuestionID."','".$AnswerProgNumber."','".$QuestionText."','".$AnswerText."',CURRENT_TIMESTAMP)"))) {
                //new user ID created, return UserID
                 $NextQuestionFlag = 1;
            } else $NextQuestionFlag="noresult";
            $NewBdConnection->CloseConnectDB();    
            return $NextQuestionFlag;
        }
 
        public function getProperty($quizID,$SessionPage,$UserID)
        {
            $NewBdConnection = new DbConnect;
            $NewBdConnection->ConnectDB();
            if (($result = $NewBdConnection->myconn->query("Select IDprogressive, TextQuestion, Answers from questionsanswers WHERE IDquiz=".$quizID." AND IDquestion=".$SessionPage))) {
                $QuestionValue = '';
                $f=0;    
                while ($row = $result->fetch_array(MYSQLI_NUM)){
                $QuestionValue .= '<div class="am-col m12"><p>Quiz:<span id="QuizNameTxt"> '.$_SESSION['QuizName'].'</span><input type="hidden" id="QuizID" value="'.$quizID.'"></p><p><b id="Question">'.$row[1].'</b></p></div><div class="am-col m12">';
                $answers = explode("!&!", $row[2]);
                for ($k=0;$k<count($answers);$k++){
                $QuestionValue .= '<div class="am-col m6 answers" style="background-color: '.'#' .str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT).';" onclick="SelectAnswer('.$row[0].','.($k+1).','.$UserID.',event);">';
                $QuestionValue .= '<p class="answerText">'.($k+1).') '.$answers[$k].'</p></div>';
                }
                $f++;    
                }
                $QuestionValue .= '</div>';
            } else {
                $QuestionValue="noresult";
            }
            $NewBdConnection->CloseConnectDB();
            return $QuestionValue;
        }
}

//define class for managing Users. Recording to DB new user and final result.
class ManageUser{
    public $UserRecord = "Please wait DB connection.";
        public function setProperty($newval,$quizVal)
        {
            $NewBdConnection = new DbConnect;
            $NewBdConnection->ConnectDB();
            if (($NewBdConnection->myconn->query("INSERT INTO `users`(`IDuserTable`, `UserName`, `QuizID`, `QuizResults`) VALUES ('','".$newval."','".$quizVal."',0)"))) {
                //new user ID created, return UserID
                 $last_id = $NewBdConnection->myconn->insert_id;
            } else {
                $last_id="noresult";
            }
            $NewBdConnection->CloseConnectDB();    
            return $last_id;
        }
        
        public function getResult($UserID)
        {
            //extract answers and calculate result
            $NewBdConnection = new DbConnect;
            $NewBdConnection->ConnectDB();
            $Vote = 0;
            $f=0;
            if (($result = $NewBdConnection->myconn->query("
                    SELECT useranswers.AnswerProgNumber,questionsanswers.CorrectOption FROM useranswers
                    INNER JOIN questionsanswers on questionsanswers.IDprogressive = useranswers.QuestionID
                    WHERE useranswers.UserID=".$UserID))) {
                while ($row = $result->fetch_array(MYSQLI_NUM)){
                if ($row[0] === $row[1]){
                    $Vote++;
                  }
                $f++;    
                }
                $VoteResult = "<p> Dear <b>".$_SESSION['UserName']."</b>, thanks for your effort!</p><p>You replied correctly to <b>".$Vote." questions out of ".$f."</b>. Your vote is: <b>".round((($Vote/$f)*100), 1)."%</b>";
            } else {
                $VoteResult="noresult";
            }
            
            //save user results
            if (($NewBdConnection->myconn->query("UPDATE `users` SET `QuizResults` = '".(($Vote/$f)*100)."' WHERE IDuserTable = ".$UserID))) {
              //new user ID created, return UserID
              $stored = "correctly";
            } else $stored="not correctly";
            $NewBdConnection->CloseConnectDB();    
            return $VoteResult./*" stored: ".$stored.*/"<br><br><button id='Restart' class='submit' onclick='location.reload();'>Try Another</button>";
        }
    }
    
    //include DB connection class
    include_once "db/DBconnect.php";

//Main Program
   if ($_SESSION['SessionPage'] === -2){//probably missing data
        //no response, will be asked again
        } else if ($_SESSION['SessionPage'] === -1){//error
        echo "<script>location.reload();</script>";
        $errorFlag=1;
        } else if ($_SESSION['SessionPage'] === 0){//first connection, user registration

        // assign base HTML code    
$htmlStart = '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" property="og:description" content=" Insert you name, and select a quiz from the list. i">
<meta property="og:type" content="website">
<title>Technical Task (v2)</title>
<link rel="stylesheet" href="styles/quiz.css">
</head>
<body>
<ul id="topbar"><li class="centerAlign"><color=FFFF><h1 id="TitleTopBar">Technical Task (v2)</h1></color></li><li class="centerAlign"></li></ul>
<div id="FrameQuiz" class="am-col m12">
';

$htmlEnd = '
</div>
<div class="footer">
  <center><p>Technical Task (V2) Simple Quiz.</p></center>
</div>
</body>
</html>
';

$scriptBlock='
<script>
//first screen
function SubmitUserData(){
        var formData = new FormData();
        var xhr = new XMLHttpRequest();
        formData.append("QuizId", document.getElementById("QuizSelector").value.split("-")[0]);
        var QuizNameText = document.getElementById("QuizSelector").options[document.getElementById("QuizSelector").selectedIndex].text; 
        QuizNameText=QuizNameText.substring(0,QuizNameText.indexOf("["));
        formData.append("QuizName", QuizNameText);
        formData.append("NumOfQuest", document.getElementById("QuizSelector").value.split("-")[1]);
        formData.append("UserName", document.getElementById("UserName").value);
        
        
        xhr.ontimeout = function () {
        };
            xhr.onreadystatechange  = function() {
                    if ((xhr.readyState === 4) && (xhr.status === 200)) {
                    var response = xhr.response;
                        if ((response != "") && (response != "error")){
                            document.getElementById("msgBOX").style.visibility="hidden";
                            document.getElementById("msgBOX").style.color="black";
                            document.getElementById("FrameQuiz").innerHTML = response;
                        } else {
                        document.getElementById("msgBOX").innerHTML="<b>Please set a username and a quiz.</b>";
                        document.getElementById("msgBOX").style.color="red";
                        document.getElementById("msgBOX").style.visibility="";
                            }
                    }
        };

        
        xhr.open("POST", "index.php", true);
        xhr.timeout = 300000; // time in milliseconds
        xhr.send(formData);
}

//quiz screen
function SelectAnswer(QuestionID,AnswerOption,UserID,e){
     var formData = new FormData();
        var xhr = new XMLHttpRequest();
        formData.append("QuestionID", QuestionID);
        formData.append("UserID", UserID);
        formData.append("AnswerOption", AnswerOption);
        AnswText=e.target.firstChild.innerHTML.substring((e.target.firstChild.innerHTML.indexOf(")"))+1);
        formData.append("AnswerText", AnswText);
        formData.append("QuestionText", document.getElementById("Question").innerHTML);

        xhr.ontimeout = function () {
        };
            xhr.onreadystatechange  = function() {
                    if ((xhr.readyState === 4) && (xhr.status === 200)) {
                    var response = xhr.response;
                        if ((response != "") && (response != "error")){
                            document.getElementById("msgBOX").style.visibility="hidden";
                            document.getElementById("FrameQuiz").innerHTML = response;
                        } else {
                        document.getElementById("msgBOX").innerHTML="<b>Probable DB connection error. retry.</b>";
                        document.getElementById("msgBOX").style.visibility="";
                            }
                    }
        };                        
        
        xhr.open("POST", "index.php", true);
        xhr.timeout = 300000; // time in milliseconds
        xhr.send(formData);       
}
</script>
';
   //end of assign Base HTML code

    //extract available quiz
    $QuizSelectionValueNew = new QuizSelection;
    $quizDropDown=$QuizSelectionValueNew->getProperty();
    if ($quizDropDown==="noresult"){
        $errorFlag=1;
    } else {
    $quizDropDown='<select id="QuizSelector"><option value="">choose one..</option>'.$quizDropDown."</select>";
    $html = '
    <p>Insert your name, and select a quiz from the list. (the number of question is shown).</p>
    <div class="am-col m6"><input type="text" id="UserName" placeholder="Your Name"></div><div class="am-col m6">
    ';
    $html.=$quizDropDown;
    $html.="</div><div class='am-col m12 centerAlign'><button id='submitForm' class='submit' onclick='SubmitUserData();'>start</button><p id='msgBOX'></p><progress value='0' max='100' id='progressBar'></progress><p id='ProgressInfo'>step 0</p></div>";
    }

    
//print Welcome Page output
echo $htmlStart.$html.$scriptBlock.$htmlEnd;
} else if ($_SESSION['SessionPage']===1){
    //register user
    $UserNew = new ManageUser;
    $UserNewID=$UserNew->setProperty($UserName,$_SESSION['QuizID']);
    if ($UserNewID==="noresult"){
        $errorFlag=1;
    } else {
    //propose first question if user has been registered correctly
    $QuizQuestionNew = new QuizQuestion;
    $quizQuestion=$QuizQuestionNew->getProperty($_SESSION['QuizID'],$_SESSION['SessionPage'],$UserNewID);
    if ($quizQuestion==="noresult"){
        $errorFlag=1;
    } else {
            echo "<p>Just click onto the answer box to chose one. At the end of the quiz you will have your score.</p>"
            .$quizQuestion."<div class='am-col m12 centerAlign'><p id='msgBOX'></p><progress value='".(($_SESSION['SessionPage']/$_SESSION['NumOfQuest'])*100)."' max='100' id='progressBar'></progress><p  id='ProgressInfo'>step ".$_SESSION['SessionPage']." out of ".$_SESSION['NumOfQuest']."</p></div>";    
        }
    }
} else if ($_SESSION['SessionPage']>1) {
    //record last user interaction in the DB
    $QuizQuestionNew = new QuizQuestion;
    $insertNewRecord=$QuizQuestionNew->setProperty($QuestionID,$UserID,$AnswerOption,$QuestionText,$AnswerText);
    if ($insertNewRecord==="noresult"){
        $errorFlag=1;
    } else {
    //if everything has been recorded, propose next question till reaching the last one
    if(($_SESSION['SessionPage']!==0) && ($_SESSION['NumOfQuest']!==0) && (($_SESSION['SessionPage']-1)==$_SESSION['NumOfQuest'])){
    //case of last user interaction (last question replied), calculate result
    $UserResult = new ManageUser;
    $UserResultPrint=$UserResult->getResult($UserID);
    echo $UserResultPrint;
    session_destroy();
    } else {//standard case, new question
        $quizQuestion=$QuizQuestionNew->getProperty($_SESSION['QuizID'],$_SESSION['SessionPage'],$UserID);    
        if ($quizQuestion==="noresult"){
        $errorFlag=1;
    } else {
        echo $quizQuestion."<div class='am-col m12 centerAlign'><p id='msgBOX'></p><progress value='".(($_SESSION['SessionPage']/$_SESSION['NumOfQuest'])*100)."' max='100' id='progressBar'></progress><p id='ProgressInfo'>step ".$_SESSION['SessionPage']." out of ".$_SESSION['NumOfQuest']."</p></div>";
        }
      }
    }
 }

//end main program

if ($errorFlag === 1){
session_destroy();    
    }
?>
