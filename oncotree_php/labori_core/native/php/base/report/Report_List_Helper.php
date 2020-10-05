<?php
	require_once dirname(__FILE__) . '/../../Labori_Core.php';

	abstract class Report_List_Helper extends Application_Page
	{
		private $reportList = array();
		private $reportParentDirectory = "";

		protected function methodIsService_extra($methodName) {return null;}
		protected abstract function getCustomPermissionList(&$permissionList);
		public abstract function buildPage($rootDir, $pageRequest);

		public function methodIsService($methodName)
		{
			$temp = $this->methodIsService_extra($methodName);

			if(!is_null($temp) && $temp)
			{
				return true;
			}
			else if(Labori_Utl::streql($methodName, "request_getReportList"))
			{
				return true;
			}
		}

		public function initiateReportList()
		{
			$classList = Labori_Router::findAllClasses(Labori_Router::TYPE_RPRT, $this->getReportParentDirectory());

			foreach($classList as $thisClass)
			{
				if(!$thisClass->getIsRestrictedByInstanceTypes())
				{
					$this->addToReportList($thisClass);
				}
			}
		}

		public function addToReportList($newBaseReport)
		{
			$this->reportList[get_class($newBaseReport)] = $newBaseReport;
		}

		public function setReportParentDirectory($reportParentDirectory)
		{
			$this->reportParentDirectory = $reportParentDirectory;
		}

		public function getReportList()
		{
			return $this->reportList;
		}

		public function getReportParentDirectory()
		{
			return $this->reportParentDirectory;
		}

		static function helper_generateQueryBuilderInterface($tempReportDir, $tableColumns, $pageName = "Page_Reports", $reportPageDir = null)
		{
			if(is_null($reportPageDir))
			{
				$reportPageDir = $tempReportDir;
			}

			$retVal = "";
			$reportPageClass = Labori_Router::findClass(Labori_Router::TYPE_SUB, $pageName, $reportPageDir . "/");
			$queriesServiceClass = Labori_Router::findClass(Labori_Router::TYPE_SERV, "Service_Queries");

			$reportList = $reportPageClass->getReportList();
			$conditionIncludeTextArray = Report_Query_Condition::getConditionIncludeTextArray();
			$availableQueries = array();
			$availableConditions = array();
			$reportsOptions = "";
			$tableData = array();
			
			foreach($reportList as $reportClassName => $thisReport)
			{
				$availableQueries += $queriesServiceClass->getQueries($tempReportDir, $reportClassName);
				$reportsOptions .= "<option value='$reportClassName'>" . $thisReport->getReportName() . "</option>";
				$availableConditions[$reportClassName] = $thisReport->getPossibleConditions();
			}
	
			foreach($availableQueries as $thisQueryUUID => $thisQuery)
			{
				$tempReportName = "";

				if(isset($reportList[$thisQuery["report_class_name"]]))
				{
					$tempReportName = $reportList[$thisQuery["report_class_name"]]->getReportName();
				}

				$queryHTML = '<div style="display:none;" class="' . $thisQueryUUID . '_vgid"><div style="width:100%; margin-bottom:10px;" class="group">';
				$groupList = json_decode($thisQuery["query"], true);
				$firstGroup = true;

				foreach($groupList as $thisGroupID => $thisGroupJSON)
				{
					if($firstGroup)
					{
						$firstGroup = false;
					}
					else
					{
						$queryHTML .= '<div class="query_divider">OR</div>';
					}

					$queryHTML .= '<div class="labori_query_group"><div class="padding_div"><div class="group">';
					$queryHTML .= '<div class="labori_group_title_light">Query Group</div>';
					$queryHTML .= '<div class="condition_container group">';

					$conditionList = json_decode($thisGroupJSON, true);

					$firstCondition = true;

					foreach($conditionList as $thisConditionUUID => $thisConditionJSON)
					{
						$thisCondition = json_decode($thisConditionJSON, true);

						if(isset($thisCondition["condition_id"]) &&
						   isset($thisCondition["include_if"]))
						{
							//Do nothing
						}
						else
						{
							continue;
						}

						if($firstCondition)
						{
							$firstCondition = false;
						}
						else
						{
							$queryHTML .= '<div class="condition_divider"><div class="query_divider_strikethrough"></div>AND<div class="query_divider_strikethrough"></div></div>';
						}

						$queryHTML .= '<div class="query_condition group">';
						$queryHTML .= '<div class="group"><span style="float:left; color: #717A85;"><i class="fa fa-cogs" aria-hidden="true"></i> Query Condition</span></div>';
						

						if(isset($availableConditions[$thisQuery["report_class_name"]][$thisCondition["condition_id"]]))
						{
							$thisConditionArrayObj = $availableConditions[$thisQuery["report_class_name"]][$thisCondition["condition_id"]]->getObjectAsAssociativeArray();

							$queryHTML .= '<div class="group query_condition_inner_element">';
							$queryHTML .= '<div class="labori_labeled_field_label imp_text">Field</div>';
							$queryHTML .= '<div class="labori_labeled_field_field">';
							$queryHTML .= '<div class="loaded_query_field_value" style="margin-bottom:0px;">' . $thisConditionArrayObj["condition_name"] . '</div>';
							$queryHTML .= '</div>';
							$queryHTML .= '</div>';

							if(isset($thisCondition["condition_historic"]))
							{
								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Use History</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';
								$queryHTML .= '<div class="loaded_query_field_value" style="margin-bottom:0px;">' . $thisCondition["condition_historic"] . '</div>';
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';
							}

							if(isset($conditionIncludeTextArray[$thisConditionArrayObj["condition_type"]]) && 
							   isset($conditionIncludeTextArray[$thisConditionArrayObj["condition_type"]][$thisCondition["include_if"]]))
							{
								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Include If</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';
								$queryHTML .= '<div class="loaded_query_field_value" style="margin-bottom:0px;">' . $conditionIncludeTextArray[$thisConditionArrayObj["condition_type"]][$thisCondition["include_if"]] . '</div>';
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';
							}

							if(isset($thisCondition["value"]))
							{
								$valueArray = array();

								if(Labori_Utl::streql(Report_Query_Condition::TYPE_TEXT, $thisConditionArrayObj["condition_type"]) ||
								   Labori_Utl::streql(Report_Query_Condition::TYPE_MULTI_TEXT, $thisConditionArrayObj["condition_type"]))
								{
									$valueArray = explode("~", $thisCondition["value"]);
								}
								else if (Labori_Utl::streql(Report_Query_Condition::TYPE_MULTI_DROPDOWN, $thisConditionArrayObj["condition_type"]) ||
								  		 Labori_Utl::streql(Report_Query_Condition::TYPE_DROPDOWN, $thisConditionArrayObj["condition_type"]))
								{
									$tempArray = explode("~", $thisCondition["value"]);

									foreach($tempArray as $thisValue)
									{
										foreach($thisConditionArrayObj["condition_values"] as $thisIDTextPair)
										{
											if(Labori_Utl::streql($thisIDTextPair["id"], $thisValue))
											{
												$valueArray[] = $thisIDTextPair["text"];
												break;
											}
										}
									}
								}
								else
								{
									$valueArray[] = $thisCondition["value"];
								}

								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Value</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';

								foreach($valueArray as $thisValue)
								{
									$queryHTML .= '<div class="loaded_query_field_value">' . $thisValue . '</div>';
								}
								
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';
							}
						}

						
						$queryHTML .= '</div>';
					}

					$queryHTML .= '</div></div></div></div>';
				}

				$queryHTML .= '</div>';

				if(isset($thisQuery["query_input_fields"]) && !empty($thisQuery["query_input_fields"]) && $thisQuery["query_input_fields"] != '{}')
				{
					$queryHTML .= '<div style="width:100%; margin-bottom:10px;" class="group">';
					$queryHTML .= '<div class="labori_query_field_group">
								   <div class="padding_div">
								   <div class="labori_group_title_dark">Query Input Fields</div>';

					$groupedInputFields = json_decode($thisQuery["query_input_fields"], true);

					foreach($groupedInputFields as $groupId => $inputFieldJSON)
					{
						$queryHTML .= '<div class="query_condition group">';
						$queryHTML .= '<div class="group"><span style="float:left; color: #717A85;"><i class="fa fa-cog" aria-hidden="true"></i> Query Input Field</span></div>';
						
						$actualInputField = json_decode($inputFieldJSON, true);
						if(isset($actualInputField["condition_id"]))
						{
							if(Labori_Utl::streql($actualInputField["condition_id"], "GLOBAL___date_range"))
							{
								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Field</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';
								$queryHTML .= '<div class="loaded_query_field_value">Start/End Date Range</div>';
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';
							}
							else if(Labori_Utl::streql($actualInputField["condition_id"], "GLOBAL___member_selector"))
							{
								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Field</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';
								$queryHTML .= '<div class="loaded_query_field_value">Member Selector</div>';
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';
							}
							else if(isset($availableConditions[$thisQuery["report_class_name"]][$actualInputField["condition_id"]]))
							{
								$queryHTML .= '<div class="group query_condition_inner_element">';
								$queryHTML .= '<div class="labori_labeled_field_label imp_text">Field</div>';
								$queryHTML .= '<div class="labori_labeled_field_field">';
								$queryHTML .= '<div class="loaded_query_field_value">' . $availableConditions[$thisQuery["report_class_name"]][$actualInputField["condition_id"]]->getObjectAsAssociativeArray()["condition_name"] . '</div>';
								$queryHTML .= '</div>';
								$queryHTML .= '</div>';

								if($availableConditions[$thisQuery["report_class_name"]][$actualInputField["condition_id"]]->getConditionHistoric() && isset($actualInputField["condition_historic"]))
								{
									$queryHTML .= '<div class="group query_condition_inner_element">';
									$queryHTML .= '<div class="labori_labeled_field_label imp_text">Use History</div>';
									$queryHTML .= '<div class="labori_labeled_field_field">';
									$queryHTML .= '<div class="loaded_query_field_value">' . $actualInputField["condition_historic"] . '</div>';
									$queryHTML .= '</div>';
									$queryHTML .= '</div>';
								}
							}
						}

						$queryHTML .= '</div>';
					}

					$queryHTML .= '</div></div>';
					$queryHTML .= '</div>';
				}

				$queryHTML .= '</div>';
				$queryHTML .= '<div style="width:100%; text-align:center;">
								<div class="labori_tooltip labori_button_round ' . $thisQueryUUID . '_vgid" onclick="toggleVisualGroupVisibility(\'' . $thisQueryUUID . '_vgid\');">
								 	<i class="fa fa-eye" aria-hidden="true"></i>
								 	<span class="labori_tooltiptext">View Query Details</span>
							 	</div>';

				$queryHTML .= '<div style="display:none;" class="labori_query_button_long red_button ' . $thisQueryUUID . '_vgid" onclick="toggleVisualGroupVisibility(\'' . $thisQueryUUID . '_vgid\');">
								 	<i class="fa fa-eye" aria-hidden="true"></i> Hide Query
							 	</div>
							 	</div>';

				$tableData[] =	array(
								"row_data"=> array(
									"query_name"=> 		'<div>' . $thisQuery["query_name"] . '</div>',
									"query_description"=> 	'<div>' . $thisQuery["query_description"] . '</div>',
									"associated_report"=>  '<div>' .$tempReportName . '</div>',
								   	"query" =>				$queryHTML,
									"query_manage" => '<div id="create_button_' . $thisQueryUUID .'" style="display:none;" class="labori_tooltip labori_button_round green_button ' . $thisQueryUUID . '_vgid" onclick="' . 
													     	Labori_Widget::genButtonFunction('create_button_' . $thisQueryUUID, 
													     									 "<i class='fa fa-check' aria-hidden='true'></i>", 
					    							     									 "<i class='fa fa-check' aria-hidden='true'></i>", 
					    							     									 "create_" . $thisQueryUUID, 
					    							     									 array("query_uuid" => $thisQueryUUID), 
														 									 Labori_Router::TYPE_SERV, 
														 									 "Service_Queries", 
														 									 null, 
														 									 "request_editQuery", 
														 									 "labori_callback_reloadOnSuccess", 
														 									 "index.php") 
													     		.'; toggleVisualGroupVisibility(\'' . $thisQueryUUID . '_vgid\');" >
														 		<i class="fa fa-check" aria-hidden="true"></i>

														</div>

														<div id="delete_button_' . $thisQueryUUID .'" class="labori_tooltip labori_button_round red_button ' . $thisQueryUUID . '_vgid" onclick="labori_prepareYesNoDialog(' . 
														 	"&quot;<i class='fa fa-warning' aria-hidden='true'></i> Confirm Deletion&quot;," .
														 	"&quot;Are you sure you want to delete <span class='imp_text_red'>" . $thisQuery["query_name"] . "</span>?&quot;," .
														 	'&quot;' .
													     	Labori_Widget::genButtonFunction('delete_button_' . $thisQueryUUID, 
													     									 "<i class=`fa fa-close` aria-hidden=`true`></i>", 
					    							     									 "<i class=`fa fa-close` aria-hidden=`true`></i>", 
					    							     									 "delete_" . $thisQueryUUID, 
					    							     									 array("query_uuid" => $thisQueryUUID, "created_by" => Labori_Session::getSessionVariable("user_name")), 
														 									 Labori_Router::TYPE_SERV, 
														 									 "Service_Queries", 
														 									 null, 
														 									 "request_deleteQuery", 
															"labori_callback_simpleReload", "index.php") .'&quot;)" >
				   												<i class="fa fa-close" aria-hidden="true"></i>
				   												<span class="labori_tooltiptext">Delete Query</span>
				   											</div>'
								),
								"row_item_style"=>array(
								
									"query_manage" => "text-align:center;",
								)
							);			
			}

			$tableData[] =	array(
								"row_data"=> array(
									"query_name"=> '<input type="text" style="width:100%;" ' . 
												      Labori_Widget::genMetaField("new_query_name", "labori_text_input","free_text", "create", "query_name", "create_button") . 
												      ' required />',

									"query_description" => '<textarea rows="1" cols="50" style="width:100%;" ' .
												      Labori_Widget::genMetaField("new_query_description", "labori_textarea","free_text", "create", "query_description", "create_button") . 
												      ' required></textarea>',

									"associated_report" => '<select type="text" style="width:100%;" ' . 
														      Labori_Widget::genMetaField("new_query_assoc_report", "","dropdown", "create", "report_class_name") . 
														     ' ">' . $reportsOptions .'</select>',

									"query" => 			'
														<div style="width:100%; margin-bottom:10px;" ' . Labori_Widget::genMetaField("new_query_holder", "group","json_group", "create", "query") . '>
														</div>

														<div style="display:none; width:100%; margin-bottom:10px;" ' . Labori_Widget::genMetaField("new_query_input_holder", "group","json_group", "create", "query_input_fields") . '>	
															<div class ="labori_query_field_group">
																<div class="padding_div">
																<div class="labori_group_title_dark">Query Input Fields</div>
																</div>
															</div>
														</div>

														<div class="labori_query_button_long green_button" onclick="labori_report_addQueryGroup(\'new_query_holder\', \'new_query_assoc_report\', \'' . $pageName . '\', \'' . $reportPageDir .'/\')">
														 	<i class="fa fa-plus-circle" aria-hidden="true"></i> Add Group
													 	</div>

													 	<div class="labori_query_button_long" onclick="labori_report_addQueryInput(\'new_query_input_holder\', \'new_query_assoc_report\', \'' . $pageName . '\', \'' . $reportPageDir .'/\')">
														 	<i class="fa fa-plus-circle" aria-hidden="true"></i> Add Query Input Field
													 	</div>',
									"query_manage" => '<div id="create_button" class="labori_tooltip labori_button_round green_button disabled" onclick="' . 
													    Labori_Widget::genButtonFunction("create_button",  "<i class='fa fa-plus' aria-hidden='true'></i>", "<i class='fa fa-plus' aria-hidden='true'></i>", "create", 
													    array("created_by" => Labori_Session::getSessionVariable("user_name"), "report_parent_dir" => $tempReportDir), 
				   										Labori_Router::TYPE_SERV, "Service_Queries", null, "request_saveQuery", "labori_callback_simpleReload", "index.php") .'" >
														 <i class="fa fa-plus" aria-hidden="true"></i>
														 <span class="labori_tooltiptext_noHover">Don\'t forget to press this button to add the query!</span>
														 <span class="labori_tooltiptext">Add Query</span>
														 </div>'
								),
								"row_item_style"=>array(
								
									"query_manage" => "text-align:center;"
								)
							);

			$editBlock = Labori_Widget::generateTable($tableColumns, $tableData, "query_edit");
			$retVal .= Labori_Widget::generateContentBlock("Edit Queries", $editBlock, $icon = '<i class="fa fa-wrench" aria-hidden="true"></i>');
			return $retVal;
		}

		public function helper_generateReportInterface()
		{
			$retHTML = "";
			$firstReport = true;

			foreach($this->getReportList() as $thisReportClassName => $thisReport)
			{
				$fileName = str_replace(" ", "_", $thisReport->getReportName());
				$fileName = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $fileName);
				$fileName = preg_replace("([\.]{2,})", '', $fileName);

				$tempHTML = '	
						   		<div class="labori_content_block_tab_container group">
									<div id="' . $thisReportClassName . '_options_tab" class="labori_content_block_tab selected_tab '. $thisReportClassName . '_tab" 
									onclick="labori_report_handleReportTabs(\'' . $thisReportClassName . '_hide_group\', \'' . $thisReportClassName . '_options_tab\', \'' . $thisReportClassName . '_tab\', \'' . $thisReportClassName . '_options_container\');">Options</div>
									<div id="' . $thisReportClassName . '_tab_group" class="group" style="display:inline-block; float:left;">
									</div>
								</div>

								<div id="' . $thisReportClassName . '_html_container">
								</div>

								<div class="' . $thisReportClassName . '_hide_group" id="' . $thisReportClassName . '_options_container">
						   		<table class="labori_report_body group">
						   			<tr>
							   			<td class="labori_report_desc">
							   				' . $thisReport->getReportDescription() . '
							   			</td>

							   			';
				$availableQueries = array();
				$queryOptionsList = "";
				$userOptionsList = "";

				$serviceQueriesClass = Labori_Router::findClass(Labori_Router::TYPE_SERV, "Service_Queries");
				$availableQueries = $serviceQueriesClass->getQueries($this->reportParentDirectory, $thisReportClassName);
				$possibleConditions = $thisReport->getPossibleConditions();
				$namesAndDesc = '';
				$first = null;
				$containerStyle = "";
				

				foreach($availableQueries as $thisQuery)
				{
					if(is_null($first))
					{
						$first = $thisQuery["query_uuid"] . "-show";
					}

					$queryInputFieldHTML = "";
					$queryInputFieldHTML .= '<input type="text" style="display:none; width:100%;" 
												' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input","free_text", $thisQuery["query_uuid"], "query_uuid") . 
			      								'value = "' . $thisQuery["query_uuid"] . '" />';

					if(!is_null($thisQuery["query_input_fields"]))
					{
						$tempJSONGroupID = "query_input_fields_group_" . Labori_Utl::generateUUID_urlSafe();
						$queryInputFieldHTML .= '<div ' . 
								 		 		Labori_Widget::genMetaField($tempJSONGroupID, "group","json_group", $thisQuery["query_uuid"], "query_input_fields") .
								 		 		'>';

						$groupedInputFields = json_decode($thisQuery["query_input_fields"], true);

						foreach($groupedInputFields as $groupId => $inputFieldJSON)
						{
							$actualInputField = json_decode($inputFieldJSON, true);

							if(isset($actualInputField["condition_id"]))
							{
								if(Labori_Utl::streql("GLOBAL___date_range", $actualInputField["condition_id"]))
								{
									$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">Start Date</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input labori_date_field","free_text", $tempJSONGroupID, "GLOBAL__start_date") . 
								      								'/> 
																</div>
															</div>';

									$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">End Date</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input labori_date_field","free_text", $tempJSONGroupID, "GLOBAL__end_date") . 
								      								'/> 
																</div>
															</div>';
								}
								else if(Labori_Utl::streql("GLOBAL___member_selector", $actualInputField["condition_id"]))
								{
									$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">Member Selector</div>
																<div class="labori_labeled_field_field">' . 
																Labori_Widget::generateAutoCompleteInput(Labori_Utl::generateUUID_urlSafe(), null, Labori_Router::TYPE_SERV, 
														 		 "Service_Members", null, "request_autocomplete_getMembers",
								 						 		 "", array(),
							      								 null, $tempJSONGroupID, "GLOBAL__member_list") .
															'</div>
															</div>';
								}
								else if(isset($possibleConditions[$actualInputField["condition_id"]]))
								{
									$conditionObjArray = $possibleConditions[$actualInputField["condition_id"]]->getObjectAsAssociativeArray();

									if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_TEXT))
									{

										$tempID = Labori_Utl::generateUUID_urlSafe();

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .'</div>
																<div class="labori_labeled_field_field">';

										$queryInputFieldHTML .= Labori_Widget::generateMultiInsertInput($tempID, 
					 											Labori_Widget::genMetaField($tempID, "labori_text_input",
					 											"multi_input", $tempJSONGroupID, $actualInputField["condition_id"]));

										$queryInputFieldHTML .= '</div></div>';
									}
									else if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_NUMBER))
									{
										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .' (Start-Inclusive)</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input","free_text", $tempJSONGroupID, $actualInputField["condition_id"] . "___~number~start") . 
								      								'/> 
																</div>
															</div>';

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .' (End-Inclusive)</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input","free_text", $tempJSONGroupID, $actualInputField["condition_id"] . "___~number~end") . 
								      								'/> 
																</div>
															</div>';
									}
									else if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_DATE))
									{
										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .' (Start-Inclusive)</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input labori_date_field","free_text", $tempJSONGroupID, $actualInputField["condition_id"] . "___~date~start") . 
								      								'/> 
																</div>
															</div>';

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .' (End-Inclusive)</div>
																<div class="labori_labeled_field_field">
																<input type="text" style="width:100%;" 
																	' . Labori_Widget::genMetaField(Labori_Utl::generateUUID_urlSafe(), "labori_text_input labori_date_field","free_text", $tempJSONGroupID, $actualInputField["condition_id"] . "___~date~end") . 
								      								'/> 
																</div>
															</div>';
									}
									else if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_DROPDOWN))
									{
										$tempID = Labori_Utl::generateUUID_urlSafe();
										$tempOptions = array();

										foreach($conditionObjArray["condition_values"] as $thisCondition)
										{
											$tempOptions[$thisCondition["id"]] = array("text" => $thisCondition["text"]);
										}

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .'</div>
																<div class="labori_labeled_field_field">';

										$queryInputFieldHTML .= Labori_Widget::generateMultiDropdown($tempID, 
																Labori_Widget::genMetaField($tempID, "","multi_input", $tempJSONGroupID, $actualInputField["condition_id"]), 
					 					   						$tempOptions, null, "width:99%;");

										$queryInputFieldHTML .= '</div></div>';
									}
									else if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_MULTI_DROPDOWN))
									{
										$tempID = Labori_Utl::generateUUID_urlSafe();
										$tempOptions = array();

										foreach($conditionObjArray["condition_values"] as $thisCondition)
										{
											$tempOptions[$thisCondition["id"]] = array("text" => $thisCondition["text"]);
										}

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .'</div>
																<div class="labori_labeled_field_field">';

										$queryInputFieldHTML .= Labori_Widget::generateMultiDropdown($tempID, 
																Labori_Widget::genMetaField($tempID, "","multi_input", $tempJSONGroupID, $actualInputField["condition_id"]), 
					 					   						$tempOptions, null, "width:99%;");

										$queryInputFieldHTML .= '</div></div>';
									}
									else if(Labori_Utl::streql($conditionObjArray["condition_type"], Report_Query_Condition::TYPE_MULTI_TEXT))
									{
										$tempID = Labori_Utl::generateUUID_urlSafe();

										$queryInputFieldHTML .= '<div class="group labori_simple_dark_field_group">
																<div class="labori_labeled_field_label">' . $conditionObjArray["condition_name"] .'</div>
																<div class="labori_labeled_field_field">';

										$queryInputFieldHTML .= Labori_Widget::generateMultiInsertInput($tempID, 
					 											Labori_Widget::genMetaField($tempID, "labori_text_input",
					 											"multi_input", $tempJSONGroupID, $actualInputField["condition_id"]));

										$queryInputFieldHTML .= '</div></div>';
									}
								}
							}
						}

						$queryInputFieldHTML .= "</div>";
					}

					$queryOptionsList .= '<option onclick="$(\'.hide_me_' . $thisReportClassName .'\').hide(); $(\'#' . $thisQuery["query_uuid"] . '-show\').show();" value="' . $thisQuery["query_uuid"] . '">' . $thisQuery["query_name"] .'</option>';
					$namesAndDesc .= '<div id="' . $thisQuery["query_uuid"]. '-show" class="hide_me_' . $thisReportClassName . '" style="display:none;">
									<div class="group labori_simple_dark_field_group">
										<div class="labori_labeled_field_label">Query Name</div>
										<div class="labori_labeled_field_field">
											' . $thisQuery["query_name"] .'
										</div>
									</div>

									<div class="group labori_simple_dark_field_group">
										<div class="labori_labeled_field_label">Query Description</div>
										<div class="labori_labeled_field_field">
											' . $thisQuery["query_description"] .'
										</div>
									</div>';

					$namesAndDesc .= $queryInputFieldHTML;

					$namesAndDesc .= '</div>';
				}

				if(!is_null($first))
				{
					$tempHTML .= '<script>
								$(document).ready(function() {
								  $("#' . $first . '").show();
								});
								</script>';
				}


				$tempHTML .= '			<td class="labori_report_fields"> 
											<div class="group labori_table_field_group" style="padding:10px;">
											  	<select type="text" style="width:100%;" ' .
									      	  		Labori_Widget::genMetaField($thisReportClassName . "_query_selector", "","dropdown",  "query_save_" . $this->reportParentDirectory . "___" . $thisReportClassName, "query_uuid") . '>' . $queryOptionsList .'</select>
										  		
												<div class="labori_content_divider"></div>

												' . $namesAndDesc .'
											</div>
										</td>';

				$tempHTML .=	'
							   			<td class="labori_report_export">' .	   				
								   			$this->generateReportButtons($fileName, $thisReportClassName, 
								   										 $this->reportParentDirectory, $thisReportClassName, $thisReport->getExports())
							   			. '</td>
							   		</tr>
						   		</table>
						   		</div>
						   ';

				
			
				if($firstReport)
				{
					$retHTML .= Labori_Widget::generateContentBlock($thisReport->getReportName(),$tempHTML,'<i class="fa fa-file" aria-hidden="true"></i>');
					$firstReport = false;
				}
				else
				{
					$retHTML .= Labori_Widget::generateContentBlock($thisReport->getReportName(),$tempHTML,'<i class="fa fa-file" aria-hidden="true"></i>', "margin-top:20px;");
				}
			}

			return $retHTML;
		}

		private function generateReportButtons($exportFileName, $reportID, $reportClassDirectory, $reportClassName, $exportTypes)
		{
			$retHTML = '';

			$iconArray = array(
				"excel" => "fa fa-file-excel-o",
				"html" => "fa fa-th",
				"pdf" => "fa fa-file-pdf-o",
				"word" => "fa fa-file-word-o",
				"csv" => "fa fa-file-text-o"
			);

			foreach($exportTypes as $thisType => $canExport)
			{
				if($canExport)
				{
					if(Labori_Utl::streql($thisType, "html"))
					{
						$retHTML .= '<div id="' . $reportID . '_' . $thisType . '_report" class="labori_tooltip labori_report_button" onclick="' . 
					    			Labori_Widget::genButtonFunction($reportID . '_' . $thisType . '_report',  
					    			"<i class='fa fa-circle-o-notch fa-spin'></i>",
					    			"<i class='" . $iconArray[$thisType] . "' aria-hidden='true'></i><span class='labori_tooltiptext'>Generate Web Page Report</span>", 
					    			"$('#" . $reportClassName . "_query_selector').val()", 
					    			array("export_type" => $thisType, "export_file_name" => $exportFileName),
									Labori_Router::TYPE_RPRT, $reportClassName, $reportClassDirectory, "request_generateReport", 
									"labori_report_callback_addReportTab", $reportClassName, false, array("groupID" => "groupID")) .'" >
						 			<i class= "' . $iconArray[$thisType] . '" aria-hidden="true"></i>
						 			<span class="labori_tooltiptext">Generate Web Page Report</span>
						 			</div>';		
					}
					else
					{
						$retHTML .= '<div id="' . $reportID . '_' . $thisType . '_report" class="labori_tooltip labori_report_button" onclick="' . 
					    			Labori_Widget::genButtonFunction($reportID . '_' . $thisType . '_report',  
					    			"<i class='fa fa-circle-o-notch fa-spin'></i>", 
					    			"<i class='" . $iconArray[$thisType] . "' aria-hidden='true'></i><span class='labori_tooltiptext'>Export to ". strtoupper($thisType) . "</span>", 
					    			"$('#" . $reportClassName . "_query_selector').val()", 
					    			array("export_type" => $thisType, "export_file_name" => $exportFileName),
									Labori_Router::TYPE_RPRT, $reportClassName, $reportClassDirectory, "request_generateReport", 
									"labori_callback_fileDownload", "index.php", true, array("groupID" => "groupID")) .'" >
						 			<i class= "' . $iconArray[$thisType] . '" aria-hidden="true"></i>
						 			<span class="labori_tooltiptext">Export to '. strtoupper($thisType) . '</span>
						 			</div>';
					}
				}
				else
				{
					$retHTML .= '<div id="' . $reportID . '_' . $thisType . '_report" class="labori_report_button disabled" onclick="' . 
				    			Labori_Widget::genButtonFunction($reportID . '_' . $thisType . '_report',  
				    			"<i class='fa fa-circle-o-notch fa-spin'></i>", "<i class='" . $iconArray[$thisType] . "' aria-hidden='true'></i>", 
				    			"$('#" . $reportClassName . "_query_selector').val()", 
				    			array("export_type" => $thisType, "export_file_name" => $exportFileName),
								Labori_Router::TYPE_RPRT, $reportClassName, $reportClassDirectory, "request_generateReport", 
								"labori_callback_fileDownload", "index.php", true, array("groupID" => "groupID")) .'" >
					 			<i class= "' . $iconArray[$thisType] . '" aria-hidden="true"></i>
					 			</div>';	
				}
			}

			return $retHTML;
		}

		/**********************************************************/
		/*REQUEST SERVICES                                        */
		/**********************************************************/
		public function request_getReportList($args)
		{
			if(isset($args["report_class_name"]))
			{
				if(isset($reportList[$args["report_class_name"]]))
				{
					return json_encode(array("success" => true, "response" => array($args["report_class_name"] => 
																			 $reportList[$args["report_class_name"]]->getObjectAsAssociativeArray())));
				}
				else
				{
					return json_encode(array("success" => false, "response" => "Could not find the requested report class"));
				}
			}
			else
			{
				$retArray = array();

				foreach($reportList as $thisReportClassName => $thisReport)
				{
					$retArray[$thisReportClassName] = $thisReport->getObjectAsAssociativeArray();
				}

				return json_encode(array("success" => true, "response" => $retArray));
			}
		}
	}
?>