<?php

namespace App\Controller;

use App\Model\Chat;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AjaxController extends Controller {

    public function index() {

    }

    public function ajaxAction($ajax){
        $user = $this->getUser();
        $request = $this->getRequest();
        foreach ($query = $request->get("q") as $key => $data) {
            if ($key.'->'.$data == "fn->nt") {
                $messages = (new Chat())->getNotificationMessages($user['username']);
                if ($messages) {
                    foreach ($messages as $ukey => $value) {
                        if ($value['chat_from'] == $user['login_id']){
                            $username = $value['chat_to']['username'];
                            $fullname = $value['chat_to']['info']['first_name'].' '.$value['chat_to']['info']['last_name'];
                            $snImg = $value['chat_to']['info']['first_name'][0].$value['chat_to']['info']['last_name'][0];
                            $mm = "You : ";
                        } else {
                            $username = $value['chat_from']['username'];
                            $fullname = $value['chat_from']['info']['first_name'].' '.$value['chat_from']['info']['last_name'];
                            $snImg = $value['chat_from']['info']['first_name'][0].$value['chat_from']['info']['last_name'][0];
                            $mm = null;
                        }

                        echo '<a href="/chat/'.$username.'" class="dropdown-item">
                                    <div class="tile tile-circle bg-green"> '.$snImg.' </div>
                                    <div class="dropdown-item-body">
                                        <p class="subject"> '.$fullname.' </p>
                                        <p class="text text-truncate"> '.$mm.$value['chat_content'].' </p><span class="date">'.$this->time_elapsed_string('@'.$value['timestamp']).'</span>
                                    </div>
                                </a>';
                    }
                } else {
                    echo 'No any messages.';
                }
            };
        }
    }

    public function addAction(){
        $user = $this->getUser();
        $request = $this->getRequest();
        $addChat = new Chat();
        $addChat->from = $request->get('cf');
        $addChat->to = $request->get('ct');
        $addChat->chat = $request->get('chat_message');
        $addChat->timestamp = time();
        $data = [];
        if ($addChat->addChat()) {
            $data['result'] = 'success';
        }
        return json_encode($data);
    }

}