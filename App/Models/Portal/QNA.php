<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Model\Portal;

use PDO;
use System\Model;
use App\Model\User;

class QNA extends Model {

    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function addQuestion($title, $question, $submitted_by)
    {
        $qna['title'] = $title;
        $qna['body'] = $question;
        $qna = json_encode($qna);
        try {
                $sql = "INSERT INTO `qna` (`question`, `qna_by`) VALUES (:question, :sub_by)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':question', $qna);
                $stmt->bindParam(':sub_by', $submitted_by);
                $result = $stmt->execute();
                return $result;
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }


    public function submitAnswer($answer, $submitted_by, $qid)
    {
//        $qna['title'] = $title;
//        $qna['body'] = $question;
//        $qna = json_encode($qna);
        $ctime = time();
        try {
            $sql = "INSERT INTO `qna` (`question_id`, `has_answer`, `answer`, `qna_by`, `answer_at`) VALUES (:qid, 'Yes', :answer, :sub_by, :ans_at)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':qid', $qid);
            $stmt->bindParam(':answer', $answer);
            $stmt->bindParam(':sub_by', $submitted_by);
            $stmt->bindParam(':ans_at', $ctime);
            $result = $stmt->execute();
            return $result;
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function updateQuestion($title, $question, $submitted_by, $id)
    {
        $qna['title'] = $title;
        $qna['body'] = $question;
        $qna = json_encode($qna);
        try {
            $sql = "UPDATE `qna` SET `question`=:question, `qna_by`=:sub_by WHERE qna_id=:id ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':question', $qna);
            $stmt->bindParam(':sub_by', $submitted_by);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()){
                return true;
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function getQuestions()
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM qna WHERE `has_answer` IS NULL");
            $stmt->execute();
            while($data=$stmt->fetchAll()){
                for ($i=0;$i<count($data);$i++) {
                    $decodeQNA = json_decode($data[$i]['question'],true);
                    $data[$i]['title'] = $decodeQNA['title'];
                    $data[$i]['body'] = $decodeQNA['body'];
                    unset($data[$i]['question']);
                    $data[$i]['qna_by'] = (new User())->getUserInfoById($data[$i]['qna_by']);
                    if (strlen($decodeQNA['body']) > 200)
                    {
                        $lastPos = (200 - 3) - strlen($data[$i]['body']);
                        $data[$i]['body'] = substr($data[$i]['body'], 0, strrpos($data[$i]['body'], ' ', $lastPos)) . '...';
                    }
                }
                return $data;
            }
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getQuestion($id)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM qna WHERE `qna_id`=:id");
            $stmt->bindParam("id", $id,PDO::PARAM_STR) ;
            $stmt->execute();
            while($data=$stmt->fetch(PDO::FETCH_ASSOC)){
                $decodeQNA = json_decode($data['question'],true);
                $data['title'] = $decodeQNA['title'];
                $data['body'] = $decodeQNA['body'];
                unset($data['question']);
                $data['qna_by'] = (new User())->getUserInfoById($data['qna_by']);
                return $data;   
            }
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function deleteQuestion($id)
    {
        try{
            $stmt = $this->conn->prepare("DELETE FROM qna WHERE qna_id=:id");
            $stmt->bindParam("id", $id,PDO::PARAM_STR);
            if ($stmt->execute()) {
                return true;
            }
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getAnswers($id)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM qna WHERE `has_answer`='Yes' AND `question_id`=:qid");
            $stmt->bindParam("qid", $id,PDO::PARAM_INT);
            $stmt->execute();
            while($data=$stmt->fetchAll()){
                for ($i=0;$i<count($data);$i++) {
//                    $decodeQNA = json_decode($data[$i]['question'],true);
//                    $data[$i]['title'] = $decodeQNA['title'];
//                    $data[$i]['body'] = $decodeQNA['body'];
//                    unset($data[$i]['question']);
                    $data[$i]['qna_by'] = (new User())->getUserInfoById($data[$i]['qna_by']);
//                    if (strlen($decodeQNA['body']) > 200)
//                    {
//                        $lastPos = (200 - 3) - strlen($data[$i]['body']);
//                        $data[$i]['body'] = substr($data[$i]['body'], 0, strrpos($data[$i]['body'], ' ', $lastPos)) . '...';
//                    }
                }
                return $data;
            }
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

}