<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	class Labori_Session
	{
		public static function buildSession($userId, $username, $email, $permissionArray, $user_login_status = 1)
		{
			$_SESSION['user_id'] = $userId;
			$_SESSION['user_name'] = $username;
			$_SESSION['user_email'] = $email;
            $_SESSION['user_login_status'] = $user_login_status;
            $_SESSION['permissions'] = $permissionArray;
            $_SESSION['request_key'] = openssl_random_pseudo_bytes(128);
            $_SESSION["original_user_session"] = array();
            $_SESSION["simulating_user"] = false;
            $_SESSION["user_simulation_list"] = array();
		}

		public static function getRoleTag()
		{
			if(isset($_SESSION["permissions"]["role_tag"]))
			{
				return $_SESSION["permissions"]["role_tag"];
			}
			else
			{
				return "unknown";
			}
		}

		public static function parsePermissions($conn, $userId)
		{
			$results = Labori_DB::performQuery($conn, "SELECT roles.* FROM users
													   right join user_role_assignments
													   on users.user_id = user_role_assignments.user_id
													   right join roles
													   on user_role_assignments.role_id = roles.id
													   where users.user_id = '" . Labori_DB::escStr($conn, $userId) . "';");

		    $retArray = array("global_permissions" => array(
		    					"is_super_admin" => false,
		    					"can_log_in" => false,
		    					"can_access_all_pages" => false,
		    					"can_simulate_user" => false,
		    					"can_assign_roles" => false,
		    					"can_delete_meta_fields" => false
		    				  ),
		    				  "role_tag" => "unknown",
		    				  "default_page" => "home",
							  "page_permissions" => array());

			if($results["row_count"] >= 1)
			{
				foreach($results["rows"] as $thisRow)
				{
					if(!is_null($thisRow["role_tag"]))
					{
						$retArray["role_tag"] = $thisRow["role_tag"];
					}

					if(Labori_Utl::streql($thisRow["is_super_admin"], "Y"))
					{
						$retArray["global_permissions"]["is_super_admin"] = true;
					}

					if(Labori_Utl::streql($thisRow["can_log_in"], "Y"))
					{
						$retArray["global_permissions"]["can_log_in"] = true;
					}

					if(Labori_Utl::streql($thisRow["can_access_all_pages"], "Y"))
					{
						$retArray["global_permissions"]["can_access_all_pages"] = true;
					}

					if(Labori_Utl::streql($thisRow["can_simulate_user"], "Y"))
					{
						$retArray["global_permissions"]["can_simulate_user"] = true;
					}

					if(Labori_Utl::streql($thisRow["can_assign_roles"], "Y"))
					{
						$retArray["global_permissions"]["can_assign_roles"] = true;
					}

					if(Labori_Utl::streql($thisRow["can_delete_meta_fields"], "Y"))
					{
						$retArray["global_permissions"]["can_delete_meta_fields"] = true;
					}

					if(!is_null($thisRow["page_permissions"]))
					{
						$retArray["page_permissions"][] = json_decode($thisRow["page_permissions"], true);
					}

					if(!is_null($thisRow["default_page"]))
					{
						$retArray["default_page"] = $thisRow["default_page"];
					}
				}
			}

			return $retArray;
		}

		public static function reloadSession()
		{
			if(isset($_SESSION['user_id']))
			{
				$conn = Labori_DB::genConnHelper("primary_database_id");
				$results = Labori_DB::performQuery($conn, "SELECT *
				                         				   FROM users
				                         				   WHERE user_id = '" . Labori_DB::escStr($conn, $_SESSION['user_id']) . "'");

				if($results["row_count"] >= 1)
				{	
					$permissions = self::parsePermissions($conn, $results["rows"][0]["user_id"]);
					
					if($permissions['global_permissions']['is_super_admin'] || 
					   $permissions['global_permissions']['can_log_in'])
					{
						$_SESSION['user_id'] = $results["rows"][0]["user_id"];
						$_SESSION['user_name'] =  $results["rows"][0]["user_name"];
						$_SESSION['user_email'] =  $results["rows"][0]["user_email"];
						$_SESSION['permissions'] = $permissions;
					}

					if(!$permissions['global_permissions']['is_super_admin'] && 
						$permissions['global_permissions']['can_simulate_user'] && 
					  (!isset($_SESSION["simulating_user"]) || !$_SESSION["simulating_user"]))
					{
						$tempParsedSimulationList = Labori_Utl::parseDelimitedStr("~", $results["rows"][0]["user_simulation_list"]);
						$tempArray = array();
						foreach($tempParsedSimulationList as $thisUserID)
						{
							$tempArray[$thisUserID] = $thisUserID;
						}

						$_SESSION["user_simulation_list"] = $tempArray;
					}
					else
					{
						$_SESSION["user_simulation_list"] = array();
					}
				}
			}
		}

		public static function getSessionVariable($var)
		{
			if(!isset($_SESSION[$var]))
			{
				throw new Exception("Required session variable cannot be found");	
				self::destroySession();
			}
			else
			{
				return $_SESSION[$var];
			}
		}

		public static function setSessionVariable($var, $value)
		{
			if (session_status() == PHP_SESSION_NONE) 
			{
			    throw new Exception("Login session cannot be found");
			}
			else
			{
				$_SESSION[$var] = $value;
			}
		}

		public static function destroySession()
		{
			$_SESSION = array();
			session_destroy();
		}
	}
?>