<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Teacher;

use App\Model\Portal\QNA;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class QNAController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        $questions = (new QNA())->getQuestions();

        return View::renderTemplate('teacher/qna.html.twig', array(
            'title' => 'QNA',
            'user' => $user,
            'questions' => $questions,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function viewAction($id){
        $this->handleSecurity('Teacher');
        $user = $this->getUser();
        $question = (new QNA())->getQuestion($id);
        if (!$question) {
            throw new \Exception('No route matched.', 404);
        }
        $answers = (new QNA())->getAnswers($id);

        return View::renderTemplate('teacher/view/qna.html.twig', array(
            'title' => $question['title'],
            'user' => $user,
            'question' => $question,
            'answers' => $answers,
            'messages' => $this->getAllFlashes(),
            'addHamburger' => 'add'
        ));
    }

    public function addAction(Request $request) {
        $this->handleSecurity('Teacher');

        $user = $this->getUser();

        if (isset($_POST['addQuestionSubmit'])) {
            $title = $request->get('title');
            $question = $request->get('questionBody');
            $addAssignment = new QNA();
            if ($addAssignment->addQuestion($title, $question, $user['login_id'])) {
                $this->addFlash('success', 'New question added successfully.');
                $this->redirect('/t/qna');
            }
        }

        return View::renderTemplate('teacher/add/qna.html.twig', array(
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

    public function submitAction($id) {
        $this->handleSecurity('Teacher');
        $user = $this->getUser();
        $question = (new QNA())->getQuestion($id);
        if (!$question) {
            throw new \Exception('No route matched.', 404);
        }

        if (isset($_POST['answerSubmit'])) {
            $request = $this->getRequest();
            $answer = $request->get('answer');
            $submitAnswer = new QNA();
            if ($submitAnswer->submitAnswer($answer, $user['login_id'], $id)) {
                $this->addFlash('success', 'Answer submit successfully.');
                $this->redirect('/t/qna/'.$id);
            }
        }
    }

    public function deleteAction($id) {
        $this->handleSecurity('Teacher');
        if ((new QNA())->deleteQuestion($id)) {
            $this->addFlash('info', 'Question deleted successfully.');
            $this->redirect('/t/qna');
        } else {
            $this->addFlash('danger', 'Question delete error.');
            $this->redirect('/t/qna');
        }
    }

}