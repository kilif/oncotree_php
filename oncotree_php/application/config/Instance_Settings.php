<?php
	class Instance_Settings
	{
		//Deployment
		public const IS_MAIN_DIR = "oncotree_php";
	}

	if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET["CONST_NAME"]))
	{
		try
		{
			$constant = 'Instance_Settings::'.strtoupper(trim($_GET["CONST_NAME"]));
		    
		    if(defined($constant)) 
		    {
		        echo constant($constant);
		    }
		}
		catch(Exception $e)
		{
		}
	}
?>