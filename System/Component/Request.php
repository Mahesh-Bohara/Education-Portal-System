<?php

namespace System\Component;

class Request {
	
	protected $controllerName;
	protected $actionName;
	protected $post;
	protected $query;
	protected $params;
	protected $segments;

	public function __construct(){
		$this->post = $_POST;
		$this->query = $_GET;
		$this->params = array_merge($this->query, $this->post);
		$this->segments = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
	}
	
	public function setControllerName($controllerName){
		$this->controllerName = $controllerName;
	}
	
	public function getControllerName(){
		return $this->controllerName;
	}
	
	public function setActionName($actionName){
		$this->actionName = $actionName;
	}
	
	public function getActionName(){
		return $this->actionName;
	}
	
	public function getPost($name = ''){
		if(!$name){
			return $this->post;
		}
		if(isset($this->post[$name])){
			return $this->post[$name];
		}
	}
	
	public function getQuery($name = ''){
		if(!$name){
			return $this->query;
		}
		if(isset($this->query[$name])){
			return $this->query[$name];
		}
	}
	
	public function getSegment($index){
		if(isset($this->segments[$index])){
			return $this->segments[$index];
		}
	}
	
	public function getSegments(){
		return $this->segments;
	}
	
	public function getSegmentCount(){
		return count($this->segments);
	}
	
	public function getUri(){
		return trim($this->getServer('REQUEST_URI'), '/');
	}
	
	public function getServer($name){
		if(isset($_SERVER[$name])){
			return $_SERVER[$name];
		}
	}
	
	public function isPost(){
		if($this->getServer('REQUEST_METHOD') == 'POST'){
			return true;
		}
		return false;
	}
	
	public function isGet(){
		if($this->getServer('REQUEST_METHOD') == 'GET'){
			return true;
		}
		return false;
	}
	
	public function toArray(){
		return $this->params;
	}
}
?>
