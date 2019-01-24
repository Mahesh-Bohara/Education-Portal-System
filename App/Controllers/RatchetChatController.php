<?php
namespace App\Controller;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class RatchetChatController implements MessageComponentInterface {
    protected $clients;
    private $dbh;
    private $users = array();

    public function __construct() {
        global $dbh, $docRoot;
        $this->clients 	= new \SplObjectStorage;
        $this->dbh 		= $dbh;
        $this->root 	= $docRoot;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        //on open function
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $id	  = $from->resourceId;
        $data = json_decode($msg, true);
        if(isset($data['data']) && count($data['data']) != 0){
            $type = $data['type'];
            $user = $data['data']['cf'];
            if($type == "register"){
                $name = htmlspecialchars($data['data']['name']);
                $this->users[$id] = array(
                    "name" 	=> $name,
                    "seen"	=> time()
                );
            }elseif($type == "send" && $user !== false){
                $msg = htmlspecialchars($data['data']['chat_message']);
                foreach ($this->clients as $client) {
                    $messageArray = array(
                        'msg_from' => [
                            'h_' => $data['data']['cf'],
                            'first_name' => $data['data']['cf_fn'],
                            'last_name' => $data['data']['cf_ln'],
                            'slImg' => $data['data']['cf_fn'][0].$data['data']['cf_ln'][0]
                        ],
                        'msg_to' => [
                            'h_' => $data['data']['ct'],
                            'first_name' => $data['data']['ct_fn'],
                            'last_name' => $data['data']['ct_ln'],
                            'slImg' => $data['data']['ct_fn'][0].$data['data']['ct_ln'][0]
                        ],
                        'message'=> "<div class='conversation-message'><div class='conversation-message-text'>" . $data['data']['chat_message'] . "</div></div>",
                        'message_'=> $data['data']['chat_message'],
                        'message_type'=>'chat-box-html'
                    );
                    $this->send($client, "message",$messageArray);
                }
            }elseif($type == "fetch"){
                $this->send($from, "fetch", $this->fetchMessages());
            }elseif($type == "fetchNM"){
                $this->send($from, "fetchNM", $this->fetchNotificationMessages());
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

    /* My custom functions */
    public function fetchMessages(){
        $sql = $this->dbh->query("SELECT * FROM `chat`");
        $msgs = $sql->fetchAll();
        return $msgs;
    }

    public function send($client, $type, $data){
        $send = array(
            "type" => $type,
            "data" => $data
        );
        $send = json_encode($send, true);
        $client->send($send);
    }
}