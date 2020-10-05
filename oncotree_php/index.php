<?php
	require_once dirname(__FILE__) . '/labori_core/native/php/Labori_Core.php';

	class MainIndex
	{
		const SERVER_DIR = "C:/xampp_7/htdocs/";

		public static function renderPage()
		{
			$header = "";
			$body = "";

			$header .= '
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta http-equiv="cache-control" content="no-cache, must-revalidate" />
			<title>Oncotree Parser</title>';
			$header .= Labori_Core::importAllScripts(Labori_Core::getRootDir());
			$header .= '<script type="text/javascript" src="' . Labori_Core::getDeploymentOption("application_js_callback", true) ."?reload=" . Labori_Utl::generateUUID_urlSafe() .'"></script>';

			$body = '
				<div style="display:none;" id="labori_popup_overlay">
						<div id="labori_popup_container">
							<div id="labori_popup_cap"></div>
							<div id="labori_popup_body" class="group"></div>
						</div>
					</div>
				<div id="labori_core_content" style="width: 600px;	margin-right: auto;	margin-left: auto; margin-top: 100px;" class="group">
					<div style="" class="labori_content_block_container group">
					   	<div style="" class="labori_content_block_cap">
					   		<i class="fa fa-bar-chart" aria-hidden="true"></i> Oncotree Parser
					   	</div>
				    <div style="" class="labori_content_block_content">
				    	<div id="import_holder" style="width: 100%; max-width: 100%;" class="labori_labeled_field group">
							<div class="labori_labeled_field_label">DX Excel File
							<div class="labori_field_sub_message">Please upload an excel file (xlsx) that contains cancer diagnosis text, one per each cell, arranged in Column A
							<br><br><b>NOTE: This operation can take several minutes as it must call outside APIs. Please allow it to finish.</b></div></div>
							<div class="labori_labeled_field_field">
								<input required type="text" style="width:100%; display:none;" ' . 
							       Labori_Widget::genMetaField("dx_upload", "labori_text_input","free_text", 
							       "dx_upload", "file_uuid", "upload_button") . 
							      ' />
							    <div style="text-align: center; margin-top: 15px;">
					   				<label id="' . 'dx_upload' . '_label" class="labori_button_round_wide green_button labori_button_round">
							   			<input id="' . 'dx_upload' . '_upload" type="file"/>
							   			<span id="' . 'dx_upload' . '_upload_button_text" >Upload...</span>
							   		</label>
							   	</div>

							   	' . Labori_Widget::genUploadFunctionWithoutJob("dx_upload_upload", 'dx_upload' . "_upload_button_text", 
																				   '<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>',
					   															   "UPLOADER", 
					   															   array("label_id" => 'upload_dx' . "_label",
																						 "labori_required_for" => "upload_button",
																						 "accepted_file_extensions" => "xlsx"), 
					   															   Labori_Router::TYPE_SERV, 
					   															   'Request_OncoTree',
					   														       null, 
					   														       "request_uploadDxFile", 
					   														       "application_uploadCallback", "dx_upload")
							   	. '
							</div>
						</div> 

						<div id="export_holder" style="display:none; width: 100%; max-width: 100%;" class="labori_labeled_field group">
							<div class="labori_labeled_field_label">Export Results
							<div class="labori_field_sub_message">Once the parsing is complete, use this button to download your results.</div></div>
							<div style="text-align: center;">
								<div id="export_button" class="labori_button_round_wide labori_button_round green_button" style="margin-top:10px; margin-left:30px;" onclick="' . 
								 	 Labori_Widget::genButtonFunction('export_button', 
									 "Exporting", 
									 "<i style='margin-right:5px;' class='fa fa-file-excel-o' aria-hidden='true'></i> Export to Excel", 
									 "na", 
									 array("export_type" => "excel", "export_file_name" => "oncotree"), 
									 Labori_Router::TYPE_SERV, 
									 "Request_OncoTree", 
									 null, 
									 "request_exportToExcel", 
									 "labori_callback_debug",
									 'index', true) .'">
									 <i style=\'margin-right:5px;\' class=\'fa fa-file-excel-o\' aria-hidden=\'true\'></i> Export to Excel
								 </div>
							</div>
						</div>

						<div id="reset_holder" style="display:none; width: 100%; max-width: 100%;" class="labori_labeled_field group">
							<div class="labori_labeled_field_label">Reset
							<div class="labori_field_sub_message">Press this to reset the interface and allow for another DX file upload.</div></div>
							<div style="text-align: center;">
								<div id="export_button" class="labori_button_round_wide labori_button_round green_button" style="margin-top:10px; margin-left:30px;" onclick="window.location=\'./\';">
									 <i style=\'margin-right:5px;\' class=\'fa fa-refresh\' aria-hidden=\'true\'></i> Reset
								 </div>
							</div>
						</div>
				    </div>
				</div>
			';


			echo "<html>
					<head>
				 		$header
				 	</head>

				 	<body class='group'>
				 		$body
					</body>
				  </html>";
		}
	}

	if(Labori_Utl::streql($_SERVER['REQUEST_METHOD'], "GET"))
	{
		MainIndex::renderPage();
	}
	else if(Labori_Utl::streql($_SERVER['REQUEST_METHOD'], "POST"))
	{
	}
?>