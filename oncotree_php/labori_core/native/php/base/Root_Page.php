<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	abstract class Root_Page extends Application_Page
	{
		private $pageIcon = '<i class="fa fa-square-o" aria-hidden="true"></i>';
		private $pagePriority = 0;
		private $restrictToInstanceTypes = array();

		public function getPageIcon()
		{
			return $this->pageIcon;
		}

		public function setPageIcon($pageIcon)
		{
			$this->pageIcon = $pageIcon;
		}

		public function getPagePriority()
		{
			return $this->pagePriority;
		}

		public function setPagePriority($pagePriority)
		{
			$this->pagePriority = $pagePriority;
		}

		public static function sortRootPagesByPriorty(&$rootPages)
		{
			usort($rootPages, array('self','cmpPages'));
		}

		public function setRestrictedToInstanceTypes($typesArray)
		{
			$this->restrictToInstanceTypes = $typesArray;
		}

		public function getIsRestrictedByInstanceTypes()
		{
			if(!empty($this->restrictToInstanceTypes))
			{
				foreach($this->restrictToInstanceTypes as $thisInstanceType)
				{
					if(Labori_Utl::streql($thisInstanceType, Instance_Settings::INSTANCE_TYPE))
					{
						return false;
					}
				}

				return true;
			}

			return false;
		}

		private static function cmpPages($a, $b)
		{
			if($a->pagePriority < $b->pagePriority)
			{
				return -1;
			}
			else if($a->pagePriority > $b->pagePriority)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}

		protected abstract function methodIsService($methodName);
		protected abstract function getCustomPermissionList(&$permissionList);
		public abstract function buildPage($rootDir, $pageRequest);
	}
?>