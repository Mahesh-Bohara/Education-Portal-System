<?php

namespace App\Controller\Teacher;

use App\Model\Portal;
use App\Model\User;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller {

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $user = $this->getUser();
        //var_dump($user);
        if (!$this->isLoggedin()) {
            header('Location: /account/login');
        }
        if (!$this->isRole('Teacher')) {
            header('Location: /');
        }
        $data = new Portal();
        $dataCount['student'] =  $data->count('Student');
        $dataCount['notes'] =  $data->countPortal('material');
        $dataCount['qna'] =  $data->countPortal('qna');

        return View::renderTemplate('teacher/index.html.twig', array(
            'title' => 'Home',
            'user' => $user,
            'count' => $dataCount

        ));
    }
}

// merge data should like

/*
 * array(5) {
  ["login_id"]=>
  string(1) "1"
  ["username"]=>
  string(11) "testtest373"
  ["email"]=>
  string(16) "test@example.com"
  ["role"]=>
  string(7) "Student"
  ["last_login"]=>
  string(10) "1544666109"
}
 */