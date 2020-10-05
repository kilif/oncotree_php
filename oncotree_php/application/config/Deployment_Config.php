<?php
	class Deployment_Config
	{
		const DEPLOYMENT_ENV = "local";

		const CORE_SETTINGS = array(
			"server_root" => array(
				"local" => "http://localhost:8888/",
				"wake_staging" => "http://wimms-staging.wakehealth.edu/",
				"wake_prod" => "http://wimms.wakehealth.edu/"
			),
			"labori_upload" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/uploads/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/uploads/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/uploads/",
			),
			"labori_php_implemented" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/implemented/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/implemented/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/implemented/",
			),

			"labori_php_root_pages" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/root_pages/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/root_pages/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/root_pages/",
			),

			"labori_php_service_scripts" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/service_scripts/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/service_scripts/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/service_scripts/",
			),

			"labori_php_report_scripts" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/report_scripts/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/report_scripts/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/report_scripts/",
			),

			"labori_php_sub_pages" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/sub_pages/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/sub_pages/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/sub_pages/",
			),

			"labori_php_helper_scripts" => array(
				"local" => "C:/xampp_7/htdocs/" . Instance_Settings::IS_MAIN_DIR . "/application/php/helper_scripts/",
				"wake_staging" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/helper_scripts/",
				"wake_prod" => "/var/www/html/" . Instance_Settings::IS_MAIN_DIR . "/application/php/helper_scripts/",
			),

			"application_js_callback" => array(
				"local" => "/" . Instance_Settings::IS_MAIN_DIR . "/application/js/callback_functions.js",
				"wake_staging" => "/" . Instance_Settings::IS_MAIN_DIR . "/application/js/callback_functions.js",
				"wake_prod" => "/" . Instance_Settings::IS_MAIN_DIR . "/application/js/callback_functions.js",
			),

			"UMLS_API_KEY" => array(
				"local" => "6d65dbcd-afb9-48f4-a0ff-b6d47ae58d0c",
				"wake_staging" => "6d65dbcd-afb9-48f4-a0ff-b6d47ae58d0c",
				"wake_prod" => "6d65dbcd-afb9-48f4-a0ff-b6d47ae58d0c",
			),
		);

		const OTHER_SETTINGS = array(
		);
	}
?>