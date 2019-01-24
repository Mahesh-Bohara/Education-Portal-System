<?php
/**
 * Created by PhpStorm.
 * User: Mahesh
 * Date: 12/12/2018
 * Time: 7:27 PM
 */

namespace App\Controller;

use App\Model\Student;
use App\Model\Teacher;
use App\Model\User;
use System\Controller;
use \System\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends Controller
{
    public function index(){
        if (!$this->isLoggedin()) {
            $this->redirect('/account/login');
        } else {
            $this->redirect('/');
        }
    }

    public function login(Request $request) {
        if ($this->isLoggedin()) {
            $this->redirect('/');
        }
        $session = $this->getSession();
        if (isset($_POST['login'])) {
            $email = $request->get('email');
            $password = $request->get('password');
            $loginAs= $request->get('epLA');
            if ($loginAs == 1) {
                //login as student
                $user = new User();
                if($user->studentLogin($email, $password)) {
                    $this->redirect('/');
                } else {
                    $session->getFlashBag()->add('danger', 'Invalid credentials.');
                }
            } elseif ($loginAs == 2) {
                //login as teacher
                $user = new User();
                if($user->teacherLogin($email, $password)) {
                    $this->redirect('/');
                } else {
                    $session->getFlashBag()->add('danger', 'Invalid credentials.');
                }
            }
        }
        $session = $this->getSession();
        return View::renderTemplate('auth/login.html', array(
                'messages' => $session->getFlashBag()->all()
        ));
    }

    public function register(Request $request){
        return View::renderTemplate('auth/register.html');
    }

    public function registerStudent(Request $request){
        if(isset($_POST['signupStudent'])) {
            $fname = $request->get('fname');
            $lname = $request->get('lname');
            $email = $request->get('email');
            $password = $request->get('password');
            $studentRegister = new Student();
            if ($studentRegister->studentRegister($fname,$lname,$email,$password)) {
                $this->addFlash('success', 'Registration Successful');
                $this->redirect('/account/login');
            } else {
                $this->addFlash('danger', 'Registration Error');
            }
        }
        return View::renderTemplate('auth/rStudent.html', array(
            'messages' => $this->getAllFlashes()
        ));
    }

    public function registerTeacher(Request $request){
        if(isset($_POST['signupTeacher'])) {
            $fname = $request->get('fname');
            $lname = $request->get('lname');
            $email = $request->get('email');
            $password = $request->get('password');
            $designation = $request->get('designation');
            $teacherRegister = new Teacher();
            if ($teacherRegister->teacherRegister($fname,$lname,$email,$password,$designation)) {
                $this->addFlash('success', 'Registration Successful');
                $this->redirect('/account/login');
            } else {
                $this->addFlash('danger', 'Registration Error');
            }
        }

        return View::renderTemplate('auth/rTeacher.html', array(
            'messages' => $this->getAllFlashes()
        ));
    }

    public function logout(){
        session_unset();
        session_destroy();
        unset($_SESSION['epUser']);
        unset($_SESSION['epUserRole']);
        if (!$this->isLoggedin()){
            $this->redirect('/');
        }
    }

}