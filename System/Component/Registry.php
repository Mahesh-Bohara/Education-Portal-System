<?php

namespace System\Component;

class Registry {
	protected $collection = array();
	
	public function add($name, $value){
		$this->collection[$name] = $value;
	}

	public function get($name){
		if(isset($this->collection[$name])){
			return $this->collection[$name];
		}
	}	
}
?>