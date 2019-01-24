<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Student;

use App\Model\Portal\Assignment;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AssignmentController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Student');

        $user = $this->getUser();

        $assignments = (new Assignment())->getAssignments();

        return View::renderTemplate('student/assignments.html.twig', array(
            'title' => 'Assignments',
            'user' => $user,
            'assignments' => $assignments,
            'messages' => $this->getAllFlashes()
        ));
    }


    public function submitAssignmentAction($id){
        $this->handleSecurity('Student');
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

        return View::renderTemplate('student/submit/assignment.html.twig', array(
            'title' => 'Assignments',
            'user' => $user,
            'assignment' => $assignment,
            'messages' => $this->getAllFlashes()
        ));
    }
}