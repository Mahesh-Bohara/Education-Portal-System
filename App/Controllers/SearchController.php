<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller;

use App\Model\Portal;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SearchController extends Controller {

    public function indexAction(Request $request)
    {
        $query =$request->get('q');
        if (!$query) {
            $this->redirect('/');
        }
        $query =urldecode($query);
        $user = $this->getUser();

        if ($user['role'] == 'Teacher') {
            $this->handleSecurity('Teacher');
        } elseif ($user['role'] == 'Student') {
            $this->handleSecurity('Student');
        }

        $searchNote = (new Portal())->search($query,'note');
        $searchAssignment = (new Portal())->search($query,'assignments');

        return View::renderTemplate('app/search.html.twig', array(
            'title' => 'Search',
            'user' => $user,
            'query' => $query,
            'notes' => $searchNote,
            'assignments' => $searchAssignment,
            'messages' => $this->getAllFlashes()
        ));
    }

}