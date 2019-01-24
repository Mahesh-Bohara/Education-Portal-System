<?php

namespace App\Controller;

use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller {
    
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (!$this->isLoggedin()) {
            header('Location: account/login');
        }

        if ($this->isRole('Teacher')) {
            header('Location: /t/home');
        } elseif ($this->isRole('Student')) {
            header('Location: /s/home');
        }


        return View::renderTemplate('app/index.html');
    }
}