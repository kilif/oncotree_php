<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	abstract class Application_Page extends Request_Handler
	{
		private $pageName = "MISSING NAME";
		private $pageURL = "unknown";

		public function getPageName()
		{
			return $this->pageName;
		}

		public function setPageName($pageName)
		{
			$this->pageName = $pageName;
		}

		public function getPageURL()
		{
			return $this->pageURL;
		}

		public function setPageURL($pageURL)
		{
			$this->pageURL = $pageURL;
		}

		public function getPermissionList()
		{
			$retArray = array("can_access" => false);
			$this->getCustomPermissionList($retArray);

			return $retArray;
		}

		public function pageNeedsLogin()
		{
			return true;
		}

		public static function getSpecialPermissionContext($pageURL)
		{
			$permissions = Labori_Session::getSessionVariable("permissions");

			if(!isset($permissions["global_permissions"]) || !isset($permissions["page_permissions"]))
			{
				return "";
			}

			if((isset($permissions["global_permissions"]["is_super_admin"]) &&
			    isset($permissions["global_permissions"]["can_access_all_pages"])) && 
			    $permissions["global_permissions"]["is_super_admin"] ||
			    $permissions["global_permissions"]["can_access_all_pages"])
			{
				return "super";
			}

			$pagePermissions = $permissions["page_permissions"];

			foreach($pagePermissions as $thisRolePermissionSet)
			{
				if(isset($thisRolePermissionSet[$pageURL]) && isset($thisRolePermissionSet[$pageURL]["context"]))
				{
					return $thisRolePermissionSet[$pageURL]["context"];
				}
			}

			return "";
		}

		public static function hasPermissionToViewPage($pageURL)
		{
			$permissions = Labori_Session::getSessionVariable("permissions");

			if(!isset($permissions["global_permissions"]) || !isset($permissions["page_permissions"]))
			{
				return false;
			}

			if((isset($permissions["global_permissions"]["is_super_admin"]) &&
			    isset($permissions["global_permissions"]["can_access_all_pages"])) && 
			    $permissions["global_permissions"]["is_super_admin"] ||
			    $permissions["global_permissions"]["can_access_all_pages"])
			{
				return true;
			}

			$pagePermissions = $permissions["page_permissions"];

			foreach($pagePermissions as $thisRolePermissionSet)
			{
				if(isset($thisRolePermissionSet[$pageURL]) && isset($thisRolePermissionSet[$pageURL]["can_access"]) && 
			   	   $thisRolePermissionSet[$pageURL]["can_access"])
				{
					return true;
				}
			}	

			return false;
		}

		protected abstract function methodIsService($methodName);
		protected abstract function getCustomPermissionList(&$permissionList);
		public abstract function buildPage($rootDir, $pageRequest);
	}
?>