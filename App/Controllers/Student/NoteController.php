<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Student;

use App\Model\Portal\Material;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NoteController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Student');

        $user = $this->getUser();

        $notes = (new Material())->getMaterials();

        return View::renderTemplate('student/notes.html.twig', array(
            'title' => 'Notes',
            'user' => $user,
            'notes' => $notes,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function viewNote($subject, $id){
        $this->handleSecurity('Student');
        $user = $this->getUser();
        $note = (new Material())->getMaterial($id);
        if (!$note) {
            throw new \Exception('No route matched.', 404);
        }

        return View::renderTemplate('student/view/note.html.twig', array(
            'title' => $note['title'],
            'user' => $user,
            'note' => $note,
            'messages' => $this->getAllFlashes(),
            'addHamburger' => 'add'
        ));
    }

    public function addAction(Request $request) {
        $this->handleSecurity('Student');

        $user = $this->getUser();

        if (isset($_POST['addNoteSubmit'])) {
            $course = $request->get('course');
            $subject = $request->get('subject');
            $content = $request->get('content');
            $title = 'Note of '.$subject.' :: '.$course;
            $addNote = new Material();
            if ($addNote->addMaterial($title, $content, 'note')) {
                $this->addFlash('success', 'New note added successfully.');
                $this->redirect('/t/notes');
            }
        }

        return View::renderTemplate('teacher/add/note.html.twig', array(
            'title' => 'Add Note',
            'user' => $user
        ));
    }

}