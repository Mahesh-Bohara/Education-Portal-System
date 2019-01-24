<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Teacher;

use App\Model\Portal\Assignment;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AssignmentController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        $assignments = (new Assignment())->getAssignments();

        return View::renderTemplate('teacher/assignments.html.twig', array(
            'title' => 'Assignments',
            'user' => $user,
            'assignments' => $assignments,
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
        if (!$assignment) {
            throw new \Exception('No route matched.', 404);
        }

        if (isset($_POST['assignmentUpdate'])) {
            $request = $this->getRequest();
            $title = $request->get('title');
            $assignment = $request->get('assignment');
            $ediAssignment = new Assignment();
            if ($ediAssignment->updateAssignment($title, $assignment, $user['login_id'], $id)) {
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
            $this->addFlash('info', 'Assignment deleted successfully.');
            $this->redirect('/t/assignments');
        } else {
            $this->addFlash('danger', 'Assignment delete error.');
            $this->redirect('/t/assignments');
        }
    }

}