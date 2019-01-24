<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Teacher;

use App\Model\Portal\Material;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NoteController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        $notes = (new Material())->getMaterials();


        return View::renderTemplate('teacher/notes.html.twig', array(
            'title' => 'Notes',
            'user' => $user,
            'notes' => $notes,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function viewNote($subject, $id){
        $this->handleSecurity('Teacher');
        $user = $this->getUser();
        $note = (new Material())->getMaterial($id);
        if (!$note) {
            throw new \Exception('No route matched.', 404);
        }

        return View::renderTemplate('teacher/view/note.html.twig', array(
            'title' => $note['title'],
            'user' => $user,
            'note' => $note,
            'messages' => $this->getAllFlashes(),
            'addHamburger' => 'add'
        ));
    }

    public function addAction(Request $request) {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        if (isset($_POST['addNoteSubmit'])) {
            $course = $request->get('course');
            $subject = $request->get('subject');
            $title = $request->get('title');
            $content = $request->get('content');
            $title = $title.','.$subject.','.$course;
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

    public function editAction($id){
        $this->handleSecurity('Teacher');
        $user = $this->getUser();
        $note = (new Material())->getMaterial($id);
        if (!$note) {
            throw new \Exception('No route matched.', 404);
        }

        if (isset($_POST['noteUpdate'])) {
            $request = $this->getRequest();
            $course = $request->get('course');
            $subject = $request->get('subject');
            $title = $request->get('title');
            $note = $request->get('content');
            $title = $title.','.$subject.','.$course;
            $editNote = new Material();
            if ($editNote->updateMaterial($title, $note, $id)) {
                $this->addFlash('success', 'Note update successfully.');
                $this->redirect('/t/notes');
            }
        }

        return View::renderTemplate('teacher/edit/note.html.twig', array(
            'title' => 'Edit Note',
            'user' => $user,
            'note' => $note,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function deleteAction($id) {
        $this->handleSecurity('Teacher');
        if ((new Material())->deleteMaterial($id)) {
            $this->addFlash('info', 'Note deleted successfully.');
            $this->redirect('/t/notes');
        } else {
            $this->addFlash('danger', 'Note delete error.');
            $this->redirect('/t/notes');
        }
    }

}