<?php
	if (session_status() !== PHP_SESSION_ACTIVE) 
	{
		session_set_cookie_params(86400);
		ini_set('session.gc_maxlifetime', 86400);
		session_start();
	} 
    
	ini_set('mysql.connect_timeout', 30000);
	ini_set('default_socket_timeout', 30000); 
	Labori_Core::setupErrorReporting();

	/*APPLICATION*/
	require_once dirname(__FILE__) . '/../../../application/config/Labori_Config.php';

	/*LABORI CORE*/
	require_once dirname(__FILE__) . '/support/Labori_Exception.php';
	require_once dirname(__FILE__) . '/support/Labori_Utl.php';
	require_once dirname(__FILE__) . '/support/Labori_DB.php';
	require_once dirname(__FILE__) . '/support/Labori_Widget.php';
	require_once dirname(__FILE__) . '/support/Labori_Router.php';
	require_once dirname(__FILE__) . '/support/Labori_Session.php';
	require_once dirname(__FILE__) . '/support/Labori_File.php';
	require_once dirname(__FILE__) . '/base/Request_Handler.php';
	require_once dirname(__FILE__) . '/base/Application_Page.php';
	require_once dirname(__FILE__) . '/base/Root_Page.php';
	require_once dirname(__FILE__) . '/base/report/Report_Query_Condition.php';
	require_once dirname(__FILE__) . '/base/report/Base_Report.php';
	require_once dirname(__FILE__) . '/base/report/Report_List_Helper.php';
	require_once dirname(__FILE__) . '/abstract/Logging_Manager.php';
	require_once dirname(__FILE__) . '/abstract/Login_Manager.php';
	
	/*THIRD PARTY*/
	require_once dirname(__FILE__) . '/../../third_party/php/php-login/minimal_password.php';
	require_once dirname(__FILE__) . '/../../third_party/php/php_simple_dom/php_simple_dom.php';
	
	class Labori_Core
	{
		const BASE_COLOR_1 = "#FFCF44";
		const BASE_COLOR_1_NO_HASH = "FFCF44";

		public static function getRootDir()
		{
			$path = dirname(__FILE__);

			if(Labori_Utl::strContains("\\", $path))
			{
				$path = explode("\\", $path);
				return($path[count($path)-4]);
			}
			else
			{
				$path = explode("/", $path);
				return($path[count($path)-4]);
			}
		}

		public static function genErrorMessage($message)
		{
			$rootDir = self::getRootDir();
			$traceMessage = debug_backtrace();
			$traceEcho = "<div class='imp_text'>Stack Trace</div>";

			for($i = 0; $i < count($traceMessage); $i++)
			{
				$traceEcho .= "[" . $i ."]";

				if(isset($traceMessage[$i]["file"]))
				{
					$traceEcho .= "<div style='padding-left:50px;'>File (<span class='imp_text'>" . $traceMessage[$i]["file"]. "</span>)</div>";
				}

				if(isset($traceMessage[$i]["line"]))
				{
					$traceEcho .= "<div style='padding-left:50px;'>Line (<span class='imp_text'>" . $traceMessage[$i]["line"]. "</span>)</div>";
				}

				if(isset($traceMessage[$i]["class"]))
				{
					$traceEcho .= "<div style='padding-left:50px;'>Class (<span class='imp_text'>" . $traceMessage[$i]["class"]. "</span>)</div>";
				}

				if(isset($traceMessage[$i]["function"]))
				{
					$traceEcho .= "<div style='padding-left:50px;'>Function (<span class='imp_text'>" . $traceMessage[$i]["function"]. "</span>)</div>";
				}

				if(isset($traceMessage[$i]["args"]))
				{
					//$traceEcho .= "<div style='padding-left:50px;'>Arguments (<span class='imp_text'>" . var_export($traceMessage[$i]["args"], true). "</span>)</div>";
				}
			}

			return '<div style="margin-left:100px; width: 90%;" class="labori_content_block_container group">
					<div class="labori_content_block_cap">
					' . "<i class='fa fa-warning' aria-hidden='true'></i>" .' Site Error 
					</div>
					<div  class="labori_content_block_content">
					An unrecoverable error occured during the execution of the site. 
					The administration has been alerted and the error has been
					logged.
					<br>
					<span class="imp_text">Please refrain from attempting to perform whatever
					action led to this error again for a few hours or until 
					the administration has given an all clear.</span>
					<div class="labori_content_block_divider"></div>
					<span style="font-size:9pt;">' . $message . '</span>
					<div class="labori_content_block_divider"></div>'
					. $traceEcho .
					'</div>
					</div>
					';
		}

		public static function setupErrorReporting()
		{
			set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) 
			{
				ob_start();

				echo Labori_Core::genErrorMessage("ERROR <span class='imp_text'>$errno</span> in file 
												   <span class='imp_text'>$errfile</span> on line 
												   <span class='imp_text'>$errline</span>: <br>" . $errstr);

				ob_end_flush();
			});

			register_shutdown_function(function() 
			{
			    $errfile = "unknown file";
			    $errstr  = "shutdown";
			    $errno   = E_CORE_ERROR;
			    $errline = 0;

			    $error = error_get_last();

			    if( $error !== NULL)
			    {
			        $errno   = $error["type"];
			        $errfile = $error["file"];
			        $errline = $error["line"];
			        $errstr  = $error["message"];

			        ob_start();

					echo Labori_Core::genErrorMessage("ERROR <span class='imp_text'>$errno</span> in file 
													   <span class='imp_text'>$errfile</span> on line 
													   <span class='imp_text'>$errline</span>: <br>" . $errstr);

					ob_end_flush();    
			    } 
			});
		}

		public static function importAllScripts($rootDir)
		{
			$retVal = "";
			$retVal .= '<!--[if lt IE 9 ]>';
			$retVal .= '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/selectivizr-min.js"></script>';
			$retVal .= '<![endif]-->';
			$retVal .= '<!--[if gte IE 9 | !IE ]><!-->';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/jquery-3.3.1.min.js"></script>';
			$retVal .= '<![endif]-->';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/jquery-ui.min.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/native/js/labori.js' ."?reload=" . Labori_Utl::generateUUID_urlSafe() . '"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/native/js/labori_report.js' ."?reload=" . Labori_Utl::generateUUID_urlSafe() . '"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/flatpickr.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/progressbar.min.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/dateJS.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/timesheet.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/moment.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/Chart.bundle.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/js_scroll_bar/jquery.mCustomScrollbar.concat.min.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/html_editor/trumbowyg.min.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/js_editor/codemirror.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/js_editor/mode/javascript/javascript.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/core.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/charts.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/plugins/forceDirected.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/themes/dark.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/themes/kelly.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/amcharts/themes/animated.js"></script>';
			$retVal .= '<script type="text/javascript" src="/'. $rootDir .'/labori_core/third_party/js/tablesorter-master/dist/js/jquery.tablesorter.js"></script>';

			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/css/dna-loading.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/resources/fonts/font-awesome/css/font-awesome.min.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/resources/fonts/open-sans/open-sans.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/css/flatpickr/ie.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/css/flatpickr/themes/dark.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/native/css/labori_core.css' ."?reload=" . Labori_Utl::generateUUID_urlSafe() . '">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/css/timesheet.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/js/js_scroll_bar/jquery.mCustomScrollbar.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/js/html_editor/ui/trumbowyg.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/js/js_editor/codemirror.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/js/js_editor/codemirror.css">';
			$retVal .= '<link type="text/css" rel="stylesheet" href="/'. $rootDir .'/labori_core/third_party/js/tablesorter-master/dist/css/theme.labori.css">';

			//Leaflet
			$retVal .= '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
					   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
					   crossorigin=""/>';
			$retVal .= '<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
					   integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
					   crossorigin=""></script>';

			return $retVal;
		}

		public static function getDeploymentOption($optionId, $coreSetting=false)
		{
			$settingArray = Deployment_Config::OTHER_SETTINGS;

			if($coreSetting)
			{
				$settingArray = Deployment_Config::CORE_SETTINGS;
			}

			if(array_key_exists($optionId, $settingArray) &&
			   array_key_exists(Deployment_Config::DEPLOYMENT_ENV, $settingArray[$optionId]))
			{
				return $settingArray[$optionId][Deployment_Config::DEPLOYMENT_ENV];
			}
			else
			{
				throw new Exception("Deployment option could not be found in deployment file.");
			}	
		}

		public static function validateRequestKey($requestKey)
		{
			//TODO handle keys
			return true;
		}
	}
?>