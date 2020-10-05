<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	class Labori_Router
	{
		const TYPE_IMPL = "impl";
		const TYPE_ROOT = "root";
		const TYPE_SUB = "sub";
		const TYPE_SERV = "serv";
		const TYPE_HLPR = "hlpr";
		const TYPE_RPRT = "rprt";

		private static function getDirectory($type, $parentRoute = '')
		{
			$importDir = null;

			if(Labori_Utl::streql($type, self::TYPE_IMPL))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_implemented", true);
			}
			else if(Labori_Utl::streql($type, self::TYPE_ROOT))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_root_pages", true);
			}
			else if(Labori_Utl::streql($type, self::TYPE_SUB))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_sub_pages", true);
			}
			else if(Labori_Utl::streql($type, self::TYPE_RPRT))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_report_scripts", true);
			}
			else if(Labori_Utl::streql($type, self::TYPE_HLPR))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_helper_scripts", true);
			}
			else if(Labori_Utl::streql($type, self::TYPE_SERV))
			{
				$importDir = Labori_Core::getDeploymentOption("labori_php_service_scripts", true);
			}

			if(!is_null($parentRoute) && $parentRoute != "null")
			{
				$importDir .= $parentRoute;
			}

			return $importDir;
		}

		public static function importAllFromDirectory($type, $parentRoute = '')
		{
			$dir = self::getDirectory($type, $parentRoute);
			foreach(glob($dir . "*.php") as $filename)
			{
			    include $filename;
			}
		}

		public static function handlePageRequest()
		{
			$path = explode("/", strtok($_SERVER['REQUEST_URI'], "?"));
			$cleanedPath = array();
			$retArray = array(
				"url" => "",
				"current_page" => null,
				"full_path" => array(),
				"nav_path" => array()
			);

			foreach($path as $thisPath)
			{
				if(is_null($thisPath) || empty(trim($thisPath)))
				{
					continue;
				}
				else
				{
					$cleanedPath[] = strtolower(trim($thisPath));
				}
			}

			if(count($cleanedPath) == 1)
			{
				$cleanedPath[] = strtolower(trim(Labori_Session::getSessionVariable("permissions")["default_page"]));
			}

			$retArray["full_path"] = $cleanedPath;
			$currentPage = $cleanedPath[count($cleanedPath) - 1];
			
			if(count($cleanedPath) == 2)
			{
				$rootPages = self::findAllClasses(self::TYPE_ROOT);

				foreach($rootPages as $thisPage)
				{
					if(Labori_Utl::streql($thisPage->getPageURL(), $currentPage))
					{
						$retArray["url"] = $thisPage->getPageURL();
						$retArray["current_page"] = $thisPage;
						$retArray["nav_path"][] = array("name"=>$thisPage->getPageName(),
														"url"=>$thisPage->getPageURL());
						break;
					}
				}
			}
			else if(count($cleanedPath) > 2)
			{
				$parentRoute = "";
				$rootPages = self::findAllClasses(self::TYPE_ROOT);
				foreach($rootPages as $thisPage)
				{
					if(Labori_Utl::streql($thisPage->getPageURL(), $cleanedPath[1]))
					{
						$parentRoute = $thisPage->getPageURL() . "/";
						$retArray["nav_path"][] = array("name"=>$thisPage->getPageName(),
														"url"=>$thisPage->getPageURL());
						break;
					}
				}	

				if(strlen(trim($parentRoute)) == 0)
				{
					$parentRoute = $cleanedPath[1] . "/";
				}

				for($i = 2; $i < count($cleanedPath); $i++)
				{
					$tempPages = self::findAllClasses(self::TYPE_SUB, $parentRoute);

					foreach($tempPages as $thisPage)
					{
						if(Labori_Utl::streql($thisPage->getPageURL(), $cleanedPath[$i]))
						{
							$parentRoute .= $thisPage->getPageURL() . "/";
							$retArray["nav_path"][] = array("name"=>$thisPage->getPageName(),
															"url"=>$thisPage->getPageURL());
							$retArray["current_page"] = $thisPage;
							$retArray["url"] = rtrim($parentRoute, '/');
							break;
						}
					}

					$parentRoute .= $cleanedPath[$i] . "/";
					
				}
			}

			return $retArray;
		}

		public static function findAllClasses($type, $parentRoute = '')
		{
			$importDir = self::getDirectory($type, $parentRoute);

			if(!is_dir($importDir))
			{
				return array();
			}

			$files = scandir($importDir);

			$importedClasses = array();
			$className = null;
			$temp = null;

			foreach($files as $thisFileName)
			{
				if(preg_match("/^.*\.php$/i", $thisFileName))
				{
					$className = basename($thisFileName, ".php");
					$classLocation = $importDir . $thisFileName;

					require_once $classLocation;
					$temp = $className;
					$importedClasses[] = new $temp();
				}
			}


			return $importedClasses;
		}

		public static function findClass($type, $className, $parentRoute = "")
		{
			$importDir = self::getDirectory($type, $parentRoute);
			$files = scandir($importDir);
			$foundClassDir = null;
			$temp = null;

			foreach($files as $thisFileName)
			{
				$temp = basename($thisFileName, ".php");

				if(Labori_Utl::streql($temp, $className))
				{
					$foundClassDir = $thisFileName;
					break;
				}
			}

			if(!is_null($thisFileName))
			{
				$classLocation = $importDir . $thisFileName;

				require_once $classLocation;
				$class = $className;
				return new $class();
			}
			else
			{
				throw new Exception("Failed to load class: " . $className);
			}
		}

		public static function handleRequest()
		{
			$method = $_SERVER['REQUEST_METHOD'];

			if(Labori_Utl::streql($method, "POST"))
			{
				if(!empty($_FILES) && isset($_REQUEST['request_payload'])) 
				{
    				$tempPost = array_change_key_case(json_decode($_REQUEST['request_payload'], true));

    				$tempFiles = array();

    				foreach($_FILES as $thisFile)
    				{
    					$tempFiles[] = $thisFile;
    				}

    				$tempArgs = json_decode($tempPost["args"], true);
    				$tempArgs["uploaded_files"] = $tempFiles;
    				$tempPost["args"] = json_encode($tempArgs);

    				if(Labori_Utl::allKeysExist(array("type", "class", "action", "args", "request_key"), $tempPost))
					{
						
						$tempParentRoute = null;

						if(isset($tempPost["parent_route"]))
						{
							$tempParentRoute = $tempPost["parent_route"];
						}

						$thisClass = self::findClass($tempPost["type"], $tempPost["class"], $tempParentRoute);
						echo $thisClass->handleRequest($tempPost["action"], $tempPost["args"], $tempPost["request_key"]);
					}
					else
					{
						Logging_Manager::logMessage(Logging_Manager::LOG_SEVERITY_ERROR_MINOR, 
											   	   "Supplied file upload array did not contain the appropriate values: " . json_encode($tempPost), true);
					}
    			} 
				else
				{
					$_POST = array_change_key_case($_POST);
					
					if(Labori_Utl::allKeysExist(array("type", "class", "parent_route", "action", "args", "request_key"), $_POST))
					{
						$tempParentRoute = null;

						if(isset($_POST["parent_route"]))
						{
							$tempParentRoute = $_POST["parent_route"];
						}

						$thisClass = self::findClass($_POST["type"], $_POST["class"], $tempParentRoute);
						echo $thisClass->handleRequest($_POST["action"], $_POST["args"], $_POST["request_key"]);
					}
					else
					{
						Logging_Manager::logMessage(Logging_Manager::LOG_SEVERITY_ERROR_MINOR, 
											   	   "Supplied POST array did not contain the appropriate values: " . var_export($_POST, true), true);
					}
				}
			}
		}
	}

	if(Labori_Utl::streql($_SERVER['REQUEST_METHOD'], "POST"))
	{
		Labori_Router::handleRequest();
	}
?>