<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';

	class Labori_Widget
	{
		/**********************************************************/
		/*HTML WIDGETS				                         	  */
		/**********************************************************/
		/*
		This function accepts the following array structures:

			$columns = 
			[
				{
					column_id:"column_id",
					column_html: html,
					column_style: (null|custom)
					column_type: (null|"text")
					column_classes: "column classes"
				},
				{
					column_id:"column_id",
					column_html: html,
					column_style: ("default"|custom)
					column_type: ("text")
				},
				...
			]

			$data =
			[
				{
					row_style: ("default"|custom),
					row_data:
					{
						"column_id":html,
						"column_id":html,
						"column_id":html,
					}
				},
				{
					row_style: ("default"|custom),

					row_item_style:
					{
						"column_id":style
					},

					row_data:
					{
						"column_id":html,
						"column_id":html,
						"column_id":html,
					}
				},
				...
			]
		*/
		public static function generateTable($columns, $data, $tableID, $specialStyle = '', $tableTitle = null, $priorRowsHTML = "", 
											 $extraTableClasses = "", $tableMeta = "")
		{
			$retVal = '<table ' . $tableMeta . ' style="' . $specialStyle . '" class="labori_table ' . $extraTableClasses .'" id="' . $tableID . '">';
			
			if($tableTitle != null)
			{
				$retVal .= '<tr sortable=\'false\' class="labori_table_header labori_table_header_title">';
				$retVal .= '<th colspan="' . count($columns) . '" class="labori_table_header_item labori_table_title tablesorter-ignoreRow">' . $tableTitle . '</th>';
				$retVal .= '</tr>';
			}
			
			$retVal .= '<tr  sortable=\'false\' id="' . $tableID . '__header" class="labori_table_header">';

			foreach($columns as $thisColumnInfo)
			{
				$tempStyle = '';
				$tempType = 'text';
				$tempHTML = '';
				$tempColID = '';
				$tempClasses = '';

				if(isset($thisColumnInfo["column_style"]))
				{
					$tempStyle = $thisColumnInfo["column_style"];
				}

				if(isset($thisColumnInfo["column_type"]))
				{
					$tempType = $thisColumnInfo["column_type"];
				}

				if(isset($thisColumnInfo["column_html"]))
				{
					$tempHTML = $thisColumnInfo["column_html"];
				}

				if(isset($thisColumnInfo["column_html"]))
				{
					$tempHTML = $thisColumnInfo["column_html"];
				}

				if(isset($thisColumnInfo["column_id"]))
				{
					$tempColID = $thisColumnInfo["column_id"];
				}

				if(isset($thisColumnInfo["column_classes"]))
				{
					$tempClasses = $thisColumnInfo["column_classes"];
				}

				$retVal .= '<th id="' . $tableID . '__c__' . $tempColID . '" style="' . $tempStyle .'" class="labori_table_header_item ' . $tempClasses . '">';
				$retVal .= $tempHTML;
				$retVal .= '</th>';
			}

			$retVal .= '</tr>' . $priorRowsHTML;
			$rowCount = 0;
			foreach($data as $thisRow)
			{
				$tempStyle = '';

				if(isset($thisRow["row_style"]))
				{
					$tempStyle = $thisRow["row_style"];
				}

				$retVal .= '<tr ';

				if(isset($thisRow["row_meta"]))
				{
					$retVal .= $thisRow["row_meta"];
				}

				if(isset($thisRow["id_overide"]))
				{
					$retVal .= ' id="' . $thisRow["id_overide"] . '" ';
				}
				else
				{
					$retVal .= ' id="' . $tableID . '__r__' . $rowCount . '" ';
				}

				if(isset($thisRow["row_additionalClasses"]))
				{
					$retVal .= ' style="' . $tempStyle .'" class="labori_table_row ' . $thisRow["row_additionalClasses"] . '">';
				}
				else
				{
					$retVal .= ' style="' . $tempStyle .'" class="labori_table_row">';
				}

				if(isset($thisRow["row_data"]))
				{
					foreach($columns as $thisColumnInfo)
					{
						$tempRowStyle = '';

						if(isset($thisColumnInfo["column_id"]) && isset($thisRow["row_item_style"]) && 
							isset($thisRow["row_item_style"][$thisColumnInfo["column_id"]]))
						{
							$tempRowStyle = $thisRow["row_item_style"][$thisColumnInfo["column_id"]];
						}

						if(isset($thisColumnInfo["column_id"]) && 
						   isset($thisRow["row_data"][$thisColumnInfo["column_id"]]))
						{
							$tempData = "";

							if(is_null($thisRow["row_data"][$thisColumnInfo["column_id"]]) || empty(trim($thisRow["row_data"][$thisColumnInfo["column_id"]])))
							{
								$tempData = "";
							}
							else
							{
								$tempData = $thisRow["row_data"][$thisColumnInfo["column_id"]];
							}

							$retVal .= '<td style="' . $tempRowStyle .'" id="' . $tableID . '__i__' . $rowCount . '__' . $thisColumnInfo["column_id"] . 
										'" class="labori_table_row_item">' . 
										$tempData . '</td>';
						}
						else if(!isset($thisRow["row_data"][$thisColumnInfo["column_id"]]))
						{
							$retVal .= '<td style="' . $tempRowStyle .'" id="' . $tableID . '__i__' . $rowCount . '__' . $thisColumnInfo["column_id"] . 
										'" class="labori_table_row_item">' . '</td>';
						}
					}
				}

				$retVal .= '</tr>';
				$rowCount++;
			}

			return $retVal . '</table>';
		}

		public static function generatePageSelector($baseURL, $countPer, $totalCount, $currentPage)
		{
			if(is_null($countPer) || (!Labori_Utl::streql($countPer, "all") && !is_numeric($countPer)))
			{
				$countPer = 25;
			}

			$numOfPages = max(1, ceil($totalCount/$countPer));

			$retVal = '<span class="labori_page_selector group">';

			if($currentPage+1 == 1)
			{
				$retVal .= '<div class="labori_page_selector_button disabled"><i class="fa fa-fast-backward" aria-hidden="true"></i></div>';
				$retVal .= '<div class="labori_page_selector_button disabled"><i class="fa fa-backward" aria-hidden="true"></i></div>';
			}
			else
			{
				$retVal .= '<div onclick="window.location=\'' . $baseURL . '&count_per=' . $countPer . '&page=0' . '\'" class="labori_page_selector_button"><i class="fa fa-fast-backward" aria-hidden="true"></i></div>';
				$retVal .= '<div onclick="window.location=\'' . $baseURL . '&count_per=' . $countPer . '&page=' . ($currentPage-1) . '\'" class="labori_page_selector_button"><i class="fa fa-backward" aria-hidden="true"></i></div>';
			}

			$retVal .= '<span style="float:left; margin-right:5px;">Page ' .  ($currentPage + 1) . ' of ' . $numOfPages . '</span>';

			if($currentPage+1 == $numOfPages)
			{
				$retVal .= '<div class="labori_page_selector_button disabled"><i class="fa fa-forward" aria-hidden="true"></i></div>';
				$retVal .= '<div class="labori_page_selector_button disabled"><i class="fa fa-fast-forward" aria-hidden="true"></i></div>';
			}
			else
			{
				$retVal .= '<div onclick="window.location=\'' . $baseURL . '&count_per=' . $countPer . '&page=' . ($currentPage+1) . '\'" class="labori_page_selector_button"><i class="fa fa-forward" aria-hidden="true"></i></div>';
				$retVal .= '<div onclick="window.location=\'' . $baseURL . '&count_per=' . $countPer . '&page=' . ($numOfPages-1) . '\'" class="labori_page_selector_button"><i class="fa fa-fast-forward" aria-hidden="true"></i></div>';
			}

			return $retVal . '</span>';
		}

		public static function generateLoadingAnimation($specialStyle='')
		{
			return '<div style="'.$specialStyle.'" class="dna-spinner loader">
				      <div class="wrapper">
				        <div class="row row-1">
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				        </div>
				        <div class="row row-2">
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				          <span></span>
				        </div>
				        <div class="dna-spinner-message">LOADING</div>
				      </div>
				    </div>';
		}

		public static function generateContentBlock($title, $body, $icon = "", $containerStyle = "", 
												   $capStyle = "", $contentStyle = "")
		{

			$retVal = '<div style="' . $containerStyle . '" class="labori_content_block_container group">
					   <div style="' . $capStyle . '" class="labori_content_block_cap">
					   ' . $icon .' ' . $title . '
					   </div>
					   <div style="' . $contentStyle . '" class="labori_content_block_content">
					   ' . $body . '
					   </div></div>';

			return $retVal;
		}

		public static function generateFileBlock($title, $type, $body, $url, $icon)
		{
			$hoverContent = '<i class="fa fa-download" aria-hidden="true"></i><br>
						   			 Download File';

			if(Labori_Utl::streql($type, "link"))
			{
				$hoverContent = '<i class="fa fa-external-link" aria-hidden="true"></i><br>
						   			Follow Link';
			}

			$retVal = '<div onclick="window.location=\''. $url .'\'" class="labori_content_file_block">
						   <div class="labori_content_file_block_cap">
						  	 	' . $icon . ' ' . $title . '
						   </div>
						   <div class="labori_content_file_block_body group">
						   		<div class="labori_content_file_block_content">
						   		<div class="labori_content_file_block_content_inner">' . $body . '</div>
						   		<div class="labori_content_file_block_body_download">
						   			 ' . $hoverContent . '
						   		</div>
						   		</div>
						   </div>
					   </div>';

			return $retVal;
		}

		public static function generateLinkBlock($title, $body, $url, $icon, $containerStyle = "",  
												 $capStyle = "", $contentStyle = "", $capIcon = "fa fa-chevron-circle-right", $altOnClick = '')
		{
			if(empty($altOnClick))
			{
				$retVal = '<div onclick="window.location=\''. $url .'\'"
						   style="' . $containerStyle . '" class="labori_content_link_block">
						   <div style="' . $capStyle . '" class="labori_content_link_block_cap">
						   <i class="' . $capIcon . '" aria-hidden="true"></i> ' . $title . '
						   </div>
						   <div class="labori_content_link_block_body group">
						   <div style="' . $contentStyle . '" class="labori_content_link_block_content">
						   ' . $body . '
						   </div>
						   <div class="labori_content_link_block_icon">' . $icon . '</div>
						   </div>
						   </div>';
			}
			else
			{
				$retVal = '<div onclick="' . $altOnClick . '"
						   style="' . $containerStyle . '" class="labori_content_link_block">
						   <div style="' . $capStyle . '" class="labori_content_link_block_cap">
						   <i class="' . $capIcon . '" aria-hidden="true"></i> ' . $title . '
						   </div>
						   <div class="labori_content_link_block_body group">
						   <div style="' . $contentStyle . '" class="labori_content_link_block_content">
						   ' . $body . '
						   </div>
						   <div class="labori_content_link_block_icon">' . $icon . '</div>
						   </div>
						   </div>';
			}

			return $retVal;
		}

		public static function generateAutoCompleteInputSingle($inputID, $call_optArgs, $call_fileType, $call_targetClass, $call_parentRoute, $call_action,
															 $selectedItem = null, $optionList = null,
														     $otherClasses ="", $groupId = null, $metaId = null, $requiredFor = null, 
														     $inputStyle = "", $holderStyle ="", $containerStyle = "",
														     $extraOnInput = '', $extraOnBlur = '', $extraOnItemChange = '')
		{
			$holderHTML = "";
			$tempOnChange = "";
			$hideInput = "";
			$required = "";

			if(!is_null($requiredFor))
			{
				$required = "required";
			}

			if(!is_null($selectedItem) && !empty($selectedItem))
			{
				$hideInput = "display:none;";
				if(!empty($extraOnItemChange))
				{
					$tempOnChange = $extraOnItemChange .'(\'' . $selectedItem . '\',false);';
				}

				if(isset($optionList[$selectedItem]))
				{
					$holderHTML .= '<div value="' . $selectedItem . '" class="labori_autocomplete_single_holder_item group">
									<div class="labori_autocomplete_single_holder_item_html" style="float:left; display:inline-block;">' . $optionList[$selectedItem] . '</div>
									<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" 
									onclick="labori_autoCompleteSingle_deleteItem(\'' .  $inputID .'\',this);' . $tempOnChange . '">
									<i class=\'fa fa-close\' aria-hidden=\'true\'></i>
									<span class="labori_tooltiptext">Delete Item</span>
									</div>
									</div>';
				}
				else
				{
					$holderHTML .= '<div value="' . $selectedItem . '" class="labori_autocomplete_single_holder_item group">
									<div class="labori_autocomplete_single_holder_item_html" style="float:left; display:inline-block;">' . $selectedItem . '</div>
									<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" 
									onclick="labori_autoCompleteSingle_deleteItem(\'' .  $inputID .'\',this);' . $tempOnChange . '">
									<i class=\'fa fa-close\' aria-hidden=\'true\'></i>
									<span class="labori_tooltiptext">Delete Item</span>
									</div>
									</div>';
				}
			}

			$inputStyle = "width: 100%; float:left;" . $inputStyle;
			$extraOnInput .= 'labori_callAutoCompleteFunctionSingle(' . self::convertToJSArgs(array("inputID" => $inputID,
																							  "optArgs" => $call_optArgs,
																							  "type" => $call_fileType,
																							  "targetClass" => $call_targetClass,
																							  "parentRoute" => $call_parentRoute,
																							  "action" => $call_action)) 
						     . ');' . $extraOnInput;

			$extraOnBlur .= 'labori_hideAutoComplete(\''.$inputID .'\');' . $extraOnBlur;
			$otherClasses .= " labori_text_input";

			$retVal = '<div style="' . $containerStyle . '">
						<div class="group">
							<input onfocus="labori_checkAutoCompleteField(this);" ' . $required . ' labori_onItemChange="' . $extraOnItemChange . '" style="' . $hideInput . $inputStyle . '" ' . 
							self::genMetaField($inputID, $otherClasses, "autocomplete_single", $groupId, $metaId, 
						  	$requiredFor, $extraOnInput, $extraOnBlur) . ' type="text" value="Start typing to search...">
							<div style="position:relative;">
								<div id = "' . $inputID .'-autocomplete" style="width:100%;" class="labori_autocomplete_list">
								</div>
							</div>
						</div>
						<div id="'. $inputID .'-autocomplete-single-holder" class="labori_autocomplete_single_holder" style="' . $holderStyle . '">' .
						$holderHTML .
						'</div>
					   </div>';

			return $retVal;
		}

		public static function generateAutoCompleteInput($inputID, $call_optArgs, $call_fileType, $call_targetClass, $call_parentRoute, $call_action,
														 $selectedList = null, $optionList = null,
													     $otherClasses ="", $groupId = null, $metaId = null, $requiredFor = null, 
													     $inputStyle = "", $holderStyle ="", $containerStyle = "",
													     $extraOnInput = '', $extraOnBlur = '', $extraOnItemChange = '')
		{
			$holderHTML = "";

			if(!is_null($selectedList) && !empty($selectedList))
			{
				$valueIdList = array();

				if(Labori_Utl::strContains("~", $selectedList))
				{
					$valueIdList = explode("~", $selectedList);
				}
				else
				{
					$valueIdList[] = $selectedList;
				}

				$tempOnChange = "";		

				foreach($valueIdList as $thisID)
				{
					if(!empty($extraOnItemChange))
					{
						$tempOnChange = $extraOnItemChange .'(\'' . $thisID . '\',false);';
					}

					if(isset($optionList[$thisID]))
					{
						$holderHTML .= '<div value="' . $thisID . '" class="labori_multiinput_holder_item group">
										<div style="float:left; display:inline-block;">' . $optionList[$thisID] . '</div>
										<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" 
										onclick="labori_multiinput_deleteItem(this);' . $tempOnChange . '">
										<i class=\'fa fa-close\' aria-hidden=\'true\'></i>
										<span class="labori_tooltiptext">Delete Item</span>
										</div>
										</div>';
					}
					else
					{
						$holderHTML .= '<div value="' . $thisID . '" class="labori_multiinput_holder_item group">
										<div style="float:left; display:inline-block;">' . $thisID . '</div>
										<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" 
										onclick="labori_multiinput_deleteItem(this);' . $tempOnChange . '">
										<i class=\'fa fa-close\' aria-hidden=\'true\'></i>
										<span class="labori_tooltiptext">Delete Item</span>
										</div>
										</div>';
					}
				}
			}

			$inputStyle = "width: 85%; float:left;" . $inputStyle;
			$extraOnInput .= 'labori_callAutoCompleteFunction(' . self::convertToJSArgs(array("inputID" => $inputID,
																							  "optArgs" => $call_optArgs,
																							  "type" => $call_fileType,
																							  "targetClass" => $call_targetClass,
																							  "parentRoute" => $call_parentRoute,
																							  "action" => $call_action)) 
						     . '); labori_autoCompleteReminder(this); ' . $extraOnInput;

			$extraOnBlur .= 'labori_hideAutoComplete(\''.$inputID .'\');' . $extraOnBlur;
			$otherClasses .= " labori_text_input";

			$requirementStr = "";
			if(!is_null($requiredFor))
			{
				$requirementStr = "labori_required_for = '" . $requiredFor . "'";
			}

			$retVal = '<div style="' . $containerStyle . '">
						<div class="group">
						<input ' . $requirementStr . ' labori_onItemChange="' . $extraOnItemChange . '" style="' . $inputStyle . '" ' . self::genMetaField($inputID, $otherClasses, "multi_input", $groupId, $metaId, 
																				  $requiredFor, $extraOnInput, $extraOnBlur) . ' type="text">
						<div class="labori_tooltip labori_button_round small_button" style="float:right; display:inline-block;" 
						onclick="labori_multiinput_insertMultiInputChoice(\''. $inputID .'\');">
						<i class=\'fa fa-plus\' aria-hidden=\'true\'></i>
						<span class="labori_tooltiptext">Add Item</span>
						<span class="labori_tooltiptext_noHover">Don\'t forget to press this button or click an item in the autocomplete list to add the item.</span>
						</div>
						<div style="position:relative;">
							<div id = "' . $inputID .'-autocomplete" class="labori_autocomplete_list">
							</div>
						</div>
						</div>
						<div id="'. $inputID .'-multiinput-holder" class="labori_multiinput_holder" style="' . $holderStyle . '">' .
						$holderHTML .
						'</div>
					   </div>';

			return $retVal;
		}

		public static function generateMultiInsertInput($inputID, $inputMeta, $inputList = null,
													 	$inputStyle = "", $holderStyle ="", $containerStyle = "", $inputValidation = "", 
													 	$requiredFor = null)
		{
			$holderHTML = "";

			if(!is_null($inputList) && !empty($inputList))
			{
				$valueIdList = array();

				if(Labori_Utl::strContains("~", $inputList))
				{
					$valueIdList = explode("~", $inputList);
				}
				else
				{
					$valueIdList[] = $inputList;
				}

				foreach($valueIdList as $thisID)
				{
					$holderHTML .= '<div value="' . Labori_Utl::escapeDoubleQuotes($thisID) . '" class="labori_multiinput_holder_item group">
									<div style="float:left; display:inline-block;">' . $thisID . '</div>
									<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" 
									onclick="labori_multiinput_deleteItem(this);">
									<i class=\'fa fa-close\' aria-hidden=\'true\'></i>
									<span class="labori_tooltiptext">Delete Item</span>
									</div>
									</div>';
				}
			}

			$inputStyle = "width: 85%; float:left;" . $inputStyle;

			$requirementStr = "";

			if(!is_null($requiredFor))
			{
				$requirementStr = "labori_required_for = '" . $requiredFor . "'";
			}

			$retVal = '<div style="' . $containerStyle . '">
						<div class="group" style="min-width: 200px;">
						<input oninput="labori_autoCompleteReminder(this);" ' . $requirementStr . ' ' . $inputValidation . ' style="' . $inputStyle . '" ' . $inputMeta . ' type="text">
						<div id="' . $inputID .'_button" class="labori_tooltip labori_button_round small_button" style="float:right; display:inline-block;" 
						onclick="labori_multiinput_insertMultiInputChoice(\''. $inputID .'\');">
						<i class=\'fa fa-plus\' aria-hidden=\'true\'></i>
						<span class="labori_tooltiptext">Add Item</span>
						<span class="labori_tooltiptext_noHover">Don\'t forget to press this button to add the item.</span>
						</div>
						</div>
						<div id="'. $inputID .'-multiinput-holder" class="labori_multiinput_holder" style="' . $holderStyle . '">' .
						$holderHTML .
						'</div>
					   </div>';

			return $retVal;
		}

		/*
		This function accepts the following array structures:

			$optionList = 
			{
				optionID:
				{
					text:value,
					onclick:some_function (optional)
				},

				optionID2:
				{
					text:value,
					onclick:some_function (optional)
				},

				...
			}
		*/
		public static function generateMultiDropdown($selectID, $selectMeta, $optionList, $selectedList = null,
													 $selectStyle = "", $holderStyle ="", $containerStyle = "", $extraHTML = "", $requiredFor = null)
		{
			$optionHTML = "";

			foreach($optionList as $optionVal => $optionMeta)
			{
				if(isset($optionMeta["text"]))
				{
					$tempMeta = "";

					if(isset($optionMeta["meta"]))
					{
						$tempMeta .= $optionMeta["meta"];
					}

					if(isset($optionMeta["onclick"]))
					{
						$optionHTML .= '<option ' . $extraHTML . ' ' . $tempMeta . ' onclick="labori_multidropdown_insertMultiDropdownChoice(\''. $selectID .'\', this); ' . $optionMeta["onclick"] . ';" 
										value=\'' .$optionVal . '\'>' . $optionMeta["text"] . '</option>';
					}
					else
					{
						$optionHTML .= '<option ' . $tempMeta . ' onclick="labori_multidropdown_insertMultiDropdownChoice(\''. $selectID .'\',this);" value=\'' .$optionVal . '\'>' . $optionMeta["text"] . '</option>';
					}
				}
			}

			$holderHTML = "";

			if(!is_null($selectedList) && !empty($selectedList))
			{
				$valueIdList = array();

				if(!is_array($selectedList))
				{
					if(Labori_Utl::strContains("~", $selectedList))
					{
						$valueIdList = explode("~", $selectedList);
					}
					else
					{
						$valueIdList[] = $selectedList;
					}
				}
				else
				{
					foreach($selectedList as $id => $value)
					{
						$valueIdList[] = $id;
					}
				}

				foreach($valueIdList as $thisID)
				{
					if(isset($optionList[$thisID]))
					{
						$holderHTML .= '<div value="' . $thisID . '" class="labori_multiinput_holder_item group">
										<div style="float:left; display:inline-block;">' . $optionList[$thisID]["text"] . '</div>
										<div class="labori_button_round tiny_button" style="float:right; display:inline-block;" 
										onclick="labori_multiinput_deleteItem(this);">
										<i class=\'fa fa-close\' aria-hidden=\'true\'></i></div>
										</div>';
					}
				}
			}

			$requirementStr = "";

			if(!is_null($requiredFor))
			{
				$requirementStr = "labori_required_for = '" . $requiredFor . "'";
			}

			$retVal = '<div style="' . $containerStyle . '">
						<select ' . $requirementStr .' labori_multi_select="true" style="' . $selectStyle . '" type="text" ' . $selectMeta . '>' . $optionHTML . '</select>
						<div id="'. $selectID .'-multiinput-holder" class="labori_multiinput_holder" style="' . $holderStyle . '">' .
						$holderHTML .
						'</div>
					   </div>';

			return $retVal;
		}

		/**********************************************************/
		/*JOB LOG WIDGETS                             	  		  */
		/**********************************************************/
		public static function jobLogFactory($msg, $type = "normal")
		{
			if(Labori_Utl::streql($type, "good"))
			{
				return self::generateJobLogMessage($msg, "green", "fa-check-circle");
			}
			else if(Labori_Utl::streql($type, "caution"))
			{
				return self::generateJobLogMessage($msg, "yellow", "fa-exclamation-triangle");
			}
			else if(Labori_Utl::streql($type, "bad"))
			{
				return self::generateJobLogMessage($msg, "red", "fa-remove");
			}
			else if(Labori_Utl::streql($type, "important"))
			{
				return self::generateJobLogMessage($msg, "yellow", "fa-check-circle", true);
			}
			else
			{
				return self::generateJobLogMessage($msg);
			}
		}

		public static function generateJobLogMessage($msg, $color = null, $icon = "fa-comment", $important = false)
		{
			$tempColor = $color; 

			if(Labori_Utl::streql($color, "red"))
			{
				$tempColor = "#FF6951"; 
			}
			else if(Labori_Utl::streql($color, "green"))
			{
				$tempColor = "#1EB980"; 
			}
			else if(Labori_Utl::streql($color, "yellow"))
			{
				$tempColor = "#FFCF44"; 
			}

			if($important)
			{
				return '<span style="color:#44C0FF;"><i class="fa ' . $icon . '" aria-hidden="true"></i> ' . $msg . '</span>';
			}
			else
			{
				if(!is_null($tempColor))
				{
					return '<span style="color:' . $color .';"><i class="fa ' . $icon . '" aria-hidden="true"></i></span> ' . $msg . '';
				}
				else
				{
					return '<span style=""><i class="fa ' . $icon . '" aria-hidden="true"></i></span> ' . $msg . '';
				}
			}
		}

		/**********************************************************/
		/*META ATTRIBUTE UTILITIES                             	  */
		/**********************************************************/
		public static function markMetaVisualGroup($visualGroup)
		{
			return "labori__meta_visual_group_id = '$visualGroup'";
		}

		public static function genMetaField($id, $otherClasses ="", $fieldType = "free_text", $groupId = null, 
										   $metaId = null, $requiredFor=null, $extraOnInput = '', $extraOnBlur = '')
		{
			if(is_null($metaId))
			{
				$metaId = $id;
			}

			$requiredForClass = "";
			$requiredFunction = "";

			if(!is_null($requiredFor))
			{
				$requiredForClass = "labori_required_for_" . $requiredFor;

				$args["this"] = "this";
				$args["buttonID"] = $requiredFor;
				$args["rerquired_class"] = $requiredForClass;

				$requiredFunction = ' oninput = "labori_toggleButtonLock(' . self::convertToJSArgs($args) .'); ' . $extraOnInput. '; labori_checkInput(this);" 
									  onblur = " labori_toggleButtonLock(' . self::convertToJSArgs($args) .'); ' . $extraOnBlur. '; labori_checkInput(this);"';
			}
			else
			{
				$requiredFunction = ' oninput = "' . $extraOnInput. '; labori_checkInput(this);" 
									  onblur = "' . $extraOnBlur. '; labori_checkInput(this);"';
			}

			$temp = "id='$id' labori__meta_id='$metaId' class='labori__field $requiredForClass $otherClasses' $requiredFunction labori__field_type='$fieldType'";

			if(!is_null($groupId))
			{
				$temp .= " labori__group_id='$groupId'";
			}

			return $temp;
		}

		/**********************************************************/
		/*JAVASCRIPT BUILDERS                               	  */
		/**********************************************************/
		public static function generateDateScatterChart($containerID, $data, $yAxesData, $seriesData)
		{
			$content = '<script>
							am4core.ready(function() {

							// Themes begin
							am4core.useTheme(am4themes_dark);
							am4core.useTheme(am4themes_animated);

							var chart = am4core.create("' . $containerID . '", am4charts.XYChart);

							chart.colors.step = 4;
							var chartData = [];
						';

			$dataKeys = null;

			foreach($data as $thisDataSet)
			{
				$tempDataStr = null;

				if(is_null($dataKeys))
				{
					$dataKeys = array_keys($thisDataSet);
				}
				
				foreach($thisDataSet as $thisKey => $thisValue)
				{
					if(preg_match("/([0-9][0-9][0-9][0-9])-([0-9]?[0-9])-([0-9]?[0-9])/", $thisValue))
					{
						$tempDataStr = Labori_Utl::addtoDelinatedStr($tempDataStr, $thisKey . ': new Date("' . $thisValue . '")', ",\n");
					}
					else
					{
						$tempDataStr = Labori_Utl::addtoDelinatedStr($tempDataStr, $thisKey . ': ' . $thisValue, ",\n");
					}
				}

				if(!is_null($tempDataStr))
				{
					$content .= "chartData.push({" . $tempDataStr . "});";
				}
			}

			$content .= '
				chart.data = chartData;
				var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
				dateAxis.renderer.minGridDistance = 50;
				dateAxis.title.text="Date";
				dateAxis.title.stroke = am4core.color("#FFCF44");
			';

			foreach($yAxesData as $thisAxisKey => $thisAxis)
			{
				$content .= '
					var valueAxis_' . $thisAxisKey . ' = chart.yAxes.push(new am4charts.ValueAxis());
					valueAxis_' . $thisAxisKey . '.renderer.line.strokeOpacity = 1;
					valueAxis_' . $thisAxisKey . '.renderer.line.strokeWidth = 2;
					valueAxis_' . $thisAxisKey . '.renderer.opposite = ' . $thisAxis["opposite"] . ';
					valueAxis_' . $thisAxisKey . '.renderer.grid.template.disabled = true;
				';

				if(isset($thisAxis["title"]))
				{
					$content .= 'valueAxis_' . $thisAxisKey . '.title.text = "' . $thisAxis["title"] . '";';
					$content .= 'valueAxis_' . $thisAxisKey . '.title.stroke = am4core.color("#FFCF44");';
				}
			}

			$tempIndex = 0;

			foreach($seriesData as $thisSeries)
			{
				$content .= '
					var series = chart.series.push(new am4charts.LineSeries());
					series.dataFields.valueY = "' . $thisSeries["field"] . '";
					series.dataFields.dateX = "date";
					series.strokeOpacity = 0;
					series.yAxis = valueAxis_' . $thisSeries["axis_key"] . ';
					series.name = "' . $thisSeries["name"] . '";
					series.tooltipText = "{name}: [bold]{' . $thisSeries["field"] . '}[/]";
				';

				$tempIcon = "Triangle";

				if(isset($thisSeries["icon"]))
				{
					$tempIcon = $thisSeries["icon"];
				}

				$iconOpacity = "1.0";

				if(isset($thisSeries["icon_opacity"]))
				{
					$iconOpacity = $thisSeries["icon_opacity"];
				}

				$iconSize = "6";

				if(isset($thisSeries["icon_size"]))
				{
					$iconSize = $thisSeries["icon_size"];
				}

				$content .= '
					var bullet = series.bullets.push(new am4charts.Bullet());

					// Add a triangle to act as am arrow
					var arrow = bullet.createChild(am4core.' . $tempIcon . ');
					arrow.opacity = ' . $iconOpacity . ';
					arrow.horizontalCenter = "middle";
					arrow.verticalCenter = "middle";
					arrow.strokeWidth = 0;
					arrow.fill = chart.colors.getIndex(' . $tempIndex . ');
					arrow.direction = "top";
					arrow.width = ' . $iconSize . ';
					arrow.height = ' . $iconSize . ';
				';

				$tempIndex+=8;
			}

			$content .= '
			// Add legend
			chart.legend = new am4charts.Legend();

			// Add cursor
			chart.cursor = new am4charts.XYCursor();

			});</script>';

			return $content;
		}

		/*
			Example Usage (2 Y Axes, 2 series using one axis, 1 series using the other):
			Labori_Widget::generateDateLineChart("grant_line_1", 
			  array(array("value" => 4, "sim_val" => 3, "other_val" => 100, "date"=>"1990-01-01"),
					array("value" => 2, "sim_val" => 4, "other_val" => 40, "date"=>"1990-01-02"),
					array("value" => 6, "sim_val" => 2, "other_val" => 90, "date"=>"1990-01-03"),
					array("value" => 3, "sim_val" => 1, "other_val" => 110, "date"=>"1990-01-04"),), 
			  
			  array("value" => array("opposite" => "false"), "other_val" => array("opposite" => "true")), 
			  
			  array(array("field"=>"value", "axis_key"=>"value", "name"=>"Test Value"),
			  		array("field"=>"other_val", "axis_key"=>"other_val", "name"=>"Other Value"),
			  		array("field"=>"sim_val", "axis_key"=>"value", "name"=>"Sim Value"),
					));
		*/
		public static function generateDateLineChart($containerID, $data, $yAxesData, $seriesData)
		{

			$content = '<script>
							am4core.ready(function() {

							// Themes begin
							am4core.useTheme(am4themes_dark);
							am4core.useTheme(am4themes_animated);

							var chart = am4core.create("' . $containerID . '", am4charts.XYChart);
							chart.colors.step = 4;

							var chartData = [];
					    ';

			$dataKeys = null;

			foreach($data as $thisDataSet)
			{
				$tempDataStr = null;

				if(is_null($dataKeys))
				{
					$dataKeys = array_keys($thisDataSet);
				}
				
				foreach($thisDataSet as $thisKey => $thisValue)
				{
					if(preg_match("/([0-9][0-9][0-9][0-9])-([0-9]?[0-9])-([0-9]?[0-9])/", $thisValue))
					{
						$tempDataStr = Labori_Utl::addtoDelinatedStr($tempDataStr, $thisKey . ': new Date("' . $thisValue . '")', ",\n");
					}
					else
					{
						$tempDataStr = Labori_Utl::addtoDelinatedStr($tempDataStr, $thisKey . ': ' . $thisValue, ",\n");
					}
				}

				if(!is_null($tempDataStr))
				{
					$content .= "chartData.push({" . $tempDataStr . "});";
				}
			}

			$content .= '
				chart.data = chartData;
				var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
				dateAxis.renderer.minGridDistance = 50;
				dateAxis.title.text="Date";
				dateAxis.title.stroke = am4core.color("#FFCF44");
			';

			foreach($yAxesData as $thisAxisKey => $thisAxis)
			{
				$content .= '
					var valueAxis_' . $thisAxisKey . ' = chart.yAxes.push(new am4charts.ValueAxis());
					valueAxis_' . $thisAxisKey . '.renderer.line.strokeOpacity = 1;
					valueAxis_' . $thisAxisKey . '.renderer.line.strokeWidth = 2;
					valueAxis_' . $thisAxisKey . '.renderer.opposite = ' . $thisAxis["opposite"] . ';
					valueAxis_' . $thisAxisKey . '.renderer.grid.template.disabled = true;
				';

				if(isset($thisAxis["title"]))
				{
					$content .= 'valueAxis_' . $thisAxisKey . '.title.text = "' . $thisAxis["title"] . '";';
					$content .= 'valueAxis_' . $thisAxisKey . '.title.stroke = am4core.color("#FFCF44");';
				}
			}

			foreach($seriesData as $thisSeries)
			{
				$content .= '
					var series = chart.series.push(new am4charts.LineSeries());
					series.dataFields.valueY = "' . $thisSeries["field"] . '";
					series.dataFields.dateX = "date";
					series.strokeWidth = 2;
					series.yAxis = valueAxis_' . $thisSeries["axis_key"] . ';
					series.name = "' . $thisSeries["name"] . '";
					series.tooltipText = "{name}: [bold]{' . $thisSeries["field"] . '}[/]";
					series.tensionX = 0.8;
				';
			}

			$content .= '
			// Add legend
			chart.legend = new am4charts.Legend();

			// Add cursor
			chart.cursor = new am4charts.XYCursor();

			});</script>';

			return $content;
		}

		/*
		If value in $data are strings, must contain double quotes around them in passed array
		*/
		public static function generateSeriesPieChart($containerID, $data, $valueCatArray, $strokeColor = "#3e4752")
		{
			$content = '<script>
							am4core.ready(function() {

							// Themes begin
							am4core.useTheme(am4themes_dark);
							am4core.useTheme(am4themes_animated);

							var chart = am4core.create("' . $containerID . '", am4charts.PieChart);
							chart.colors.step = 4;
							chart.data = [';

			$dataList = null;
			foreach($data as $thisData)
			{
				$tempElement = null;
				
				foreach($thisData as $key => $value)
				{
					$tempElement = Labori_Utl::addtoDelinatedStr($tempElement, '"' . $key . '":' . $value, ",");
				}

				if(!is_null($tempElement))
				{
					$dataList = Labori_Utl::addtoDelinatedStr($dataList, "{" . $tempElement . "}", ",");
				}
			}

			if(!is_null($dataList))
			{
				$content .= $dataList;
			}

			$content .=	'];

						// Set inner radius
						chart.innerRadius = am4core.percent(20);';
			$i = 0;

			foreach($valueCatArray as $category => $valuetype)
			{
				$content .= '// Add and configure Series
						var pieSeries' . $i . ' = chart.series.push(new am4charts.PieSeries());
						pieSeries' . $i . '.dataFields.value = "' . $valuetype . '";
						pieSeries' . $i . '.dataFields.category = "' . $category . '";
						pieSeries' . $i . '.slices.template.stroke = am4core.color("' . $strokeColor .'");
						pieSeries' . $i . '.slices.template.strokeWidth = 2;
						pieSeries' . $i . '.slices.template.strokeOpacity = 1;

						// This creates initial animation
						pieSeries' . $i . '.hiddenState.properties.opacity = 1;
						pieSeries' . $i . '.hiddenState.properties.endAngle = -90;
						pieSeries' . $i . '.hiddenState.properties.startAngle = -90;
						pieSeries' . $i . '.labels.template.fontSize = 9;
						pieSeries' . $i . '.slices.template.states.getKey("hover").properties.shiftRadius = 0;
						pieSeries' . $i . '.slices.template.states.getKey("hover").properties.scale = 1.0;
						';

				if($i != count($valueCatArray) -1)
				{
					$content .= '
					pieSeries' . $i . '.labels.template.disabled = true;
					pieSeries' . $i . '.ticks.template.disabled = true;
					';
				}

				$i++;
			}
						
			$content .=	'});
					</script>';

			return $content;
		}

		/*
		If value in $data are strings, must contain double quotes around them in passed array
		*/
		public static function generatePieChart($containerID, $data, $valueKey, $categoryKey)
		{
			$content = '<script>
							am4core.ready(function() {

							// Themes begin
							am4core.useTheme(am4themes_dark);
							am4core.useTheme(am4themes_animated);

							var chart = am4core.create("' . $containerID . '", am4charts.PieChart);
							chart.colors.step = 4;
							chart.data = [';

			$dataList = null;
			foreach($data as $thisData)
			{
				$tempElement = null;
				
				foreach($thisData as $key => $value)
				{
					$tempElement = Labori_Utl::addtoDelinatedStr($tempElement, '"' . $key . '":' . $value, ",");
				}

				if(!is_null($tempElement))
				{
					$dataList = Labori_Utl::addtoDelinatedStr($dataList, "{" . $tempElement . "}", ",");
				}
			}

			if(!is_null($dataList))
			{
				$content .= $dataList;
			}

			$content .=	'];

						// Set inner radius
						chart.innerRadius = am4core.percent(50);

						// Add and configure Series
						var pieSeries = chart.series.push(new am4charts.PieSeries());
						pieSeries.dataFields.value = "' . $valueKey . '";
						pieSeries.dataFields.category = "' . $categoryKey . '";
						pieSeries.slices.template.stroke = am4core.color("#3e4752");
						pieSeries.slices.template.strokeWidth = 2;
						pieSeries.slices.template.strokeOpacity = 1;

						// This creates initial animation
						pieSeries.hiddenState.properties.opacity = 1;
						pieSeries.hiddenState.properties.endAngle = -90;
						pieSeries.hiddenState.properties.startAngle = -90;
						});
					</script>';

			return $content;
		}

		public static function makeTableSortable($tableID)
		{
			$content = '<script>
							labori_makeTableSortable("' . $tableID . '");
						</script>';
			return $content;
		}

		public static function makeTableDraggable($tableID, $disableOnStart = false)
		{
			$content = '<script>
						$(document).ready(function() {
						 	var fixHelperModified = function(e, tr) {
							    var $originals = tr.children();
							    var $helper = tr.clone();
							    $helper.children().each(function(index) {
							        $(this).width($originals.eq(index).width())
							    });
							    return $helper;
							},
						    updateIndex = function(e, ui) {
						        $(\'td.index\', ui.item.parent()).each(function (i) {
						            $(this).html(i + 1);
						        });
						    };

							$("#' . $tableID . ' tbody").sortable({
							    helper: fixHelperModified,
							    stop: updateIndex,
							    items: "tr:not([sortable=\'false\'])"
							}).disableSelection();
							';

			if($disableOnStart)
			{
				$content .= '$("#' . $tableID . ' tbody").sortable(\'disable\');
							';
			}

			$content .=	'});
						</script>';
			return $content;
		}

		public static function selectDropdownOptWithKV($selectID, $selectKey, $selectVal)
		{
			$content = '<script>
						$(document).ready(function() {
						  $("#'. $selectID . '").val("' . $selectKey .'");
						  $("#'. $selectID . '").closest(\'.labori_select\').children(\'.labori_select-styled\').html(\'' . $selectVal . '\');
						 // $($($("#'. $selectID . '").parent()[0]).children()[1]).html($(\'#'. $selectKey . ' option[value="' .  Labori_Utl::escapeSingleQuotes($selectKey) .'"]\').attr(\'selected\',\'selected\').text());
						});
						</script>';
			return $content;
		}

		public static function setupDataReminder()
		{
			$content = '<script>
						$(document).ready(function() {
						 	labori_setupDataReminder();
						});
						</script>';
			return $content;
		}

		public static function selectDropdownOpt($selectID, $selectOpt)
		{
			$content = '<script>
						$(document).ready(function() {
						  $("#'. $selectID . '").val("' . $selectOpt .'");
						  $($($("#'. $selectID . '").parent()[0]).children()[1]).html($(\'#'. $selectID . ' option[value="' .  Labori_Utl::escapeSingleQuotes($selectOpt) .'"]\').attr(\'selected\',\'selected\').text());
						});
						</script>';
			return $content;
		}

		public static function convertToDatePicker($id)
		{
			$content = '<script>
						$(document).ready(function() {
						  flatpickr("#'. $id . '", {});
						});
						</script>';
			return $content;
		}

		public static function convertMultiToDatePicker($class)
		{
			$content = '<script>
						$(document).ready(function() {
						  flatpickr(".'. $class . '", {});
						});
						</script>';
			return $content;
		}


		public static function genUploadFunctionWithoutJob($fileUploadId, $fileUploadButtonTextID, $loadingHTML,
														   $groupID, $optArgs, $type, $class, $parentRoute, $action, 
												 		   $callBackFunction, $callBackArgs)
		{
			$args = array();
			$args["fileInputId"] = $fileUploadId;			    	   
			$args["buttonTextId"] = $fileUploadButtonTextID;
			$args["loadingHTML"] = $loadingHTML;
			$args["groupID"] = $groupID;
			$args["optArgs"] = $optArgs;
			$args["type"] = $type;
			$args["class"] = $class;
			$args["parent_route"] = $parentRoute;
			$args["action"] = $action;
			$args["callBackFunction"] = $callBackFunction;
			$args["callBackArgs"] = $callBackArgs;

			$tempFun =  'labori_setupUploadWithoutJob(' . self::convertToJSArgs($args) . ');';

			$content = '<script>
						$(document).ready(function() {
						  var el = document.getElementById("' . $fileUploadId . '");
							el.onchange = function(){
							  	' . $tempFun .'
							};
						});
						</script>';
			return $content;
		}

		public static function genUploadFunctionWithJob($jobVisibilityGroup, $jobLogHolder, $jobStatusHolder, $jobProgressHolder, $fileUploadId,
														$groupID, $optArgs, $type, $class, $parentRoute, $action, 
												 		$callBackFunction, $callBackArgs)
		{
			$args = array();

			$args["jobVisibilityGroup"] = $jobVisibilityGroup;
			$args["jobLogHolder"] = $jobLogHolder;
			$args["jobStatusHolder"] = $jobStatusHolder;
			$args["jobProgressHolder"] = $jobProgressHolder;			    	   
			$args["fileInputId"] = $fileUploadId;
			$args["groupID"] = $groupID;
			$args["optArgs"] = $optArgs;
			$args["type"] = $type;
			$args["class"] = $class;
			$args["parent_route"] = $parentRoute;
			$args["action"] = $action;
			$args["callBackFunction"] = $callBackFunction;
			$args["callBackArgs"] = $callBackArgs;

			$tempFun =  'labori_setupUploadWithJob(' . self::convertToJSArgs($args) . ');';

			$content = '<script>
						$(document).ready(function() {
						  var el = document.getElementById("' . $fileUploadId . '");
							el.onchange = function(){
							  	' . $tempFun .'
							};
						});
						</script>';
			return $content;
		}

		public static function genLoaderFunction($optArgs, 
												 $type, $class, $parentRoute, $action, 
												 $callBackFunction, $callBackArgs, 
												 $dontEscapeArgsArray = array())
		{
			$args = array();

			$args["optArgs"] = $optArgs;
			$args["type"] = $type;
			$args["class"] = $class;
			$args["parent_route"] = $parentRoute;
			$args["action"] = $action;
			$args["callBackFunction"] = $callBackFunction;
			$args["callBackArgs"] = $callBackArgs;

			return '<script>
				$(document).ready(function() {
					labori_setupAutoServiceCall(' . self::convertToJSArgs($args, $dontEscapeArgsArray) . ');
				});
			</script>';
		}

		public static function genButtonFunction($buttonID, $processingText, $completeText,
												 $groupID, $optArgs, 
												 $type, $class, $parentRoute, $action, 
												 $callBackFunction, $callBackArgs, 
												 $isForFileDownload=false,
												 $dontEscapeArgsArray = array(),
												 $priorCheckFunction = "",
												 $priorCheckCallback = "")
		{
			$args = array();

			$args["buttonID"] = $buttonID;
			$args["processingText"] = $processingText;
			$args["completeText"] = $completeText;
			$args["groupID"] = $groupID;
			$args["optArgs"] = $optArgs;
			$args["type"] = $type;
			$args["class"] = $class;
			$args["parent_route"] = $parentRoute;
			$args["action"] = $action;
			$args["callBackFunction"] = $callBackFunction;
			$args["callBackArgs"] = $callBackArgs;
			$args["priorCheckFunction"] = $priorCheckFunction;
			$args["priorCheckCallback"] = $priorCheckCallback;

			if(!$isForFileDownload)
			{
				return 'labori_setupButton(' . self::convertToJSArgs($args, $dontEscapeArgsArray) . ')';
			}
			else
			{
				return 'labori_setupButtonForFileDownload(' . self::convertToJSArgs($args, $dontEscapeArgsArray) . ')';
			}
		}

		public static function setupForm($formID, $groupID, $optArgs, 
										 $type, $class, $parentRoute, $action, 
										 $callBackFunction, $callBackArgs)
		{
			$args = array();

			$args["formID"] = $formID;
			$args["groupID"] = $groupID;
			$args["optArgs"] = $optArgs;
			$args["type"] = $type;
			$args["class"] = $class;
			$args["parent_route"] = $parentRoute;
			$args["action"] = $action;
			$args["callBackFunction"] = $callBackFunction;
			$args["callBackArgs"] = $callBackArgs;

			return '<script>
				$(document).ready(function() {
				  labori_setupForm(' . self::convertToJSArgs($args) . ');
				});
			</script>';
		}

		public static function convertToJSArgs($args, $noEscape = array())
		{
			$retVal = null;
			$thisArg = null;

			foreach($args as $key => $value)
			{
				$thisArg = null;

				if(is_null($value))
				{
					$thisArg = 'null';
				}	
				else if(is_array($value))
				{
					if(Labori_Utl::isAssociativeArray($value))
					{
						$thisArg = "{";
						$innerVal = null; 

						foreach($value as $innerKey => $innerValue)
						{
							$innerVal = Labori_Utl::addtoDelinatedStr($innerVal, $innerKey . ":'" . Labori_Utl::escapeSingleQuotes($innerValue) . "'", ", ");
						}

						$thisArg .= $innerVal . "}";
					}
					else
					{
						$thisArg = "[";
						$innerVal = null; 

						foreach($value as $innerValue)
						{
							$innerVal = Labori_Utl::addtoDelinatedStr($innerVal, "'" . Labori_Utl::escapeSingleQuotes($innerValue) . "'", ", ");
						}

						$thisArg .= $innerVal . "]";
					}
				}
				else if(Labori_Utl::streql($value, "this"))
				{
					$thisArg = 'this';
				}
				else
				{
					if(array_key_exists($key, $noEscape))
					{
						$thisArg = $value;
					}
					else
					{
						$thisArg = "'" . Labori_Utl::escapeSingleQuotes($value) . "'";
					}
				}

				$retVal = Labori_Utl::addtoDelinatedStr($retVal, $thisArg, ", ");
			}

			return $retVal;
		}
	}
?>