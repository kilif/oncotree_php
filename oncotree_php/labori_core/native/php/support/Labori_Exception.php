<?php
	class Labori_Exception extends Exception
	{
		public $errorFile = "";
		public $errorLine = "";
		public $errorContext = array();

		public function __construct($message, $code = 0, Exception $previous = null, 
									$errorFile = "", $errorLine ="", $errorContext = array()) 
		{
			$this->errorFile = $errorFile;
			$this->errorLine = $errorLine;
			$this->errorContext = $errorContext;
			
			parent::__construct($message, $code, $previous);
    	}
	}
?>