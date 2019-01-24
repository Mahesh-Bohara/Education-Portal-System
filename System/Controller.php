<?php

namespace System;

use App\Model\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base controller
 *
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    public function setSession(Session $session){
        $this->session = $session;
    }

    public function getSession(){
        return $this->session;
    }

    public function setApplicationPath($path){
        $this->appPath = rtrim($path, '/');
    }

    public function setRegistry($registry){
        $this->registry = $registry;
    }

    public function setRequest(Request $request){
        $this->request = $request;
    }

    public function getRequest(){
        return $this->request;
    }

    public function setResponse(Response $response){
        $this->response = $response;
    }

    public function getResponse(){
        return $this->response;
    }

    public function isLoggedin(){
        if(isset($_SESSION['epUser'])) {
         return true;
        }
    }

    public function getUser(){
        if(isset($_SESSION['epUser'])) {
            $user = new User();
            $userData = $user->userByUsername($_SESSION['epUser']);
            return $userData;
        }
    }

    public function isRole($role) {
        $cuser = $this->getUser();
        $cuser = $cuser['username'];
        $user = new User();
        if ($user->userInfo($cuser)['role'] == $role) {
            return true;
        }
    }

    public function handleSecurity($role = null) {
        if (!$this->isLoggedin()) $this->redirect('/account/login');
        if ($role != null) {
            if (!$this->isRole($role)) {
                if ($role == 'Teacher')
                    $this->redirect('/s/home');
                elseif ($role == 'Student')
                    $this->redirect('/t/home');
            }
        }
    }

    public function updateLastActivity(){
        (new User())->updateLastActivity($this->getUser()['email']);
    }

    public function addFlash($type, $flash) {
        $session = $this->getSession();
        $session->getFlashBag()->add($type, $flash);
    }

    public function getAllFlashes() {
        // $this->addFlash() is equivalent to $request->getSession()->getFlashBag()->add()
        //$request->getSession()->getFlashBag()->add('notice', 'hello');
        //$session->getFlashBag()->add('success', 'Login Successful');
        $session = $this->getSession();
        return $session->getFlashBag()->all();
    }

    public function redirect($location){
        header('Location: ' . $location);
        exit;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
    
}
