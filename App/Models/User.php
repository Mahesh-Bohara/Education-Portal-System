<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/12/2018
 * Time: 9:12 PM
 */

namespace App\Model;
use PDO;
use System\Model;

class User extends Model
{
    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function studentLogin($email, $password){
        try {
            $studentPassword = md5($password);
            $stmt = $this->conn->prepare("SELECT * FROM `login` WHERE (email=:studentEmail) AND role='Student'");
            $stmt->bindParam("studentEmail", $email,PDO::PARAM_STR);
            $stmt->execute();
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1)
            {
                if($userRow['password']==$studentPassword)
                {
                    $_SESSION['epUser'] = $userRow['username'];
                    $_SESSION['epUserRole'] = $userRow['role'];
                    $this->updateLogin($userRow['email']);
                    return true;
                }
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function teacherLogin($email, $password){
        try {
            $studentPassword = md5($password);
            $stmt = $this->conn->prepare("SELECT * FROM `login` WHERE (email=:teacherEmail) AND role='Teacher'");
            $stmt->bindParam("teacherEmail", $email,PDO::PARAM_STR);
            $stmt->execute();
            $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1)
            {
                if($userRow['password']==$studentPassword)
                {
                    $_SESSION['epUser'] = $userRow['username'];
                    $_SESSION['epUserRole'] = $userRow['role'];
                    return true;
                }
            }
        }
        catch(PDOException $e) {
            echo '{"error":'. $e->getMessage() .'}';
        }
    }

    public function userInfo($username)
    {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM login WHERE username=:username");
            $stmt->bindParam("username", $username,PDO::PARAM_STR) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }


    public function userByUsername($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT login_id,username,email,role,last_login,last_activity FROM login WHERE username=:username");
            $stmt->bindParam("username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $data;
            if ($data['role'] == 'Student') {
                $extData = new Student();
                $user['info'] = $extData->studentInfo($data['email']);
            } elseif ($data['role'] == 'Teacher') {
                $extData = new Teacher();
                $user['info'] = $extData->teacherInfo($data['email']);
            }
            return $user;
        } catch (PDOException $e) {
            echo '{"error":{"text":' . $e->getMessage() . '}}';
        }
    }

    public function updateLogin($email) {
        try{
            $now = new \DateTime();
            $currentTime = $now->getTimestamp();
            //var_dump($currentTime);
            //exit;
            $sql = "UPDATE `login` SET `last_login` = :last_login WHERE `email`=:email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':last_login', $currentTime);
            $stmt->bindValue(':email', $email);
            $result = $stmt->execute();
            return $result;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function updateLastActivity($email) {
        try{
            $now = new \DateTime();
            $currentTime = $now->getTimestamp();
            $sql = "UPDATE `login` SET `last_activity` = :last_activity WHERE `email`=:email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':last_activity', $currentTime);
            $stmt->bindValue(':email', $email);
            $result = $stmt->execute();
            return $result;
        }
        catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /** chat functions */

    /**
     * @param $email -> current logged in user email
     * @return mixed
     */
    public function getChatUserList($email) {
        try{
            $sql = "SELECT * FROM `login` WHERE `email`!= :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $result = $stmt->execute();
            $data = [];
            while ($fdata=$stmt->fetchAll()) {
                for ($i=0;$i<count($fdata);$i++) {
                    $data[$i] = $this->userByUsername($fdata[$i]['username']);
                    //array_push($data,$userinfo);
                }
            }
            return $data;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getUserInfoById($user_id){
        try{
            $sql = "SELECT `username`,`email`,`role` FROM `login` WHERE `login_id`= :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $result = $stmt->execute();
            $userInfo = null;
            while($data=$stmt->fetch(PDO::FETCH_ASSOC)){
                $userInfo = $this->userByUsername($data['username']);
            }
            return $userInfo;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }



}