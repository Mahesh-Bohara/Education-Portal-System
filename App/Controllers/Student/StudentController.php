<?php

namespace App\Controller\Student;

use App\Model\Portal;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller {

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $this->handleSecurity('Student');

        $notes = (new Portal())->getNotesByLatest();
        $assignments = (new Portal())->getAssignmentsByLatest();

        return View::renderTemplate('student/index.html.twig', array(
            'title' => 'Home',
            'user' => $user,
            'notes' => $notes,
            'assignments' => $assignments

        ));
    }
}