<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/14/2018
 * Time: 9:49 AM
 */

namespace App\Controller\Teacher;


use System\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use System\View;

class CourseController extends Controller
{
    public function indexAction()
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        if (isset($_POST['assignmentSubmit'])) {

        }

        return View::renderTemplate('teacher/course.html.twig', array(
            'title' => 'Course',
            'user' => $user
        ));
    }

    public function addAction() {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();


        return View::renderTemplate('teacher/add/note.html.twig', array(
            'title' => 'Add Course',
            'user' => $user
        ));
    }
    public function deleteAction() {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();


    }


}