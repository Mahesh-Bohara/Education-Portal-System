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


class SearchController extends Controller {

    public function indexAction(Request $request)
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();
        $query =$request->getQueryString();
        echo $query;
        echo 'search page';
        exit;

        $assignments = (new Assignment())->getAssignments();

        return View::renderTemplate('teacher/assignments.html.twig', array(
            'title' => 'Assignments',
            'user' => $user,
            'assignments' => $assignments,
            'messages' => $this->getAllFlashes()
        ));
    }

}