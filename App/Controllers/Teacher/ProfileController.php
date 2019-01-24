<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Teacher;

use App\Model\User;
use System\Controller;
use System\Error;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//THis is page is under construction

class ProfileController extends Controller {

    public function indexAction($username)
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        if ($user['username'] == $username) {
            //logged in user profile
            $puser = $user;
        } else {
            $puser = (new User())->userByUsername($username);
            if (!$puser) {
                throw new \Exception('No route matched.', 404);
            }
            echo 'other user';
            exit;
        }

        return View::renderTemplate('teacher/profile/profile.html.twig', array(
            'title' => $user['username'],
            'user' => $user,
            'puser' => $puser,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function addAction(Request $request) {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        if (isset($_POST['assignmentSubmit'])) {
            $title = $request->get('title');
            $assignment = $request->get('assignment');
            $addAssignment = new Assignment();
            if ($addAssignment->addAssignment($title, $assignment, $user['login_id'])) {
                $this->addFlash('success', 'New assignment added successfully.');
                $this->redirect('/t/assignments');
            }
        }

        return View::renderTemplate('teacher/add/assignment.html.twig', array(
            'title' => 'Add Assignment',
            'user' => $user
        ));
    }

    public function editAction($id){
        $this->handleSecurity('Teacher');
        $user = $this->getUser();
        $assignment = (new Assignment())->getAssignment($id);

        if (isset($_POST['assignmentUpdate'])) {
            $request = $this->getRequest();
            $title = $request->get('title');
            $assignment = $request->get('assignment');
            $addAssignment = new Assignment();
            if ($addAssignment->updateAssignment($title, $assignment, $user['login_id'], $id)) {
                $this->addFlash('success', 'Assignment update successfully.');
                $this->redirect('/t/assignments');
            }
        }

        return View::renderTemplate('teacher/edit/assignment.html.twig', array(
            'title' => 'Assignments',
            'user' => $user,
            'assignment' => $assignment,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function deleteAction($id) {
        $this->handleSecurity('Teacher');
        if ((new Assignment())->deleteAssignment($id)) {
            $this->addFlash('info', 'Assignment #'.$id.' deleted successfully.');
            $this->redirect('/t/assignments');
        } else {
            $this->addFlash('danger', 'Assignment #'.$id.' delete error.');
            $this->redirect('/t/assignments');
        }
    }

}