<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller;

use App\Model\Chat;
use App\Model\Portal;
use App\Model\User;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller {

    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (!$this->isLoggedin()) $this->redirect('/account/login');

        if ($user['role'] == 'Teacher') {
            $this->handleSecurity('Teacher');
        } elseif ($user['role'] == 'Student') {
            $this->handleSecurity('Student');
        }

        $chatUserList = (new User())->getChatUserList($user['email']);
        $lastChatsSU = (new Chat())->getLastChatOfUsers($user['username']);

        $this->updateLastActivity(); //this line helps user to know who is online

        return View::renderTemplate('app/chat.html.twig', array(
            'title' => 'Chat',
            'user' => $user,
            'users' => $chatUserList,
            'LChats' => $lastChatsSU,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function chat($username) {
        $user = $this->getUser();
        if (!$this->isLoggedin()) $this->redirect('/account/login');

        if ($user['username'] == $username) {
            $this->redirect('/');
        }
        if (!$ChatUserInfo = (new User)->userByUsername($username)) {
            throw new \Exception('No route matched.', 404);
        }

        $chatUserList = (new User())->getChatUserList($user['email']);
        $lastChatsSU = (new Chat())->getLastChatOfUsers($user['username']);

        $chats = (new Chat())->FetchChatsB2U($user['login_id'],(new User())->userInfo($username)['login_id']);
        
        $this->updateLastActivity();

        return View::renderTemplate('app/chat.single.html.twig', array(
            'title' => 'Chat',
            'user' => $user,
            'chats' => $chats,
            'chatUser' => $ChatUserInfo,
            'users' => $chatUserList,
            'LChats' => $lastChatsSU,
            'messages' => $this->getAllFlashes()
        ));
    }

}