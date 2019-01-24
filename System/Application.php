<?php 

namespace System;

use System\Component\Registry;
//use System\Component\Request;
//use System\Component\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use System\Controller;
use System\View;
use System\Router;

class Application {
    
	protected $rootPath;
	protected $appPath;
	protected $registry;
    
	public function __construct($rootPath){
	
		$rootPath = rtrim(str_replace("\\", "/", $rootPath));
		$this->appPath = $rootPath . '/App';

		//$this->response = new Response();
		$this->registry = new Registry();
		$this->routes = new Router();

		$this->routes->add('default', ['controller' => 'Default', 'action' => 'default']);
	}
	
	public function addRoute($routeName, $params = []){
		//$this->routes[$routeName] = $route;
		$this->routes->add($routeName, $params);
	}

    // all the routes
	public function routes() {
		$this->routes->add('', ['controller' => 'Index', 'action' => 'index']);
		$this->routes->add('account', ['controller' => 'Auth', 'action' => 'index']);
		$this->routes->add('account/', ['controller' => 'Auth', 'action' => 'index']);
        $this->routes->add('account/login', ['controller' => 'Auth', 'action' => 'login']);

        $this->routes->add('account/signup', ['controller' => 'Auth', 'action' => 'register']);
        $this->routes->add('account/r/Student', ['controller' => 'Auth', 'action' => 'registerStudent']);
        $this->routes->add('account/r/Teacher', ['controller' => 'Auth', 'action' => 'registerTeacher']);
        $this->routes->add('account/logout', ['controller' => 'Auth', 'action' => 'logout']);
        $this->routes->add('logout', ['controller' => 'Auth', 'action' => 'logout']);


        $this->routes->add('s/home', ['controller' => 'Student\Student', 'action' => 'index']);
        $this->routes->add('s/notes', ['controller' => 'Student\Note', 'action' => 'index']);
        $this->routes->add('s/note/{subject:.+}/{id:\d+}', ['controller' => 'Student\Note', 'action' => 'viewNote']);
        $this->routes->add('s/assignments', ['controller' => 'Student\Assignment', 'action' => 'index']);
        $this->routes->add('s/assignment/{id:\d+}/submit', ['controller' => 'Student\Assignment', 'action' => 'submitAssignment']);

        $this->routes->add('s/qna', ['controller' => 'Student\QNA', 'action' => 'index']);
        $this->routes->add('s/qna/ask', ['controller' => 'Student\QNA', 'action' => 'add']);
        $this->routes->add('s/qna/{id:\d+}', ['controller' => 'Student\QNA', 'action' => 'view']);
        $this->routes->add('s/qna/{id:\d+}/{action}', ['controller' => 'Student\QNA']);

        $this->routes->add('ajax', ['controller' => 'Ajax', 'action' => 'ajax']);
        $this->routes->add('ajax/add', ['controller' => 'Ajax', 'action' => 'add']);

        $this->routes->add('t/home', ['controller' => 'Teacher\Teacher', 'action' => 'index']);

        //teacher routes
        $this->routes->add('t', ['controller' => 'Teacher\Teacher', 'action' => 'index']);
        $this->routes->add('t/', ['controller' => 'Teacher\Teacher', 'action' => 'index']);


        $this->routes->add('t/notes', ['controller' => 'Teacher\Note', 'action' => 'index']);
        $this->routes->add('t/note/add', ['controller' => 'Teacher\Note', 'action' => 'add']);
        $this->routes->add('t/note/{id:\d+}/{action}', ['controller' => 'Teacher\Note']);

        $this->routes->add('t/note/{subject:.+}/{id:\d+}', ['controller' => 'Teacher\Note', 'action' => 'viewNote']);

        $this->routes->add('t/assignments', ['controller' => 'Teacher\Assignment', 'action' => 'index']);
        $this->routes->add('t/assignment/add', ['controller' => 'Teacher\Assignment', 'action' => 'add']);
        $this->routes->add('t/assignment/{id:\d+}/{action}', ['controller' => 'Teacher\Assignment']);

        $this->routes->add('t/qna', ['controller' => 'Teacher\QNA', 'action' => 'index']);
        $this->routes->add('t/qna/add', ['controller' => 'Teacher\QNA', 'action' => 'add']);
        $this->routes->add('t/qna/{id:\d+}', ['controller' => 'Teacher\QNA', 'action' => 'view']);
        $this->routes->add('t/qna/{id:\d+}/{action}', ['controller' => 'Teacher\QNA']);

        //search
        $this->routes->add('search', ['controller' => 'Search', 'action' => 'index']);
//        $this->routes->add('search?q={query:.+}', ['controller' => 'Teacher\Search', 'action' => 'index']);
//        $this->routes->add('search?q={query:.+}', ['controller' => 'Search', 'action' => 'index']);

        $this->routes->add('chat', ['controller' => 'Chat', 'action' => 'index']);
        $this->routes->add('chat/{username:.+}', ['controller' => 'Chat', 'action' => 'chat']);

        // teacher profile
        $this->routes->add('t/{username:.+}', ['controller' => 'Teacher\Profile', 'action' => 'index']);
        // student profile
        $this->routes->add('s/{username:.+}', ['controller' => 'Student\Profile', 'action' => 'index']);

        $this->routes->add('t/course', ['controller' => 'Teacher\Course', 'action' => 'index']);

        $this->routes->add('t/{controller}/{action}', ['namespace' => 'Teacher\Teacher']);

		//$this->routes->add('admins/{controller}/{action}', ['namespace' => 'Admin']);
		$this->routes->add('{controller}/{action}');
	}

	public function getRegistry(){
		return $this->registry;
	}
	
	public function getResponse(){
		return $this->response;
	}

	public function run(Request $request) {
		$this->routes();
		return $this->routes->dispatch($request);
	}
    


}