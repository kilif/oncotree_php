<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	abstract class Logging_Manager
	{
		const LOG_SEVERITY_TEST_MESSAGE = "test";
		const LOG_SEVERITY_ERROR_MINOR = "error_minor";
		const LOG_SEVERITY_ERROR_SEVERE = "error_severe";

		protected abstract function handleLogRequest($severity, $logEntry, $storeTrace=true);

		public static function logMessage($severity, $logEntry, $storeTrace=true)
		{
			Labori_Router::findClass(Labori_Router::TYPE_IMPL, 
									 Labori_Core::getDeploymentOption("logging_implemented_class", 
									 true))->handleLogRequest($severity, $logEntry, $storeTrace);
		}
	}
?>