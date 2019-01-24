<?php

namespace System\Component;

class Response {
	
	protected $statusCode ;
	protected $output;
	protected $headers = array();
	
	public function addHeader($header, $value, $override = true){
		if($override){
			$this->headers[$header] = $value;
		}else{
			if(!array_key_exists($header, $this->headers)){
				$this->headers[$header] = $value;
			}
		}
		return $this;
	}
	
	public function setStatusCode($code){
		$this->statusCode = $code;
		return $this;
	}
	
	public function getStatusCode(){
		return $this->statusCode;
	}
	
    public function redirect($location){
        header('Location: ' . $location);
        exit;
    }

	public function write($text){
		$this->output .= $text;
		return $this;
	}
	
	public function writeLine($text){
		$this->output .= PHP_EOL . $text;
		return $this;
	}
	
	public function flush(){
		if (!headers_sent()){
			foreach($this->headers as $header=>$value){
				header($header.':'.$value, true);
			}
		}
		
		if($this->statusCode){
			$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
			header($protocol . ' ' . $this->statusCode);
		}
		
		echo $this->output;
		exit;
	}
}
?>
