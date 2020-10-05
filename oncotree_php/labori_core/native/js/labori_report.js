function labori_report_deleteReportTab(reportName, currentTabID, currentItemID)
{
	$('#' + currentTabID).remove();
	$('#' + currentItemID).remove();
	labori_report_handleReportTabs(reportName + "_hide_group", 
								   reportName + "_options_tab", 
								   reportName + "_tab", 
								   reportName + "_options_container");
}

function labori_report_handleReportTabs(hideGroupClass, tabID, tabClass, showItemID)
{
	$('.' + hideGroupClass).hide();
	$('#' + showItemID).show();
	$('.' + tabClass).removeClass('selected_tab');
	$('#' + tabID).addClass('selected_tab');
}

function labori_report_callback_addReportTab(responseObj, args)
{
	var reportName = args;
	var selectedQueryListItem = $('li[rel="' + $('#' + reportName +'_query_selector').val() + '"]');
	var queryName = "New Report";
	
	if(selectedQueryListItem.length > 0)
	{
		var startVal = 1;
		var existing = $('#' + reportName + '_tab_group .labori_content_block_tab span');
		var foundName = false;
		var namePrefix = $(selectedQueryListItem[0]).html();
		var nameToTry = "";

		while(!foundName)
		{
			nameToTry = namePrefix + " [" + startVal + "]";
			foundName = true;

			for(var i = 0; i < existing.length; i++)
			{
				if($(existing[i]).html() == nameToTry)
				{
					foundName = false;
					break;
				}
			}

			startVal++;
			
		}

		queryName = nameToTry;
	}


	var tabID = reportName + '_' + labori_generateUniqueID();
	$('#' + reportName + '_tab_group').append('<div id="' + tabID + '_tab" class="labori_content_block_tab ' + reportName + '_tab"' + 
											  ' onclick="labori_report_handleReportTabs(\'' + reportName + '_hide_group\', \'' + tabID + '_tab\', \'' + reportName + '_tab\', \'' + tabID + '_container\');">' +
											   '<span>' + queryName + '</span><div class="labori_button_round tiny_button" onclick="event.stopPropagation(); labori_report_deleteReportTab(\'' + reportName + '\', \'' + tabID + '_tab\', \'' + tabID + '_container\')" style="margin-left:10px; float:right; display:inline-block;">' +
															'<i class="fa fa-close" aria-hidden="true"></i></div>' + 
											   '</div>')

	$('#' + reportName + "_html_container").append('<div id="' + tabID + '_container" style="display:none;" class="'+ reportName + '_hide_group">' + responseObj + '</div>');
}

/**********************************************************/
/*QUERY_FUNCTIONS           		 	  				  */
/**********************************************************/
function labori_report_setInnerHolderOption(conditionGroupID, type, options)
{
	if(type == 'nothing')
	{
		$('#' + conditionGroupID + "-holder-inner").html('');
	}
	else if(type == "multi_input_text")
	{
		var tempID = labori_generateUniqueID();
		$('#' + conditionGroupID  + "-holder-inner").html('<div class="group query_condition_inner_element">' + 
									'<div class="labori_labeled_field_label">Values</div>' + 
									'<div class="labori_labeled_field_field"><div class="group">' + 
									'<input type="text" style="width: 85%; float:left;" id="' + tempID + '"' + 
									'" labori__meta_id="value' + 
									'" class="labori__field labori_text_input" labori__field_type="multi_input" labori__group_id="' + conditionGroupID + 
									'"/>' + 
									'<div class="labori_button_round small_button" style="float:right; display:inline-block;" onclick="labori_multiinput_insertMultiInputChoice(\''+ tempID + '\');">' + 
									'<i class="fa fa-plus" aria-hidden="true"></i></div>' + 
									'</div>' +
									'<div id="' + tempID + '-multiinput-holder" class="labori_multiinput_holder" style=""></div>' + 
									'</div>' +
									'</div>');
	}
	else if(type == "multi_input_dropdown")
	{
		var tempOptions = "";
		var tempID = labori_generateUniqueID();

		for(var j = 0; j < options.length; j++)
		{
			tempOptions += "<option onclick='labori_multidropdown_insertMultiDropdownChoice(\"" + tempID + "\",this);' value='" + options[j].id + "'>" + options[j].value + "</option>";
		}

		$('#' + conditionGroupID  + "-holder-inner").html('<div class="group query_condition_inner_element">' + 
									'<div class="labori_labeled_field_label">Values</div>' + 
									'<div class="labori_labeled_field_field"><div class="group">' + 
									'<select type="text" id="' + tempID + '"' + 
									'" labori__meta_id="value' +
									'" class="labori__field labori_text_input" labori_multi_select="true" labori__field_type="multi_input" labori__group_id="' + conditionGroupID + 
									'">' + tempOptions +
									'</select>' + 
									'<div id="' + tempID + '-multiinput-holder" class="labori_multiinput_holder" style=""></div>' + 
									'</div>' +
									'</div>');

		$(document).ready(function() 
		{
			labori_convertDropdowns("#" + tempID);
		});
	}
	else if(type == "number")
	{
		var tempID = labori_generateUniqueID();
		$('#' + conditionGroupID  + "-holder-inner").html('<div class="group query_condition_inner_element">' + 
									'<div class="labori_labeled_field_label">Value</div>' + 
									'<div class="labori_labeled_field_field">' + 
									'<input type="text" style="width:100%" id="' + tempID + '"' + 
									'" labori__meta_id="value' + 
									'" class="labori__field labori_text_input" labori__field_type="free_text" labori__group_id="' + conditionGroupID + 
									'"/>' + 
									'</div>');
	}
	else if(type == "date")
	{
		var tempID = labori_generateUniqueID();
		$('#' + conditionGroupID  + "-holder-inner").html('<div class="group query_condition_inner_element">' + 
									'<div class="labori_labeled_field_label">Value</div>' + 
									'<div class="labori_labeled_field_field">' + 
									'<input type="text" style="width:100%" id="' + tempID + '"' + 
									'" labori__meta_id="value' + 
									'" class="labori__field labori_text_input labori_date_field" labori__field_type="free_text" labori__group_id="' + conditionGroupID + 
									'"/>' + 
									'</div>');

		 flatpickr('#' + tempID, {});
	}
}

function labori_report_pullDownConditionOptions(conditionGroupID, conditionID, reportIDDropdown, reportHelperClass, reportHelperDir)
{
	var requestArgs = {};
	requestArgs["report_class_name"] = $('#' + reportIDDropdown).val();
	requestArgs["report_helper_class"] = reportHelperClass;
	requestArgs["report_helper_directory"] = reportHelperDir;
	requestArgs["condition_id"] = conditionID;

	var requestPayload = {
				"type":'serv', 
				"class":'Service_Queries', 
				"parent_route":null, 
				"action":'request_getConditionsForReport', 
				"request_key":"TODO", 
				"args": JSON.stringify(requestArgs),
				};

	$.ajax(
	{
		type: "POST",
		data: requestPayload,
		url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
		cache: false,

		success: function(data)
		{
			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					console.log(data.response);
					$('#' + conditionGroupID + '-holder').html('');


					var historicID = labori_generateUniqueID();
					$tempConditionHistoric = '';

					if(data.response.condition_historic)
					{
						
						var tempOptList = '<option value="Yes">Yes</option>' + 
										  '<option value="No">No</option>';

						$tempConditionHistoric = '<div class="group query_condition_inner_element">' + 
												 '<div class="labori_labeled_field_label">Use History</div>' + 
												 '<div class="labori_labeled_field_field">' + 
												 '<select type="text" style="width:99%;" id="' + historicID + '"' + 
												 '" labori__meta_id="condition_historic' +
												 '" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionGroupID + 
												 '">' + tempOptList + '</select>' +
												 '</div>' +
												 '</div>';
					}

					if(data.response.condition_type == 'text')
					{
						var tempOptList = '<option value="REMOVE___ME">Please Select One</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="not_empty">Text Is Not Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="empty">Text Is Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_text' + '\', null);" value="include">Text Equals</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_text' + '\', null);" value="exclude">Text Does Not Equal</option>' +
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_text' + '\', null);" value="include_regex">Text Matches Pattern</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_text' + '\', null);" value="exclude_regex">Text Does Not Match Pattern</option>';

						var includeID = labori_generateUniqueID();

						var stuffToAppend = $tempConditionHistoric +
											'<div class="group query_condition_inner_element">' + 
											'<div class="labori_labeled_field_label">Include If</div>' + 
											'<div class="labori_labeled_field_field">' + 
											'<select type="text" style="width:99%;" id="' + includeID + '"' + 
											'" labori__meta_id="include_if' +
											'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionGroupID + 
											'">' + tempOptList + '</select>' +
											'</div>' +
											'</div>' +
											'<div id="' + conditionGroupID + '-holder-inner"></div>';	

						$('#' + conditionGroupID + '-holder').append(stuffToAppend);

						$(document).ready(function() 
						{
							labori_convertDropdowns("#" + historicID);
							labori_convertDropdowns("#" + includeID);
						});
					}
					else if(data.response.condition_type == 'dropdown')
					{
						var tempValuesList = '[';

						for(var j = 0; j < data.response.condition_values.length; j++)
						{
							if(tempValuesList == '[')
							{
								tempValuesList += '{id:\'' + data.response.condition_values[j].id + "\', value:\'" + data.response.condition_values[j].text + "\'}";
							}
							else
							{
								tempValuesList += ',{id:\'' + data.response.condition_values[j].id + "\', value:\'" + data.response.condition_values[j].text + "\'}";
							}
						}


						tempValuesList += ']';

						var tempOptList = '<option value="REMOVE___ME">Please Select One</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="not_empty">Value Is Not Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="empty">Value Is Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_dropdown' + '\', ' + tempValuesList + ');" value="include">Value Equals</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'multi_input_dropdown' + '\', ' + tempValuesList + ');" value="exclude">Value Does Not Equal</option>';

						var includeID = labori_generateUniqueID();

						var stuffToAppend = $tempConditionHistoric +
											'<div class="group query_condition_inner_element">' + 
											'<div class="labori_labeled_field_label">Include If</div>' + 
											'<div class="labori_labeled_field_field">' + 
											'<select type="text" style="width:99%;" id="' + includeID + '"' + 
											'" labori__meta_id="include_if' + 
											'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionGroupID + 
											'">' + tempOptList + '</select>' +
											'</div>' +
											'</div>' +
											'<div id="' + conditionGroupID + '-holder-inner"></div>';	

						$('#' + conditionGroupID + '-holder').append(stuffToAppend);

						$(document).ready(function() 
						{
							labori_convertDropdowns("#" + historicID);
							labori_convertDropdowns("#" + includeID);
						});
					}
					else if(data.response.condition_type == 'number')
					{
						var tempOptList = '<option value="REMOVE___ME">Please Select One</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="not_empty">Number Is Not Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="empty">Number Is Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include">Number Equals</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="exclude">Number Does Not Equal</option>' +
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_greater">Number is Greater Than</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_lesser">Number is Less Than</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_greater_equal">Number is Greater Than/Equal</option>' +
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_lesser_equal">Number is Less Than/Equal</option>';

						var includeID = labori_generateUniqueID();

						var stuffToAppend = $tempConditionHistoric +
											'<div class="group query_condition_inner_element">' + 
											'<div class="labori_labeled_field_label">Include If</div>' + 
											'<div class="labori_labeled_field_field">' + 
											'<select type="text" style="width:99%;" id="' + includeID + '"' + 
											'" labori__meta_id="include_if' +
											'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionGroupID + 
											'">' + tempOptList + '</select>' +
											'</div>' +
											'</div>' +
											'<div id="' + conditionGroupID + '-holder-inner"></div>';	

						$('#' + conditionGroupID + '-holder').append(stuffToAppend);

						$(document).ready(function() 
						{
							labori_convertDropdowns("#" + historicID);
							labori_convertDropdowns("#" + includeID);
						});
					}
					else if(data.response.condition_type == 'date')
					{
						var tempOptList = '<option value="REMOVE___ME">Please Select One</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="not_empty">Date Is Not Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'nothing' + '\', null);" value="empty">Date Is Empty</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="include">Date Equals</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="exclude">Date Does Not Equal</option>' +
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="include_greater">Date is Greater Than</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="include_lesser">Date is Less Than</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="include_greater_equal">Date is Greater Than/Equal</option>' +
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'date' + '\', null);" value="include_lesser_equal">Date is Less Than/Equal</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_years_prior">Date is X Years Prior to Today</option>' + 
										  '<option onclick="labori_report_setInnerHolderOption(\'' + conditionGroupID + '\', \'' + 'number' + '\', null);" value="include_years_forward">Date is X Years From Today</option>';

						var includeID = labori_generateUniqueID();

						var stuffToAppend = $tempConditionHistoric +
											'<div class="group query_condition_inner_element">' + 
											'<div class="labori_labeled_field_label">Include If</div>' + 
											'<div class="labori_labeled_field_field">' + 
											'<select type="text" style="width:99%;" id="' + includeID + '"' + 
											'" labori__meta_id="include_if' + 
											'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionGroupID + 
											'">' + tempOptList + '</select>' +
											'</div>' +
											'</div>' +
											'<div id="' + conditionGroupID + '-holder-inner"></div>';	

						$('#' + conditionGroupID + '-holder').append(stuffToAppend);

						$(document).ready(function() 
						{
							labori_convertDropdowns("#" + historicID);
							labori_convertDropdowns("#" + includeID);
						});
					}
				}
			}
		}
	});
}

function labori_report_deleteCondition(groupID, conditionId)
{
	$('#' + conditionId).remove(); 
	$('#' + conditionId + '-divider').remove();

	var queryGroups = $('#' + groupID + ' .padding_div .condition_container').children();
	var lastClass = null;

	for(var i = 0; i < queryGroups.length; i++)
	{
		if((lastClass == null || !lastClass.includes('query_condition')) && 
		   $(queryGroups[i]).attr('class').includes('condition_divider'))
		{
			$(queryGroups[i]).remove();
		}

		lastClass = $(queryGroups[i]).attr('class');
	}
}

function labori_report_addCondition(groupID, reportIDDropdown, reportHelperClass, reportHelperDir)
{
	var conditionDropdownId = 'new_group_condition_dropdown_' + labori_generateUniqueID();
	var conditionId = 'new_condition_' + labori_generateUniqueID();
	var requestArgs = {};

	requestArgs["report_class_name"] = $('#' + reportIDDropdown).val();
	requestArgs["report_helper_class"] = reportHelperClass;
	requestArgs["report_helper_directory"] = reportHelperDir;

	var requestPayload = {
				"type":'serv', 
				"class":'Service_Queries', 
				"parent_route":null, 
				"action":'request_getConditionsForReport', 
				"request_key":"TODO", 
				"args": JSON.stringify(requestArgs),
				};

	$.ajax(
	{
		type: "POST",
		data: requestPayload,
		url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
		cache: false,

		success: function(data)
		{
			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					
					var optionList = '<option value="REMOVE___ME">Please Select One</option>';

					for(var i = 0; i < data.response.length; i++)
					{
						optionList += '<option onclick="labori_report_pullDownConditionOptions(\'' + conditionId + '\', \'' + data.response[i].condition_id + '\', \'' + reportIDDropdown + '\', \'' + reportHelperClass + '\', \'' + reportHelperDir + '\')" value="' + 
									   data.response[i].condition_id + '">[' + data.response[i].table_name_human_readable + "] " + data.response[i].condition_name + '</option>';
					}

					if($('#' + groupID + ' .padding_div .condition_container .query_condition').length >= 1)
					{
						$('#' + groupID + ' .padding_div .condition_container').append('<div class="condition_divider" id="'+ conditionId +'-divider"><div class="query_divider_strikethrough"></div>AND<div class="query_divider_strikethrough"></div></div>');
					}


					$('#' + groupID + ' .padding_div .condition_container').append('<div class="query_condition labori__field group" id="' + conditionId + '"  labori__field_type="json_group" labori__group_id="' + groupID + '" labori__meta_id="condition_group__' + labori_generateUniqueID() + '">' +
															
															'<div class="group">' + 
															'<span style="float:left; color: #717A85;"><i class="fa fa-cogs" aria-hidden="true"></i> Condition Options</span>' + 
															'<div style="" class="labori_button_round tiny_button gray_button" onclick="labori_report_deleteCondition(\'' + groupID + '\', \'' + conditionId + '\')">' +
															'<i class="fa fa-close" aria-hidden="true"></i>' + 
															'</div></div>' +

															'<div class="group query_condition_inner_element">' + 
															'<div class="labori_labeled_field_label">Field</div>' + 
															'<div class="labori_labeled_field_field">' + 
															'<select type="text" style="width:99%;" id="' + conditionDropdownId + '"' + 
															'" labori__meta_id="condition_id' + 
															'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionId + 
															'">' + optionList + '</select>' +
															'</div>' +
															'</div>' +	

															'<div id="' + conditionId + '-holder"></div>' + 								 

															'</div>');

					$(document).ready(function() 
					{
						labori_convertDropdowns("#" + conditionDropdownId);
					});
				}
			}
		}
	});
}

function labori_report_deleteQueryGroup(groupHolderId, groupID)
{
	$('#' + groupID).remove(); 
	$('#' + groupID + '-divider').remove();

	var queryGroups = $('#' + groupHolderId).children();
	var lastClass = null;

	for(var i = 0; i < queryGroups.length; i++)
	{
		if((lastClass == null || !lastClass.includes('labori_query_group')) && 
		   $(queryGroups[i]).attr('class').includes('query_divider'))
		{
			$(queryGroups[i]).remove();
		}

		lastClass = $(queryGroups[i]).attr('class');
	}
}

function labori_report_addQueryGroup(groupHolderId, reportIDDropdown, reportHelperClass, reportHelperDir)
{
	var newGroupId = 'new_group_' + labori_generateUniqueID();
	$('#' + groupHolderId).show();
	
	if($('#' + groupHolderId + ' .labori_query_group').length >= 1)
	{
		$('#' + groupHolderId).append('<div class="query_divider" id="'+ newGroupId +'-divider">OR</div>');
	}
	
	$('#' + groupHolderId).append('<div id="'+ newGroupId +'" labori__meta_id="'+ newGroupId +'" labori__field_type="json_group" labori__group_id="' + groupHolderId + '" class="labori__field labori_query_group">' + 
								'<div class="padding_div"><div class="group">' + 
								'<div class="labori_group_title_light">Query Group</div>' +
								'<div class="condition_container group"></div>' + 
								'<div style="width: 49%; float: left; display: inline-block;" class="labori_query_button_long" onclick="labori_report_addCondition(\'' + newGroupId + '\', \'' + reportIDDropdown + '\', \'' + reportHelperClass + '\', \'' + reportHelperDir + '\')">' +
								'<i class="fa fa-plus-circle" aria-hidden="true"></i> Add Condition' + 
								'</div>' +

								'<div style="width: 49%; float: right; display: inline-block;" class="labori_query_button_long red_button" onclick="labori_report_deleteQueryGroup(\'' + groupHolderId + '\', \'' + newGroupId + '\')">' +
								'<i class="fa fa-close" aria-hidden="true"></i> Delete Group' + 
								'</div>' +  
								'</div>' + 
								'</div>' + 

								'</div>');
}

function labori_report_deleteQueryInput(groupHolderId, conditionId)
{
	$('#' + conditionId).remove(); 

	var queryGroups = $('#' + groupHolderId + ' .padding_div').children();
	
	if(queryGroups.length < 2)
	{
		$('#' + groupHolderId).hide();
	}
}

function labori_report_addQueryInput(groupHolderId, reportIDDropdown, reportHelperClass, reportHelperDir)
{
	$('#' + groupHolderId).show();

	var conditionDropdownId = 'new_group_condition_dropdown_' + labori_generateUniqueID();
	var conditionId = 'new_condition_' + labori_generateUniqueID();
	var requestArgs = {};

	requestArgs["report_class_name"] = $('#' + reportIDDropdown).val();
	requestArgs["report_helper_class"] = reportHelperClass;
	requestArgs["report_helper_directory"] = reportHelperDir;

	var requestPayload = {
				"type":'serv', 
				"class":'Service_Queries', 
				"parent_route":null, 
				"action":'request_getConditionsForReport', 
				"request_key":"TODO", 
				"args": JSON.stringify(requestArgs),
				};

	$.ajax(
	{
		type: "POST",
		data: requestPayload,
		url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
		cache: false,

		success: function(data)
		{
			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					var tempYNList = '<option value="Yes">Yes</option>' + 
									  '<option value="No">No</option>';
					var historicID = labori_generateUniqueID();

					var optionList = '<option value="REMOVE___ME">Please Select One</option>';
					optionList += '<option value="GLOBAL___date_range">Start/End Date Range</option>';
					optionList += '<option value="GLOBAL___member_selector">Member Selector</option>';

					for(var i = 0; i < data.response.length; i++)
					{
						if(data.response[i].condition_historic)
						{
							optionList += '<option onclick="$(\'#' + historicID + '_vg\').show(); labori_selectDropdownOpt(\'' + historicID +'\', \'Yes\');" value="' + data.response[i].condition_id + '">[' + data.response[i].table_name_human_readable + "] " + data.response[i].condition_name + '</option>';
						}
						else
						{
							optionList += '<option onclick="$(\'#' + historicID + '_vg\').hide(); labori_selectDropdownOpt(\'' + historicID +'\', \'No\');" value="' + data.response[i].condition_id + '">[' + data.response[i].table_name_human_readable + "] " + data.response[i].condition_name + '</option>';
						}
					}

					$('#' + groupHolderId + ' .padding_div').append('<div class="query_condition labori__field group" id="' + conditionId + '"  labori__field_type="json_group" labori__group_id="' + groupHolderId + '" labori__meta_id="condition_group__' + labori_generateUniqueID() + '">' +
															
															'<div class="group">' + 
															'<span style="float:left; color: #717A85;"><i class="fa fa-cog" aria-hidden="true"></i> Query Field Options</span>' + 
															'<div style="" class="labori_button_round tiny_button gray_button" onclick="labori_report_deleteQueryInput(\'' + groupHolderId + '\', \'' + conditionId + '\')">' +
															'<i class="fa fa-close" aria-hidden="true"></i>' + 
															'</div></div>' +

															'<div class="group query_condition_inner_element">' + 
															'<div class="labori_labeled_field_label">Field</div>' + 
															'<div class="labori_labeled_field_field">' + 
															'<select type="text" style="width:99%;" id="' + conditionDropdownId + '"' + 
															'" labori__meta_id="condition_id' + 
															'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionId + 
															'">' + optionList + '</select>' +
															'</div>' +
															'</div>' +	

															'<div style="display:none;" id="' + historicID + '_vg" class="group query_condition_inner_element">' + 
															'<div class="labori_labeled_field_label">Use History</div>' + 
															'<div class="labori_labeled_field_field">' + 
															'<select type="text" style="width:99%;" id="' + historicID + '"' + 
															'" labori__meta_id="condition_historic' + 
															'" class="labori__field labori_select-hidden" labori__field_type="dropdown" labori__group_id="' + conditionId + 
															'">' + tempYNList + '</select>' +
															'</div>' +
															'</div>' +									 
															'</div>');

					$(document).ready(function() 
					{
						labori_convertDropdowns("#" + conditionDropdownId);
						labori_convertDropdowns("#" + historicID);
					});
				}
			}
		}
	});
}