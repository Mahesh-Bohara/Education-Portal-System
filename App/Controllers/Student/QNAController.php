<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/13/2018
 * Time: 9:26 AM
 */

namespace App\Controller\Student;

use App\Model\Portal\QNA;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class QNAController extends Controller {

    public function indexAction()
    {
        $this->handleSecurity('Student');

        $user = $this->getUser();

        $questions = (new QNA())->getQuestions();

        return View::renderTemplate('student/qna.html.twig', array(
            'title' => 'Questions',
            'user' => $user,
            'questions' => $questions,
            'messages' => $this->getAllFlashes()
        ));
    }

    public function viewAction($id){
        $this->handleSecurity('Student');
        $user = $this->getUser();
        $question = (new QNA())->getQuestion($id);
        if (!$question) {
            throw new \Exception('No route matched.', 404);
        }
        $answers = (new QNA())->getAnswers($id);

        return View::renderTemplate('student/view/qna.html.twig', array(
            'title' => $question['title'],
            'user' => $user,
            'question' => $question,
            'answers' => $answers,
            'messages' => $this->getAllFlashes(),
            'addHamburger' => 'add'
        ));
    }

    public function submitAction($id) {
        $this->handleSecurity('Student');
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
                $this->redirect('/s/qna/'.$id);
            }
        }
    }

}