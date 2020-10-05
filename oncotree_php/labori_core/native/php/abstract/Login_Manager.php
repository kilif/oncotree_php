<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	abstract class Login_Manager extends Request_Handler
	{
		abstract protected function buildLoginInterface($errorCode);
		
		public static function isLoggedIn()
		{
			if(isset($_SESSION["user_login_status"]) && $_SESSION["user_login_status"] == "1")
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>