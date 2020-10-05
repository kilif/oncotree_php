<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	abstract class Request_Handler
	{
		abstract protected function methodIsService($methodName);

		public function exemptFromValidation($methodName)
		{
			return false;
		}

		public function handleRequest($action, $args, $requestKey)
		{

			if(!$this->exemptFromValidation($action) && !Labori_Core::validateRequestKey($requestKey))
			{
				Logging_Manager::logMessage(Logging_Manager::LOG_SEVERITY_ERROR_MINOR, 
								   	   	   "Supplied request key is not valid.", false);
			}
			else if($this->methodIsService($action) && method_exists($this, $action))
			{
				return $this->{($action)}(json_decode($args, true));
			}
			else
			{
				Logging_Manager::logMessage(Logging_Manager::LOG_SEVERITY_ERROR_MINOR, 
								   	   	   "Method (" . $action . ") either isn't a service method or doesn't exist", true);
			}	
		}
	}
?>