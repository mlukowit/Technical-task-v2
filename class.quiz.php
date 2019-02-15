<?php
//options are imploded with $$
class Quiz {
    protected $db;
    public function __construct(PDO $db){
        $this->db= $db;
    }

    public function startQuiz($nickname='user',$categories=array()){
        if(isset($_SESSION['quiz'])){
            //quiz already in progress. Cannot play more than 1 quiz at a time
            //show another question in the already set session.

        }
        //$name = (User::getUserId()) ? User::getField('name') : $name ;
        $name = "anonymous";
        $_SESSION['quiz'] = array(
            'total'     => 0 ,
            'correct'   => 0 ,
            'wrong'     => 0 ,
            'categories'=> $categories ,
            'qids'      => array(0),
            'time'      => 0 ,
            'nick'      => $nickname,
            'startTime' => time(),
            'over'      => 0
        );
    }
    public function over(){
        $_SESSION['quiz']['over']=1;
    }
    public function isOver(){
        return $_SESSION['quiz']['over'];
    }
    public function isRunning(){
        return isset($_SESSION['quiz']);
    }
    public function endQuiz(){
        $_SESSION['quiz']=array();
        unset($_SESSION['quiz']);
    }
    public function showStatus(){
        showarray($_SESSION);
    }
    public function getResults(){
        $results = array(   'total' => $_SESSION['quiz']['total'],
                            'correct'=>$_SESSION['quiz']['correct'],
                            'wrong' =>$_SESSION['quiz']['wrong'],
                            'time'  =>$_SESSION['quiz']['time']
        );
        return $results;
    }
    public function submitAnswer($answer = null){
        if($answer && isset($_SESSION['quiz']['answer'])){
            if($answer == $_SESSION['quiz']['answer']){
                $_SESSION['quiz']['correct']++;
                $_SESSION['quiz']['last'] = 1;
            } else {
                $_SESSION['quiz']['wrong']++;
                $_SESSION['quiz']['last'] = -1;
            }
        } else{
            $_SESSION['quiz']['last'] = 0;
        }
        $_SESSION['quiz']['time'] = time() - $_SESSION['quiz']['startTime'];

    }
    public function getLast(){
       $last = $_SESSION['quiz']['last'];
       unset($_SESSION['quiz']['last']);
       return $last;
    }
    // call showQuestion immediately after the submitAnswer to overwrite the answer
    // and get the next question.

    public function getCategories(){
        $q = $this->db->prepare("SELECT id,title FROM categories");
        $q->execute();
        return $q->fetchAll(PDO::FETCH_OBJ);
    }
    public function showQuestion2(){
        $qids = implode(',',$_SESSION['quiz']['qids']);
        $categories = implode(',',$_SESSION['quiz']['categories']);
        $random = $this->db->query("SELECT RAND()*MAX(id) as random FROM quiz")->fetchObject()->random;
        if($categories)
            $sql = "SELECT * FROM quiz WHERE id >= $random AND id NOT IN ($qids) AND category IN ($categories) LIMIT 1";
        else
            $sql = "SELECT * FROM quiz WHERE id >= $random AND id NOT IN ($qids) LIMIT 1";
        $q = $this->db->prepare($sql);
        $q->execute();
        if ($q->rowCount()){
            $r = $q->fetchObject();
            $_SESSION['quiz']['answer'] = $r->answer;
            $_SESSION['quiz']['qids'][] = $r->id;
            $_SESSION['quiz']['total']++;
            return $r;
        }
        if($categories)
            $sql = "SELECT * FROM quiz WHERE id < $random AND id NOT IN ($qids) AND category IN ($categories) LIMIT 1";
        else
            $sql = "SELECT * FROM quiz WHERE id < $random AND id NOT IN ($qids) LIMIT 1";
        $q = $this->db->prepare($sql);
        $q->execute();
        if ($q->rowCount()){
            $r = $q->fetchObject();
            $_SESSION['quiz']['answer'] = $r->answer;
            $_SESSION['quiz']['qids'][] = $r->id;
            $_SESSION['quiz']['total']++;
            return $r;
        }
        unset($_SESSION['quiz']['answer']);
        return null;
        /*          ALTERNATIVE ALGORITHM (NOT RELIABLE)
        echo $sql = "SELECT q.* FROM quiz q
                JOIN (SELECT (RAND()*(SELECT MAX(id) FROM quiz)) AS id)  r
                WHERE q.id >= r.id AND q.id NOT IN ($qids) AND q.category IN ($categories)
                ";
        $q = $this->db->prepare($sql);
        $q->execute();
        if ($q->rowCount()){
            $r = $q->fetchObject();
            return $r;
        } else {
            $sql = "SELECT q.* FROM quiz q
                JOIN (SELECT (RAND()*(SELECT MAX(id) FROM quiz)) AS id)  r
                WHERE q.id < r.id AND q.id NOT IN ($qids) AND q.category IN ($categories)
                ";
            $q= $this->db->prepare($sql);
            $q->execute();
            if($q->rowCount()){
                $r = $q->fetchObject();
                $_SESSION['quiz']['answer'] = $r->answer;
                $_SESSION['quiz']['qids'][] = $r->id;
                return $r;
            }
        }
        return null;
        */
    }
    public function showQuestion(){
        $qids = implode(',',$_SESSION['quiz']['qids']);
        $categories = implode(',',$_SESSION['quiz']['categories']);
        $random = $this->db->query("SELECT RAND()*MAX(id) as random FROM quiz_category")->fetchObject()->random;
        if($categories)
                $sql = "SELECT q.id,q.question,q.options,q.answer FROM quiz q
             JOIN (SELECT qid FROM quiz_category
            WHERE qid NOT IN ($qids) AND cid IN ($categories) ORDER BY rand() LIMIT 1) c
            ON c.qid = q.id LIMIT 1";
        else
            $sql = "SELECT q.id,q.question,q.options,q.answer FROM quiz q
         JOIN (SELECT qid FROM quiz_category
        WHERE qid NOT IN ($qids) ORDER BY rand() LIMIT 1) c
        ON c.qid = q.id LIMIT 1";

        $q = $this->db->prepare($sql);
        $q->execute();
        if ($q->rowCount()){
            $r = $q->fetchObject();
            $_SESSION['quiz']['answer'] = $r->answer;
            $_SESSION['quiz']['qids'][] = $r->id;
            $_SESSION['quiz']['total']++;
            return $r;
        }
        unset($_SESSION['quiz']['answer']);
        return null;
    }
    public function addQuestion($question,$options,$answer,$categories){
        $q= $this->db->prepare("INSERT INTO quiz(question,options,answer)
        VALUES (?,?,?)");
        $q->execute(array($question,$options,$answer));
        $p = $this->db->prepare("INSERT INTO quiz_category(qid,cid) VALUES(?,?)");
        $id = $this->db->lastInsertId();
        $cats = explode(',',$categories);
        foreach($cats as $c){
            if($c > 0)
                $p->execute(array($id,$c));
        }
        return $id;
    }
    public function editQuestion($question,$options,$answer,$categories,$id){
        $q= $this->db->prepare("UPDATE quiz SET question=?,options=?,answer=? WHERE id = ?");
        if($q->execute(array($question,$options,$answer,$id))){
            $q2 = $this->db->prepare("DELETE FROM quiz_category WHERE qid=?");
            $q2->execute(array($id));
            $p = $this->db->prepare("INSERT INTO quiz_category(qid,cid) VALUES(?,?)");
            $cats = explode(',',$categories);
            foreach($cats as $c){
                if($c > 0)
                    $p->execute(array($id,$c));
            }
            return $id;
        }
        return false;
    }
    public function calculateScore(){
        $nick = $_SESSION['quiz']['nick'];
        $user= 'player'; 
        $c = $_SESSION['quiz']['correct'];
        $w = $_SESSION['quiz']['wrong'];
        $t = $_SESSION['quiz']['total'];
        $time = $_SESSION['quiz']['time'];
        $score = 500*($c*2-$w)/(50+$time);
        $q = $this->db->prepare("INSERT INTO quiz_scores(nick,user,correct,wrong,total,time,score) VALUES(?,?,?,?,?,?,?)");
        $q->execute(array($nick,$user,$c,$w,$t,$time,$score));
    }
}
