<?php

namespace App\Model;


use PDO;
use System\Model;



class Chat extends Model
{
    protected $conn;

    public function __construct()
    {
        $this->conn = self::getDB();
    }

    public function FetchChatsB2U($user1, $user2) {
        try{
            $stmt = $this->conn->prepare("SELECT * FROM chat WHERE (chat_from=:user_1 AND chat_to=:user_2) OR (chat_from=:user_2 AND chat_to=:user_1) ORDER BY chat_id");
            $stmt->bindParam("user_1", $user1,PDO::PARAM_INT) ;
            $stmt->bindParam("user_2", $user2,PDO::PARAM_INT) ;
            $stmt->execute();
            $chats = [];
            $chat_S = false;
            while($data=$stmt->fetchAll()) {
                $count = count($data);
                $chats['chats'] = $data;
                $chats['csu__'] = null;
                for($i=0;$i<$count;$i++) {
                    if ($chat_S == false) {
                        $chats['csu__'] = (new User())->getUserInfoById($data[$i]['chat_from']);
                        $chat_S = true;
                    }
                    $chats['chats'][$i]['chat_to'] = (new User())->getUserInfoById($user2);
                }
            }

            return $chats;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getLastChatOfUser($username,$clu_id){
        try{
            $user_id = (new User())->userInfo($username);
            $user_id = $user_id['login_id'];
            $stmt = $this->conn->prepare("SELECT * FROM chat WHERE (chat_from=:user_id AND chat_to=:clu_id) OR (chat_from=:clu_id AND chat_to=:user_id) ORDER BY chat_id DESC LIMIT 1");
            $stmt->bindParam("user_id", $user_id,PDO::PARAM_INT) ;
            $stmt->bindParam("clu_id", $clu_id,PDO::PARAM_INT) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getNotificationsChats($from, $clu_id){
        try{
            $user_id = (new User())->userInfo($from);
            $user_id = $user_id['login_id'];
            $stmt = $this->conn->prepare("SELECT * FROM chat WHERE (chat_from=:user_id AND chat_to=:clu_id) OR (chat_from=:clu_id AND chat_to=:user_id) ORDER BY chat_id DESC LIMIT 1");
            $stmt->bindParam("user_id", $user_id,PDO::PARAM_INT) ;
            $stmt->bindParam("clu_id", $clu_id,PDO::PARAM_INT) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            if ($data){
                if ($data['chat_from'] == $user_id) {
                    $data['chat_from'] = (new User())->getUserInfoById($user_id);
                } elseif($data['chat_to'] == $user_id) {
                    $data['chat_to'] = (new User())->getUserInfoById($user_id);
                }
            }
            return $data;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getLastChatSentToUser($username){
        try{
            $user_id = (new User())->userInfo($username);
            $user_id = $user_id['login_id'];
            $stmt = $this->conn->prepare("SELECT * FROM chat WHERE chat_to=:user_id ORDER BY chat_id DESC LIMIT 1");
            $stmt->bindParam("user_id", $user_id,PDO::PARAM_INT) ;
            $stmt->execute();
            $data=$stmt->fetch(PDO::FETCH_ASSOC);
            return $data;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }


    public function getLastChatsOfUser($username){
        try{
            $user_id = (new User())->userInfo($username);
            $user_id = $user_id['login_id'];
            $stmt = $this->conn->prepare("SELECT `login_id`, `username` FROM login WHERE username!=:username");
            $stmt->bindParam("username", $username,PDO::PARAM_STR) ;
            $stmt->execute();
            $lChats = [];
            while($data=$stmt->fetchAll()) {
                $count = ((count($data)<10) ? count($data) : 10);
                for ($i = 0; $i < $count; $i++) {
                    $lChats[$data[$i]['login_id']] = $this->getLastChatSentToUser($data[$i]['username']);
                }
            }
            return $lChats;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * @param $username -> current logged user username
     * @return mixed
     */
    public function getLastChatOfUsers($username){
        try{
            $user_id = (new User())->userInfo($username);
            $user_id = $user_id['login_id'];
            $fLastChats = [];
            $stmt = $this->conn->prepare("SELECT `login_id`, `username` FROM login WHERE username!=:username");
            $stmt->bindParam("username", $username,PDO::PARAM_STR) ;
            $stmt->execute();
            while($data = $stmt->fetchAll()) {
                $count = ((count($data)<10) ? count($data) : 10);
                for ($i=0;$i<$count;$i++){
                    $fLastChats[$data[$i]['login_id']] = $this->getLastChatOfUser($data[$i]['username'],$user_id);
                }
            }
            return $fLastChats;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getNotificationMessages($username){
        try{
            $user_id = (new User())->userInfo($username);
            $user_id = $user_id['login_id'];
            $fLastChats = [];
            $stmt = $this->conn->prepare("SELECT `login_id`, `username` FROM login WHERE username!=:username");
            $stmt->bindParam("username", $username,PDO::PARAM_STR) ;
            $stmt->execute();
            while($data = $stmt->fetchAll()) {
                $count = ((count($data)<10) ? count($data) : 10);
                for ($i=0;$i<$count;$i++){
                    if ($this->FetchChatsB2U($data[$i]['login_id'],$user_id)){
                        $fLastChats[$data[$i]['login_id']] = $this->getNotificationsChats($data[$i]['username'],$user_id);
                    }
                }
            }
            return $fLastChats;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function addChat() {
        try{
            $stmt = $this->conn->prepare("INSERT INTO `chat`(`chat_from`,`chat_to`,`chat_content`,`timestamp`) VALUE (:from,:to,:chat,:timestamp)");
            $stmt->bindParam(":from", $this->from,PDO::PARAM_INT) ;
            $stmt->bindParam(":to", $this->to,PDO::PARAM_INT) ;
            $stmt->bindParam(":chat", $this->chat,PDO::PARAM_STR) ;
            $stmt->bindParam(":timestamp", $this->timestamp,PDO::PARAM_INT) ;
            $result = $stmt->execute();
            return $result;
        }
        catch (\PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }



}