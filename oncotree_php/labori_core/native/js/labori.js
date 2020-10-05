/**********************************************************/
/*GLOBAL CACHE                        		 	  		  */
/**********************************************************/
var labori_cache_holder = {};
var labori_cancelDataCheck = false;

function labori_addValueToCache(cache_key, value)
{
	labori_cache_holder[cache_key] = value;
}

function labori_deleteValueFromCache(cache_key)
{
	if(labori_cache_holder.cache_key !== undefined)
	{
		delete labori_cache_holder.cache_key;
	}
}

function labori_getValueFromCache(cache_key, value_key)
{
	return labori_cache_holder.cache_key;
}

/**********************************************************/
/*CONSTANTS                        		 	  			  */
/**********************************************************/
var labori_js_color = "#FFCF44";

function labori_getInstanceSetting(constName)
{
	var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", '/' + labori_getRootDir() + '/application/config/Instance_Settings.php?CONST_NAME=' + constName, false ); 
    xmlHttp.send( null );
    return xmlHttp.responseText;
}

/**********************************************************/
/*IE COMPATIABILITY                         		 	  */
/**********************************************************/
if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
      if (typeof start !== 'number') {
        start = 0;
      }

      if (start + search.length > this.length) {
        return false;
      } else {
        return this.indexOf(search, start) !== -1;
      }
    };
  }

/**********************************************************/
/*DATE UTILITIES                            		 	  */
/**********************************************************/
function labori_insertEndDateOneYearFromNow(startDateInputID, endDateInputID)
{
	if($('#' + endDateInputID).val().trim().length == 0)
	{
		var dateValue = $('#' + startDateInputID).val();
		dateValue = moment(dateValue.trim(), "Y-M-D");
		dateValue.subtract(1, 'days');
		dateValue.add(1, 'years');
		$('#' + endDateInputID).val(dateValue.format('Y-MM-DD'));
		document.querySelector('#' + endDateInputID)._flatpickr.setDate(dateValue.format('Y-MM-DD'), true, 'Y-m-d');
	}
}

/**********************************************************/
/*STRING UTILITIES                            		 	  */
/**********************************************************/
function labori_genRegexFromString(regexStr)
{
	var flags = regexStr.replace(/.*\/([gimy]*)$/, '$1');
	var pattern = regexStr.replace(new RegExp('^/(.*?)/'+flags+'$'), '$1');
	var regex = new RegExp(pattern, flags);
	return regex;
}

function labori_replaceAll(search, replacement, target) 
{
    return target.replace(new RegExp(search, 'g'), replacement);
}

function labori_isValidJSON(str) 
{
    try 
    {
        JSON.parse(str);
    } 
    catch (e) 
    {
        return false;
    }

    return true;
}

function labori_generateUniqueID()
{
	return 'i' + Math.random().toString(36).substring(2, 15) + "_" + (Math.floor(Date.now() / 1000))
}

function labori_convertToCurrency(input)
{
	var currentVal = $(input).val();
	currentVal = labori_replaceAll('\\$', '', currentVal);
	currentVal = labori_replaceAll('\\,', '', currentVal);
	//currentVal = currentVal.replace(/^0+/, '');
	
	var parts = currentVal.toString().split(".");
  	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	$(input).val('$' + parts.join("."));
}

/**********************************************************/
/*FILE UTILITIES                               		 	  */
/**********************************************************/
function labori_getRootDir()
{
	var temp = document.location.pathname.split("/");

	if(temp.length >= 2)
	{
		return temp[1];
	}
	else
	{
		return null;
	}
}

/**********************************************************/
/*FUNCTION UTILITIES                               	      */
/**********************************************************/
function labori_attachClickOnEnter(fieldID, buttonID)
{
	$("#" + fieldID).keyup(function(event) {
	    if (event.keyCode === 13) {
	        $("#" + buttonID).click();
	    }
	});
}


function labori_copyTextToClipboard(elementID, copyIndicatorID)
{
	var copyText = document.getElementById(elementID);

	if(copyText !== undefined)
	{
		copyText.focus();
		copyText.select();
		document.execCommand("copy");
		$('#' + copyIndicatorID).html('<i class="fa fa-check" aria-hidden="true"></i>');

		setTimeout(function() {
			$('#' + copyIndicatorID).html('<i class="fa fa-clipboard" aria-hidden="true"></i>');
		}, 2500);
	}
}

function labori_getFunctionFromString(functionName)
{
	var scope = window;
    var scopeSplit = functionName.split('.');
    for (i = 0; i < scopeSplit.length - 1; i++)
    {
        scope = scope[scopeSplit[i]];

        if (scope == undefined)
        {
        	return;
        }
    }

    return scope[scopeSplit[scopeSplit.length - 1]];
}

/**********************************************************/
/*SEARCH UTILITIES                                        */
/**********************************************************/
function labori_handleBasicSearch(url)
{
	var searchType = $('#search_type').val();
	var searchVal = labori_extractMetaFieldValue($('#search_field'));
	var countPer = $('#count_per').val();

	window.location = encodeURI(url +
								'?page=0' +  
								'&count_per=' + countPer +
								'&search_on=' + searchType +
								'&search_val=' + searchVal);
}

function labori_handleBasicSearchWithURLParams(url)
{
	var searchType = $('#search_type').val();
	var searchVal = labori_extractMetaFieldValue($('#search_field'));
	var countPer = $('#count_per').val();

	window.location = encodeURI(url +
								'&page=0' +  
								'&count_per=' + countPer +
								'&search_on=' + searchType +
								'&search_val=' + searchVal);
}

function labori_handleBasicSearchEscaped(url)
{
	var searchType = $('#search_type').val();
	var searchVal = labori_extractMetaFieldValue($('#search_field')).split("`").join("");
	searchVal = searchVal.split("\"").join("`");
	searchVal = searchVal.split("'").join("~~");
	var countPer = $('#count_per').val();

	window.location = encodeURI(url +
								'?page=0' +  
								'&count_per=' + countPer +
								'&search_on=' + searchType +
								'&search_val=' + searchVal);
}

function labori_handleBasicSearchEscapedWithURLParams(url)
{
	var searchType = $('#search_type').val();
	var searchVal = labori_extractMetaFieldValue($('#search_field')).split("`").join("");
	searchVal = searchVal.split("\"").join("`");
	searchVal = searchVal.split("'").join("~~");
	var countPer = $('#count_per').val();

	window.location = encodeURI(url +
								'&page=0' +  
								'&count_per=' + countPer +
								'&search_on=' + searchType +
								'&search_val=' + searchVal);
}

/**********************************************************/
/*FORM UTILITIES                                       	  */
/**********************************************************/
function labori_autoRejectCallback()
{
	$('.submission_vg').hide();
	$('.rejection_vg').show();

	return {"auto_reject" : true};
}

function labori_isAutoReject_safe()
{
	if(typeof labori__form_auto_reject_function === "function")
	{
		try
		{
			var retVal = labori__form_auto_reject_function();

			if(retVal !== true && retVal !== false)
			{
				return false;
			}
			else
			{
				return retVal;
			}
		}
		catch(err)
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function labori_disableFormField(questionID, toggleButton, toggleButtonClass)
{
	console.log(questionID);
	var innerObj = $('#' + questionID + "__holder").find('.labori__field');

	if(innerObj.length > 1)
	{
		innerObj = innerObj[0];
	}

	$(innerObj).addClass('labori__override_requirements');
	$('#' + questionID + "__holder").hide();
	labori_toggleButtonLockWithID(undefined, toggleButton, toggleButtonClass);
}

function labori_enableFormField(questionID, toggleButton, toggleButtonClass)
{
	var innerObj = $('#' + questionID + "__holder").find('.labori__field');

	if(innerObj.length > 1)
	{
		innerObj = innerObj[0];
	}

	$(innerObj).removeClass('labori__override_requirements');
	$('#' + questionID + "__holder").show();
	labori_toggleButtonLockWithID(undefined, toggleButton, toggleButtonClass);
}

function labori_getFormFields(e, fieldType, metaGroupID, targetClass, parentRoute, prevRow, visualGroup)
{
	e.stopPropagation();
	var requestArgs = {};

	if(prevRow)
	{
		requestArgs["editting_prev_row"] = true;
	}

	if(visualGroup !== null)
	{
		requestArgs["tab_group_prefix"] = visualGroup;
	}

	requestArgs["field_type"] = fieldType;
	requestArgs["meta_group"] = metaGroupID;
	requestArgs["save_button_id"] = "create_button";

	var requestPayload = {
					"type":"sub", 
					"class":targetClass, 
					"parent_route":parentRoute, 
					"action":"request_getFieldTypeSettings", 
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
					$('#' + metaGroupID).html(data.results);
				}
			}
		}
	});
}

/**********************************************************/
/*USER SIMULATION UTILITIES                               */
/**********************************************************/
function labori_simulateUser(userID)
{
	requestArgs = {};
    requestArgs["user_id"] = userID;

	var requestPayload = {
					"type":"serv", 
					"class":"Service_Users", 
					"parent_route":"", 
					"action":"request_simulateUser", 
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
			console.log(data);

			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					window.location = './' + window.location.search;
				}
			}
		}
	});
}

/**********************************************************/
/*META UTILITIES                                       	  */
/**********************************************************/
function labori_meta_field_generateMachineReadableID(metaNameInput, machineNameField, machineNameGroup)
{
	var tempName = $('#' + metaNameInput).val();
	tempName = labori_replaceAll(" ", "_", tempName).toLowerCase();

	var prevNames = $('[labori__meta_id="' + machineNameGroup + '"]');
	var append = 0;
	var foundID = false;
	var tempTrialName = tempName;

	while(!foundID)
	{
		foundID = true;

		for(var i = 0; i < prevNames.length; i++)
		{
			if($(prevNames[i]).attr('id') == machineNameField)
			{
				continue;
			}

			var tempVal = $(prevNames[i]).attr('actual_value');

			if(tempVal == undefined)
			{
				tempVal = $(prevNames[i]).val();
			}

			if(tempVal !== undefined && tempVal.trim() == tempTrialName)
			{
				foundID = false;
				break;
			}
		}

		if(!foundID)
		{
			tempTrialName = tempName + "_" + String(append);
			append++;
		}
	}

	tempTrialName = tempTrialName.replace(/[^\w]+/g,"");

	$('#' + machineNameField).val(tempTrialName);
}

function labori_meta_field_generateTypeMeta(fieldType)
{
	var newHTML = "";

	if(fieldType == "free_text")
	{
		newHTML = '<div class=" group">'
					 +'<div class="labori_labeled_field_label">Max Length</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input oninput = "labori_checkInput(this);" onblur = "labori_checkInput(this);" validate_lower_bound="1" validation_type = "numeric" class="labori__field labori_validation_required_for__create_button labori_text_input" style="width:100%;" id="new_meta_char_limit" labori__meta_id="meta_char_limit" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Regex</div>'
					+' <div class="labori_labeled_field_field">'
					 	+'<input style="width:100%;" id="new_meta_regex" labori__meta_id="meta_regex" class="labori__field  labori_text_input" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>';
	}
	else if(fieldType == "textarea")
	{
		newHTML = '<div class=" group">'
					 +'<div class="labori_labeled_field_label">Max Length</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input oninput = "labori_checkInput(this);" onblur = "labori_checkInput(this);" validate_lower_bound="1" validation_type = "numeric" class="labori__field labori_validation_required_for__create_button labori_text_input" style="width:100%;" id="new_meta_char_limit" labori__meta_id="meta_char_limit" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Regex</div>'
					+' <div class="labori_labeled_field_field">'
					 	+'<input style="width:100%;" id="new_meta_regex" labori__meta_id="meta_regex" class="labori__field  labori_text_input" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>';
	}
	else if(fieldType == "numeric")
	{
		newHTML = '<div class=" group">'
					 +'<div class="labori_labeled_field_label">Lower Bound</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input oninput = "labori_checkInput(this);" onblur = "labori_checkInput(this);" validation_type = "numeric" class="labori__field labori_validation_required_for__create_button labori_text_input" style="width:100%;" id="new_meta_lower_bound" labori__meta_id="meta_lower_bound" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Upper Bound</div>'
					+' <div class="labori_labeled_field_field">'
					 	+'<input oninput = "labori_checkInput(this);" onblur = "labori_checkInput(this);" validation_type = "numeric" class="labori__field labori_validation_required_for__create_button labori_text_input" style="width:100%;" id="new_meta_upper_bound" labori__meta_id="meta_upper_bound" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Allow Decimals</div>'
					 +'<div class="labori_labeled_field_field">'
					 		+'<select type="text" style="width:99%;" id="new_meta_allow_decimals" labori__meta_id="meta_allow_decimals" class="labori__field" labori__field_type="dropdown" labori__group_id="new_meta_field_meta">'
					 		+'<option value="Yes">Yes</option>'
					    	+'<option value="No">No</option>'
					    +'</select>'
					 +'</div>'
				 +'</div>'
				 ;

		$(document).ready(function() 
		{
			labori_convertDropdowns("#new_meta_allow_decimals");
		});
	}
	else if(fieldType == "date")
	{
		newHTML = '<div class=" group">'
					 +'<div class="labori_labeled_field_label">Min Date</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input style="width:100%;" id="new_meta_min_date" labori__meta_id="meta_min_date" class="labori__field labori_text_input labori_date_field" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Max Date</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input style="width:100%;" id="new_meta_max_date" labori__meta_id="meta_max_date" class="labori__field labori_text_input labori_date_field" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>';

		$(document).ready(function() 
		{
			labori_convertDatePickers("#new_meta_min_date");
			labori_convertDatePickers("#new_meta_max_date");
		});
	}
	else if(fieldType == "dropdown")
	{
		newHTML = '<div style="" class=" group">'
						+'<div class="labori_labeled_field_label">Dropdown Options</div>'
						+'<div class="labori_labeled_field_field">'
							+'<div style="">'
								+'<div class="group">'
								+'<input oninput="labori_autoCompleteReminder(this);" style="width: 85%; float:left;" id="new_meta_dropdown_options" labori__meta_id="meta_dropdown_options" class="labori__field  labori_text_input" labori__field_type="multi_input" labori__group_id="new_meta_field_meta" type="text">'
								+'<div class="labori_tooltip labori_button_round small_button" style="float:right; display:inline-block;" onclick="labori_multiinput_insertMultiInputChoice(\'new_meta_dropdown_options\');">'
									+'<i class="fa fa-plus" aria-hidden="true"></i><span class="labori_tooltiptext">Add Item</span><span class="labori_tooltiptext_noHover">Don\'t forget to press this button to add the item.</span></div>'
								+'</div>'
								+'<div id="new_meta_dropdown_options-multiinput-holder" class="labori_multiinput_holder" style=""></div>'
							+'</div>'
						+'</div>'
			 		+'</div>';
	}
	else if(fieldType == "multi_dropdown")
	{
		newHTML = '<div style="" class=" group">'
						+'<div class="labori_labeled_field_label">Dropdown Options</div>'
						+'<div class="labori_labeled_field_field">'
							+'<div style="">'
								+'<div class="group">'
								+'<input oninput="labori_autoCompleteReminder(this);" style="width: 85%; float:left;" id="new_meta_dropdown_options" labori__meta_id="meta_dropdown_options" class="labori__field  labori_text_input" labori__field_type="multi_input" labori__group_id="new_meta_field_meta" type="text">'
								+'<div class="labori_tooltip labori_button_round small_button" style="float:right; display:inline-block;" onclick="labori_multiinput_insertMultiInputChoice(\'new_meta_dropdown_options\');">'
									+'<i class="fa fa-plus" aria-hidden="true"></i><span class="labori_tooltiptext">Add Item</span><span class="labori_tooltiptext_noHover">Don\'t forget to press this button to add the item.</span></div>'
								+'</div>'
								+'<div id="new_meta_dropdown_options-multiinput-holder" class="labori_multiinput_holder" style=""></div>'
							+'</div>'
						+'</div>'
			 		+'</div>';
	}
	else if(fieldType == "multi_free_text")
	{
		newHTML = '<div class=" group">'
					 +'<div class="labori_labeled_field_label">Max Length</div>'
					 +'<div class="labori_labeled_field_field">'
					 	+'<input oninput = "labori_checkInput(this);" onblur = "labori_checkInput(this);" validate_lower_bound="1" validation_type = "numeric" class="labori__field labori_validation_required_for__create_button labori_text_input" style="width:100%;" id="new_meta_char_limit" labori__meta_id="meta_char_limit" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>'
				 +'<div style="margin-top:5px;" class=" group">'
					 +'<div class="labori_labeled_field_label">Regex</div>'
					+' <div class="labori_labeled_field_field">'
					 	+'<input style="width:100%;" id="new_meta_regex" labori__meta_id="meta_regex" class="labori__field  labori_text_input" labori__field_type="free_text" labori__group_id="new_meta_field_meta" type="text">'
					 +'</div>'
				 +'</div>';
	}

	$('#new_meta_field_meta').html(newHTML);
}

function hideVisualGroup(visualGroupClass)
{
	var visualGroup = $('.' + visualGroupClass);

	for(var i = 0; i < visualGroup.length; i++)
	{
		$($('.' + visualGroupClass)[i]).hide();
	}
}

function showVisualGroup(visualGroupClass)
{
	var visualGroup = $('.' + visualGroupClass);

	for(var i = 0; i < visualGroup.length; i++)
	{
		$($('.' + visualGroupClass)[i]).show();
	}
}

function toggleVisualGroupVisibility(visualGroupClass)
{
	var visualGroup = $('.' + visualGroupClass);

	for(var i = 0; i < visualGroup.length; i++)
	{
		if($($('.' + visualGroupClass)[i]).is(":visible"))
		{
			$($('.' + visualGroupClass)[i]).hide();
		}
		else
		{
			$($('.' + visualGroupClass)[i]).show();
		}
	}
}

function labori_getAllMetaFields(groupID)
{
	var retArray = Array();
	var results = $('.labori__field');
	var tempJQObj = null;

	for(var i = 0; i < results.length; i++)
	{	
		tempJQObj = $('#' + results[i].id);

		if(groupID != null)
		{
			if(groupID == tempJQObj.attr('labori__group_id'))
			{
				retArray.push(tempJQObj);
			}
		}
		else
		{
			retArray.push(tempJQObj);
		}
	}

	return retArray;
}

function labori_getAllMetaAttributes(jQueryObj)
{
	var retArray = {};
	var temp = null;

	for(var i = 0; i < jQueryObj[0].attributes.length; i++)
	{
		temp = jQueryObj[0].attributes[i].nodeName.split("__");

		if(temp.length >= 2 && temp[0] == "labori")
		{
			retArray[temp[1]] = jQueryObj[0].attributes[i].nodeValue;
		}
	}

	return retArray;
}

function labori_extractFieldValue_fromID(metaID)
{
	var tempVal = $("[labori__meta_id='" + metaID + "'");

	if(tempVal !== undefined)
	{
		return labori_extractMetaFieldValue(tempVal);
	}
}

function labori_extractMetaFieldValue(jQueryObj)
{
	var tempAttrs = labori_getAllMetaAttributes(jQueryObj);

	if(tempAttrs["field_type"] == 'free_text')
	{
		if(jQueryObj.attr('validate_date_format') !== undefined)
		{
			if(new RegExp(/^[0-9]{4}-[0-9]?[0-9]-[0-9]?[0-9]$/).test(jQueryObj[0].value))
			{
				return jQueryObj[0].value;
			}
			else
			{
				return moment(jQueryObj[0].value, jQueryObj.attr('validate_date_format').trim()).format("YYYY-MM-DD");
			}
		}
		else
		{
			return jQueryObj[0].value;
		}
	}
	else if(tempAttrs["field_type"] == 'dropdown')
	{
		return jQueryObj.val();
	}
	else if(tempAttrs["field_type"] == 'masked_value')
	{
		return jQueryObj.attr('actual_value');
	}
	else if(tempAttrs["field_type"] == 'autocomplete_single')
	{
		var tempID = jQueryObj.attr("id");
		var tempList = $("#" + tempID + "-autocomplete-single-holder .labori_autocomplete_single_holder_item");
		var retVal = null;

		for(var i = 0; i < tempList.length; i++)
		{
			if(retVal === null && $(tempList[i]).attr("value").trim().length > 0)
			{
				retVal = $(tempList[i]).attr("value");
			}
		}

		if(retVal === null)
		{
			retVal = "";
		}

		return retVal;
	}
	else if(tempAttrs["field_type"] == 'multi_input')
	{
		var tempID = jQueryObj.attr("id");
		var tempList = $("#" + tempID + "-multiinput-holder .labori_multiinput_holder_item");
		var retVal = null;

		for(var i = 0; i < tempList.length; i++)
		{
			if($(tempList[i]).attr("value").trim().length > 0)
			{
				if(retVal === null)
				{
					retVal = $(tempList[i]).attr("value");
				}
				else
				{
					retVal += "~" + $(tempList[i]).attr("value");
				}
			}
		}

		if(retVal === null)
		{
			retVal = "";
		}

		return retVal;
	}
	else if(tempAttrs["field_type"] == 'json_group')
	{
		var tempID = jQueryObj.attr("id");
		var metaFields = labori_getAllMetaFields(tempID);
		retVal = {};
		for(var i = 0; i < metaFields.length; i++)
		{
			retVal[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
		}

		return JSON.stringify(retVal);
	}
}

function labori_checkAllFields()
{
	var allLaboriFields = $('.labori__field');

	for(var i = 0; i < allLaboriFields.length; i++)
	{
		labori_checkInput(allLaboriFields[i]);
		var tempVal = labori_extractMetaFieldValue($(allLaboriFields[i]));

		if(tempVal !== null && tempVal !== undefined && tempVal.length !== 0 && tempVal !== "{}")
		{
			//console.log($(allLaboriFields[i]).attr('labori__meta_id') + ": " + tempVal + ":" + tempVal.length);
			if($(allLaboriFields[i]).hasClass('labori_required_input_override'))
			{
				$(allLaboriFields[i]).removeClass('labori_required_input_override');
			}
			else if($($(allLaboriFields[i]).parent()).hasClass('labori_required_input_override'))
			{
				$($(allLaboriFields[i]).parent()).removeClass('labori_required_input_override');
			}
			else if($($(allLaboriFields[i]).children()).hasClass('labori_required_table_override'))
			{
				$($(allLaboriFields[i]).children()).removeClass('labori_required_table_override');	
			}
		}
	}
}

function labori_checkInput(fieldObj)
{
	var tempValidationType = $(fieldObj).attr('validation_type');
	var classList = $(fieldObj).attr('class').split(/\s+/);
	var validationRequiredFor = undefined;
	var validationRequiredForClass = undefined;

	if(classList !== undefined)
	{
		for(var i = 0; i < classList.length; i++)
		{
			if(classList[i].includes("labori_validation_required_for__"))
			{
				validationRequiredFor = classList[i];
				validationRequiredForClass = classList[i].split("__")[1].trim();
				break;
			}
		}
	}

	if(tempValidationType !== undefined)
	{
		if(tempValidationType == "text_input" || 
		   tempValidationType == "table_input" || 
		   tempValidationType == "multi_text_input")
		{
			var tempVal = $(fieldObj).val().trim();
			var regexField = $(fieldObj).attr('validate_regex');
			var dateFormatField = $(fieldObj).attr('validate_date_format');
			var lengthField = $(fieldObj).attr('validate_char_limit');
			var customError = $(fieldObj).attr('validate_custom_error');
			var invalidInput = false;
			var errorMsg = "";

			if(tempVal.trim().length > 0)
			{
				if(regexField !== undefined)
				{
					regexField = labori_genRegexFromString(regexField);
				}

				if(regexField !== undefined && !regexField.test(tempVal))
				{
					invalidInput = true;
					errorMsg += "This does not match the required format.<br>";
				}

				if(lengthField !== undefined && !isNaN(lengthField))
				{
					if(tempVal.length > parseFloat(lengthField))
					{
						invalidInput = true;
						errorMsg += "Input must be " + lengthField + " characters or less.<br>";
					}
				}

				if(dateFormatField !== undefined)
				{
					if(new RegExp(/^[0-9]{4}-[0-9]?[0-9]-[0-9]?[0-9]$/).test(tempVal) && moment(tempVal, "YYYY-MM-DD").isValid())
					{
						//Do nothing
					}
					else if(moment(tempVal, dateFormatField.trim()).isValid() == false)
					{
						invalidInput = true;
						errorMsg += "Date is not a valid accepted format (" + dateFormatField + ")<br>";
					}
				}
			}

			if(customError !== undefined)
			{
				errorMsg = customError;
			}

			if(invalidInput)
			{
				if(tempValidationType == "multi_text_input")
				{
					$('#' + $(fieldObj).attr('id') + "_button").addClass("disabled");
				}

				var warningID = labori_generateUniqueID();
				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID == undefined)
				{
					$(fieldObj).attr('validate_warning_id', 'validate_warning_' + warningID);
					$(fieldObj).before('<div error_message=\"' + errorMsg + '\" onclick=\"labori_changePopupDialog(\'Input Error\', ' + 
									   '$(\'#validate_warning_' + warningID + '\').attr(\'error_message\')' + '); $(\'#labori_popup_overlay\').show();\" ' + 
									   "id='validate_warning_" + warningID + "' class='labori_input_warning'><i class='fa fa-question-circle' aria-hidden='true'></i></div>");
				}
				else
				{
					$('#' + tempWarningID).attr('error_message', errorMsg);
				}

				$(fieldObj).addClass("labori_field_error");
			}
			else
			{
				if(tempValidationType == "multi_text_input")
				{
					$('#' + $(fieldObj).attr('id') + "_button").removeClass("disabled");
				}

				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID != undefined)
				{
					$('#' + tempWarningID).remove();
					$(fieldObj).removeAttr('validate_warning_id');
				}

				$(fieldObj).removeClass("labori_field_error");
			}
		}
		else if(tempValidationType == "numeric")
		{
			var tempVal = $(fieldObj).val().trim();
			var allowDecimals = $(fieldObj).attr('validate_allow_decimals');
			var lowerBound = $(fieldObj).attr('validate_lower_bound');
			var upperBound = $(fieldObj).attr('validate_upper_bound');
			var customError = $(fieldObj).attr('validate_custom_error');
			var invalidInput = false;
			var errorMsg = "";

			if(tempVal.length > 0 && isNaN(tempVal))
			{
				invalidInput = true;
				errorMsg += "Input must be a number.<br>";
			}
			else if(tempVal.length > 0)
			{
				strVal = tempVal;
				tempVal = parseFloat(tempVal);

				if(allowDecimals !== undefined && allowDecimals == "No" && strVal.includes("."))
				{
					invalidInput = true;
					errorMsg += "Input must be an integer.<br>";
				}

				if(lowerBound !== undefined && !isNaN(lowerBound))
				{
					if(tempVal < parseFloat(lowerBound))
					{
						invalidInput = true;
						errorMsg += "Input must be greater than or equal to " + lowerBound + ".<br>";
					}
				}

				if(upperBound !== undefined && !isNaN(upperBound))
				{
					if(tempVal > parseFloat(upperBound))
					{
						invalidInput = true;
						errorMsg += "Input must be less than or equal to " + upperBound + ".<br>";
					}
				}

				if(customError !== undefined)
				{
					errorMsg = customError;
				}
			}

			if(invalidInput)
			{
				var warningID = labori_generateUniqueID();
				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID == undefined)
				{
					$(fieldObj).attr('validate_warning_id', 'validate_warning_' + warningID);
					$(fieldObj).before('<div error_message=\"' + errorMsg + '\" onclick=\"labori_changePopupDialog(\'Input Error\', ' + 
									   '$(\'#validate_warning_' + warningID + '\').attr(\'error_message\')' + '); $(\'#labori_popup_overlay\').show();\" ' + 
									   "id='validate_warning_" + warningID + "' class='labori_input_warning'><i class='fa fa-question-circle' aria-hidden='true'></i></div>");
				}
				else
				{
					$('#' + tempWarningID).attr('error_message', errorMsg);
				}

				$(fieldObj).addClass("labori_field_error");
			}
			else
			{
				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID != undefined)
				{
					$('#' + tempWarningID).remove();
					$(fieldObj).removeAttr('validate_warning_id');
				}

				$(fieldObj).removeClass("labori_field_error");
			}
		}
		else if(tempValidationType == "date")
		{
			var tempVal = Date.parse($(fieldObj).val().trim());
			var maxDate = $(fieldObj).attr('validate_max_date');
			var minDate = $(fieldObj).attr('validate_min_date');
			var customError = $(fieldObj).attr('validate_custom_error');
			var invalidInput = false;
			var errorMsg = "";

			if(minDate !== undefined)
			{
				if(tempVal < Date.parse(minDate))
				{
					invalidInput = true;
					errorMsg += "Date must occur on or after " + minDate + ".<br>";
				}
			}

			if(maxDate !== undefined)
			{
				if(tempVal > Date.parse(maxDate))
				{
					invalidInput = true;
					errorMsg += "Date must occur on or before " + maxDate + ".<br>";
				}
			}

			if(customError !== undefined)
			{
				errorMsg = customError;
			}
			
			if(invalidInput)
			{
				var warningID = labori_generateUniqueID();
				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID == undefined)
				{
					$(fieldObj).attr('validate_warning_id', 'validate_warning_' + warningID);
					$(fieldObj).before('<div error_message=\"' + errorMsg + '\" onclick=\"labori_changePopupDialog(\'Input Error\', ' + 
									   '$(\'#validate_warning_' + warningID + '\').attr(\'error_message\')' + '); $(\'#labori_popup_overlay\').show();\" ' + 
									   "id='validate_warning_" + warningID + "' class='labori_input_warning'><i class='fa fa-question-circle' aria-hidden='true'></i></div>");
				}
				else
				{
					$('#' + tempWarningID).attr('error_message', errorMsg);
				}

				$(fieldObj).addClass("labori_field_error");
			}
			else
			{
				var tempWarningID = $(fieldObj).attr('validate_warning_id');

				if(tempWarningID != undefined)
				{
					$('#' + tempWarningID).remove();
					$(fieldObj).removeAttr('validate_warning_id');
				}

				$(fieldObj).removeClass("labori_field_error");
			}
		}

		if(validationRequiredFor !== undefined)
		{
			labori_toggleButtonLockOnValidation(validationRequiredForClass, validationRequiredFor);
		}
	}
}

function labori_validate(jQueryObj)
{
	return true;
}

/**********************************************************/
/*POPUP UTILITIES                               	  	  */
/**********************************************************/
function labori_setupDataReminder()
{
	window.addEventListener("beforeunload", function (e) {
		if(labori_cancelDataCheck !== true)
		{
		    var confirmationMessage = 'Data may have been entered. '
		                            + 'If you leave before saving, your changes will be lost.';

		    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
		    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
		}
	});
}

function labori_prepareYesNoDialog(capHTML, bodyMessage, onClickStr)
{
	$('#labori_popup_cap').html(capHTML);
	var buttonStyle = "float: left; width: 40%; margin-top: 30px;";
	onClickStr = labori_replaceAll("`", '&quot;', onClickStr);
	bodyMessage += '<br><div style="' + buttonStyle + '" class="labori_button red_button" onclick="' + onClickStr + '; labori_clearPopup();">Yes</div>';
	bodyMessage += '<div style="' + buttonStyle + ' margin-left:10px;" class="labori_button red_button" onclick="labori_clearPopup();">No</div>';
	$('#labori_popup_body').html(bodyMessage);
	$('#labori_popup_overlay').show();
	$('body').addClass('disable_scroll');
}

function labori_prepareYesNoDialogWithCustomButtons(capHTML, bodyMessage, yesText, noText, onClickStr)
{
	$('#labori_popup_cap').html(capHTML);
	var buttonStyle = "float: left; width: 40%; margin-top: 30px;";
	onClickStr = labori_replaceAll("`", '&quot;', onClickStr);
	bodyMessage += '<br><div style="' + buttonStyle + '" class="labori_button red_button" onclick="' + onClickStr + '; labori_clearPopup();">' + yesText +'</div>';
	bodyMessage += '<div style="' + buttonStyle + ' margin-left:10px;" class="labori_button red_button" onclick="labori_clearPopup();">' + noText + '</div>';
	$('#labori_popup_body').html(bodyMessage);
	$('#labori_popup_overlay').show();
	$('body').addClass('disable_scroll');
}

function labori_changePopupDialogCustom(capHTML, bodyHTML)
{
	$('#labori_popup_cap').html(capHTML);
	$('#labori_popup_body').html(bodyHTML);
}

function labori_changePopupDialogToSuccess()
{
	$('#labori_popup_cap').addClass('labori_popup_cap_success');
	$('#labori_popup_container').addClass('labori_popup_container_success');
}

function labori_changePopupDialog(capHTML, bodyMessage)
{
	$('#labori_popup_cap').html(capHTML);
	var buttonStyle = "width: 80%; margin-left:auto; margin-right:auto; margin-top: 30px;";
	bodyMessage += '<div style="' + buttonStyle + '" class="labori_button red_button" onclick="labori_clearPopup();">Ok</div>';
	$('#labori_popup_body').html(bodyMessage);
}

function labori_prepareDialog(capHTML, bodyMessage)
{
	$('#labori_popup_cap').html(capHTML);
	var buttonStyle = "width: 80%; margin-left:auto; margin-right:auto; margin-top: 30px;";
	bodyMessage += '<div style="' + buttonStyle + '" class="labori_button red_button" onclick="labori_clearPopup();">Ok</div>';
	$('#labori_popup_body').html(bodyMessage);
	$('#labori_popup_overlay').show();
	$('body').addClass('disable_scroll');
}

function labori_clearPopup()
{
	$('#labori_popup_overlay').hide();
	$('#labori_popup_cap').html('');
	$('#labori_popup_body').html('');
	$('#labori_popup_cap').removeClass('labori_popup_cap_success');
	$('#labori_popup_container').removeClass('labori_popup_container_success');
	$('body').removeClass('disable_scroll');
}

function labori_showPopup()
{
	$('#labori_popup_overlay').show();
	$('body').addClass('disable_scroll');
}

/**********************************************************/
/*INTERFACE UTILITIES                               	  */
/**********************************************************/
function labori_checkAutoCompleteField(field)
{
	if($(field).val() == 'Start typing to search...')
	{
		$(field).val('');
	}
}

function labori_selectTab(currentTab, tabVisualGroup, tabButtonGroup, showID)
{
	$('.' + tabVisualGroup).hide();
	$('.' + tabButtonGroup).removeClass('selected_tab');
	$(currentTab).addClass('selected_tab');
	$('#' + showID).show();
}

function labori_selectDropdownOpt(selectID, selectOpt)
{
	$('#' + selectID).val(selectOpt);
	$($($('#'+ selectID).parent()[0]).children()[1]).html($('#'+ selectID + ' option[value="' + selectOpt + '"]').attr('selected','selected').text());
}

function labori_groupSliders(groupSelector)
{
	var sliders = undefined;

	if(groupSelector === undefined)
	{
		sliders = $(".labori_slider_group .labori_slider");
	}
	else
	{
		sliders = $(groupSelector + " .labori_slider");
	}

	sliders.each(function() 
	{
	    var availableTotal =  parseInt($(this).attr("max"), 10);

	    $(this).on('input change', function(){
	    	var total = 0;    

			sliders.not(this).each(function() 
			{
				total += parseInt($(this).val(), 10);
			});

			var tempVal = parseInt($(this).val(), 10);
	        var leftOver = availableTotal - total - tempVal;            
			
			while(leftOver < 0)
			{
				var maxSlider = undefined;
				var maxAmount = undefined;

				sliders.not(this).each(function() 
				{
					if(maxAmount == undefined || maxAmount < $(this).val())
					{
						maxSlider = this;
						maxAmount = $(this).val();
					}
				});

				if(maxSlider != undefined)
				{
					$(maxSlider).val( parseInt($(maxSlider).val(), 10) - 1);

					var linkedInput = $('#' + 'linked__' + $(maxSlider).attr('id'));

					if(linkedInput != undefined)
					{
						linkedInput.val($(maxSlider).val());
					}

					leftOver += 1;
				}
				else
				{
					break;
				}
			}

			while(leftOver > 0)
			{
				var minSlider = undefined;
				var minAmount = undefined;

				sliders.not(this).each(function() 
				{
					if(minAmount == undefined || minAmount > $(this).val())
					{
						if($(this).val() > 0)
						{
							minSlider = this;
							minAmount = $(this).val();
						}
					}
				});

				if(minSlider == undefined)
				{
					for(var i =0; i < sliders.length; i++)
					{
						if(sliders[i] != this)
						{
							minSlider = sliders[i];
							minAmount = $(sliders[i]).val();
							break;
						}
					}
				}

				if(minSlider != undefined)
				{
					$(minSlider).val( parseInt($(minSlider).val(), 10) + 1);

					var linkedInput = $('#' + 'linked__' + $(minSlider).attr('id'));

					if(linkedInput != undefined)
					{
						linkedInput.val($(minSlider).val());
					}

					leftOver -= 1;
				}
				else
				{
					break;
				}
			}
	    });
	});
}

function labori_toggleButtonLockOnValidation(buttonId, requiredClass)
{
	var allFields = Array();

	var results = $('.' + requiredClass);
	var tempJQObj = null;

	for(var i = 0; i < results.length; i++)
	{	
		tempJQObj = $('#' + results[i].id);
		allFields.push(tempJQObj);
	}

	var enable = true;

	for(var i = 0; i < allFields.length; i++)
	{
		if(allFields[i].hasClass('labori_field_error'))
		{
			enable = false;
			break;
		}
	}

	if(enable)
	{

		$('#' + buttonId).prop('disabled', false);
		$('#' + buttonId).removeClass('disabled');
		labori_toggleButtonLockWithID(undefined, buttonId, "labori_required_for_" + buttonId);
	}
	else
	{
		$('#' + buttonId).prop('disabled', true);
		$('#' + buttonId).addClass('disabled');
	}	
}

function labori_toggleButtonLockWithID(elementID, buttonId, requiredClass)
{
	labori_toggleButtonLock($('#' + elementID), buttonId, requiredClass);
}

function labori_toggleButtonLock(element, buttonId, requiredClass)
{
	var allFields = Array();

	var results = $('.' + requiredClass);
	var tempJQObj = null;

	for(var i = 0; i < results.length; i++)
	{	
		tempJQObj = $('#' + results[i].id);
		allFields.push(tempJQObj);
	}

	var enable = true;

	for(var i = 0; i < allFields.length; i++)
	{
		var tempFeedbackID = allFields[i].attr('labori__feedback_container');

		if(tempFeedbackID === undefined)
		{
			continue;
		}

		if(allFields[i].hasClass('labori__override_requirements'))
		{
			$('#' + tempFeedbackID).html('');
			continue;
		}

		if(allFields[i].hasClass('labori_field_error'))
		{
			$('#' + tempFeedbackID).html("(field has an error)");
			continue;
		}

		var tempVal = labori_extractMetaFieldValue(allFields[i]);
		var fieldType = labori_getAllMetaAttributes(allFields[i])["field_type"];

		if(tempVal == null || tempVal.trim() == '{}' || tempVal.trim().length == 0)
		{
			$('#' + tempFeedbackID).html('(field is required)');
		}
		else
		{
			$('#' + tempFeedbackID).html('');
		}
	}

	for(var i = 0; i < allFields.length; i++)
	{
		if(allFields[i].hasClass('labori__override_requirements'))
		{
			continue;
		}

		if(allFields[i].hasClass('labori_field_error'))
		{
			enable = false;
			break;
		}

		var tempVal = labori_extractMetaFieldValue(allFields[i]);
		var fieldType = labori_getAllMetaAttributes(allFields[i])["field_type"];

		if(tempVal == null || tempVal.trim() == '{}' || tempVal.trim().length == 0)
		{
			enable = false;
			break;
		}
	}

	if(enable)
	{
		$('#' + buttonId).addClass('labori_tooltip_shown');
		$('#' + buttonId).prop('disabled', false);
		$('#' + buttonId).removeClass('disabled');
	}
	else
	{
		$('#' + buttonId).removeClass('labori_tooltip_shown');
		$('#' + buttonId).prop('disabled', true);
		$('#' + buttonId).addClass('disabled');
	}	
}

/**********************************************************/
/*MULTI DROPDOWN UTILITIES                                */
/**********************************************************/
function labori_multiinput_deleteItem(item)
{
	var tempID = $($(item).parent().parent()).attr('id');
	var tempValue = $($(item).parent()).attr('value');
	var parentID = undefined;

	if(tempID !== undefined)
	{
		var splitArr = tempID.split("-");

		if(splitArr.length > 0)
		{
			parentID = splitArr[0].trim();
			
			if($('#' + parentID).attr('labori_required_for') !== undefined)
			{
				if($($(item).parent().parent()).find('.labori_multiinput_holder_item').length <= 1)
				{
					if($('#' + parentID).prop("tagName").toLowerCase() == 'input')
					{
						$('#' + parentID).addClass('labori_required_input_override');
					}
					else if($('#' + parentID).prop("tagName").toLowerCase() == 'select')
					{
						$($('#' + parentID).parent()).addClass('labori_required_input_override');
					}
				}
			}
		}

		if(tempValue == "LABORI__DROPDOWN__OTHER")
		{
			var tempOtherID = $($(item).parent()).attr('other_field_id').trim();

			if(tempOtherID !== undefined)
			{
				$('#' + tempOtherID).hide();
			}
		}
	}

	$($(item).parent()).remove();

	if($('#' + parentID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(undefined, $('#' + parentID).attr('labori_required_for'), 'labori_required_for_' + $('#' + parentID).attr('labori_required_for'));
	}
}

function labori_multiinput_insertMultiInputChoice(inputID)
{
	if($('#' + inputID).hasClass("labori_field_error"))
	{
		return;
	}

	var choiceVal = $('#' + inputID).val();
	$('#' + inputID).val('');

	var newItem = '<div value="' + choiceVal + '" class="labori_multiinput_holder_item group">'+
					'<div style="float:left; display:inline-block;">' + choiceVal + '</div>'+
					'<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" '+
					'onclick="labori_multiinput_deleteItem(this);">'+
					'<i class=\'fa fa-close\' aria-hidden=\'true\'></i><span class="labori_tooltiptext">Delete Item</span></div>' +
					'</div>';

	$('#' + inputID + "-multiinput-holder").html($('#' + inputID + "-multiinput-holder").html() + newItem);

	if($('#' + inputID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(inputID, $('#' + inputID).attr('labori_required_for'), 'labori_required_for_' + $('#' + inputID).attr('labori_required_for'));
		$('#' + inputID).removeClass('labori_required_input_override');
	}
}

function labori_multidropdown_insertMultiDropdownChoice(selectID, choice)
{
	var choiceVal = $(choice).attr("rel");
	var choiceText = $(choice).html();
	var extra = '';

	var selectedOpts = $('#' + selectID + "-multiinput-holder .labori_multiinput_holder_item");

	for(var i = 0; i < selectedOpts.length; i++)
	{
		if(choiceVal == $(selectedOpts[i]).attr('value'))
		{
			return;
		}
	}

	if($(choice).attr("other_field_id"))
	{
		extra = ' other_field_id =" ' + $(choice).attr("other_field_id").trim() + '" ';
	}

	var newItem = '<div ' + extra + ' value="' + choiceVal + '" class="labori_multiinput_holder_item group">'+
					'<div style="float:left; display:inline-block;">' + choiceText + '</div>'+
					'<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" '+
					'onclick="labori_multiinput_deleteItem(this);">'+
					'<i class=\'fa fa-close\' aria-hidden=\'true\'></i><span class="labori_tooltiptext">Delete Item</span></div>' +
					'</div>';

	$('#' + selectID + "-multiinput-holder").html($('#' + selectID + "-multiinput-holder").html() + newItem);

	if($('#' + selectID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(selectID, $('#' + selectID).attr('labori_required_for'), 'labori_required_for_' + $('#' + selectID).attr('labori_required_for'));
		$($('#' + selectID).parent()).removeClass('labori_required_input_override');
	}
}

/**********************************************************/
/*AUTOCOMPLETE UTILITIES                               	  */
/**********************************************************/
function labori_autoCompleteReminder(inputObj)
{
	if($(inputObj).val().trim().length > 0)
	{
		$($(inputObj).parent().find('.labori_button_round')).addClass('labori_tooltip_shown');
	}
	else
	{
		$($(inputObj).parent().find('.labori_button_round')).removeClass('labori_tooltip_shown');
	}
}

function labori_autoCompleteAddValue(selectID, $key, $value)
{
	var choiceVal = $key;
	var choiceText = $value;

	var onItemChange = $('#' + selectID).attr('labori_onItemChange');
	var extraOnDelete = '';

	if(onItemChange != undefined && onItemChange.length > 0)
	{
		extraOnDelete = onItemChange + "('" + choiceVal + "', false);";
		var tempFun = labori_getFunctionFromString(onItemChange);
		tempFun(choiceVal, true);

	}

	var newItem = '<div value="' + choiceVal + '" class="labori_multiinput_holder_item group">'+
					'<div style="float:left; display:inline-block;">' + choiceText + '</div>'+
					'<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" '+
					'onclick="labori_multiinput_deleteItem(this); ' + extraOnDelete + '">'+
					'<span class="labori_tooltiptext">Delete Item</span>' + 
					'<i class=\'fa fa-close\' aria-hidden=\'true\'></i></div>' +
					'</div>';

	$('#' + selectID + "-multiinput-holder").html($('#' + selectID + "-multiinput-holder").html() + newItem);
	$('#' + selectID).val('');

	if($('#' + selectID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(selectID, $('#' + selectID).attr('labori_required_for'), 'labori_required_for_' + $('#' + selectID).attr('labori_required_for'));
		$('#' + selectID).removeClass('labori_required_input_override');
	}
}

function labori_autoCompleteAddValueSingle(ev, selectID, key, value)
{
	ev.stopPropagation();
	var choiceVal = key;
	var choiceText = value;

	var onItemChange = $('#' + selectID).attr('labori_onItemChange');
	var extraOnDelete = '';

	if(onItemChange != undefined && onItemChange.length > 0)
	{
		extraOnDelete = onItemChange + "('" + choiceVal + "', false);";
		var tempFun = labori_getFunctionFromString(onItemChange);
		tempFun(choiceVal, true);

	}

	var newItem = '<div value="' + choiceVal + '" class="labori_autocomplete_single_holder_item group">'+
					'<div class="labori_autocomplete_single_holder_item_html" style="float:left; display:inline-block;">' + choiceText + '</div>'+
					'<div class="labori_tooltip labori_button_round tiny_button" style="float:right; display:inline-block;" '+
					'onclick="labori_autoCompleteSingle_deleteItem(\'' + selectID + '\', this); ' + extraOnDelete + '">'+
					'<span class="labori_tooltiptext">Delete Item</span>' + 
					'<i class=\'fa fa-close\' aria-hidden=\'true\'></i></div>' +
					'</div>';

	$('#' + selectID).hide();
	$('#' + selectID + "-autocomplete-single-holder").html($('#' + selectID + "-autocomplete-single-holder").html() + newItem);
	$('#' + selectID).val('');

	if($('#' + selectID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(selectID, $('#' + selectID).attr('labori_required_for'), 'labori_required_for_' + $('#' + selectID).attr('labori_required_for'));
		$('#' + selectID).removeClass('labori_required_input_override');
	}
}

function labori_autoCompleteSingle_deleteItem(selectID, item)
{
	$('#' + selectID).val('');
	$('#' + selectID).show();
	$($(item).parent()).remove();

	if($('#' + selectID).attr('labori_required_for') !== undefined)
	{
		labori_toggleButtonLockWithID(undefined, $('#' + selectID).attr('labori_required_for'), 'labori_required_for_' + $('#' + selectID).attr('labori_required_for'));
	}
}

function labori_hideAutoComplete(inputID)
{
	$('#' + inputID + "-autocomplete").html();
	$('#' + inputID + "-autocomplete").hide();
}

function labori_callAutoCompleteFunctionSingle(inputID, optArgs, type, targetClass, parentRoute, action)
{
	labori_hideAutoComplete(inputID);
	var requestArgs = {};

	requestArgs["search"] = $('#' + inputID).val();

	if(optArgs != null)
	{
		Object.keys(optArgs).forEach(function(key) 
		{
			requestArgs[key] = optArgs[key];	
		});
	}

	var requestPayload = {
				"type":type, 
				"class":targetClass, 
				"parent_route":parentRoute, 
				"action":action, 
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
					var tempHTML = "";

					for(var i = 0; i < data.response.length; i++)
					{
						tempHTML += '<div class="labori_autocomplete_option" onMouseDown="labori_autoCompleteAddValueSingle(event, \'' + inputID + '\',\'' + data.response[i].key +'\',\'' + 
									labori_replaceAll("'", "", data.response[i].value) +'\')">' + labori_replaceAll("'", "", data.response[i].value) + '</div>';
					}

					$('#' + inputID + "-autocomplete").html(tempHTML);
					$('#' + inputID + "-autocomplete").show();

					$('#' + inputID + "-autocomplete").css("top", "");
					var viewportHeight = $(window).height() - $(window).scrollTop();
					var heightAdjustment = $('#' + inputID + "-autocomplete").height();

					/*if(heightAdjustment + $('#' + inputID).offset().top + 30 - $(window).scrollTop() > viewportHeight)
					{
						$('#' + inputID + "-autocomplete").css("top", "-" + heightAdjustment + "px");
					}
					else
					{
						$('#' + inputID + "-autocomplete").css("top", "");
					}*/
				}
			}
		}
	});
}

function labori_callAutoCompleteFunction(inputID, optArgs, type, targetClass, parentRoute, action)
{
	labori_hideAutoComplete(inputID);
	var requestArgs = {};

	requestArgs["search"] = $('#' + inputID).val();

	if(optArgs != null)
	{
		Object.keys(optArgs).forEach(function(key) 
		{
			requestArgs[key] = optArgs[key];	
		});
	}

	var requestPayload = {
				"type":type, 
				"class":targetClass, 
				"parent_route":parentRoute, 
				"action":action, 
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
					var tempHTML = "";

					for(var i = 0; i < data.response.length; i++)
					{
						tempHTML += '<div class="labori_autocomplete_option" onMouseDown="labori_autoCompleteAddValue(\'' + inputID + '\',\'' + data.response[i].key +'\',\'' + 
									labori_replaceAll("'", "", data.response[i].value) +'\')">' + labori_replaceAll("'", "", data.response[i].value) + '</div>';
					}

					$('#' + inputID + "-autocomplete").html(tempHTML);
					$('#' + inputID + "-autocomplete").show();

					$('#' + inputID + "-autocomplete").css("top", "");
					var viewportHeight = $(window).height() - $(window).scrollTop();
					var heightAdjustment = $('#' + inputID + "-autocomplete").height();

					/*if(heightAdjustment + $('#' + inputID).offset().top + 30 - $(window).scrollTop() > viewportHeight)
					{
						$('#' + inputID + "-autocomplete").css("top", "-" + heightAdjustment + "px");
					}
					else
					{
						$('#' + inputID + "-autocomplete").css("top", "");
					}*/
				}
			}
		}
	});
}

/**********************************************************/
/*TABLE UTILITIES                              	  		  */
/**********************************************************/
function labori_moveRow(btn, direction)
{
	var allRows = $(btn).parents("tbody:first").children();

	if(allRows.length > 3)
	{
		var firstRow = $(allRows[1]).prev().next();
		var lastRow = $(allRows[allRows.length-1]).prev().next();

		var row = $(btn).parents("tr:first");

	    if(direction == "up" && row.prev()[0] != firstRow[0])
	    {
	        row.insertBefore(row.prev());
	    } 
	    else if(row.next()[0] != lastRow[0])
	    {
	        row.insertAfter(row.next());
	    }
	}
}

function labori_toggleTableRequirement(tableObjID)
{
	var parentTable = $('#' + tableObjID);

	if($(parentTable).attr('labori_required_for') !== undefined)
	{
		if($(parentTable).find('tr').length > 2)
		{
			$(parentTable).removeClass('labori_required_table_override');
		}
		else
		{
			$(parentTable).addClass('labori_required_table_override');
		}

		labori_toggleButtonLockWithID(undefined, $(parentTable).attr('labori_required_for'), 'labori_required_for_' + $(parentTable).attr('labori_required_for'));
	}
}

function labori_insertTableRow(saveBtn, deleteRow, groupId, metaId)
{
	var oldSaveBtnId = $(saveBtn).attr('id');
	var ignoreColumnId = $(saveBtn).parent().attr('id');
	var insertDataRowId = $(saveBtn).parent().parent().attr('id');
	var tempID = labori_generateUniqueID();
	var newBtnId = tempID + "_save_button";
	var row = $('#' + insertDataRowId).children();
	var rowElements = Array();
	var tempRowElements = undefined;
	var tempRowObj = undefined;
	var parentTable = $(saveBtn).parent().parent().parent().parent().attr('id');

	for(var i = 0; i < row.length; i++)
	{
		if($(row[i]).attr('id') == ignoreColumnId)
		{
			continue;
		}

		tempRowElements = $(row[i]).children();

		for(var j = 0; j < tempRowElements.length; j++)
		{
			var tempClasses = undefined;
			var tempOnBlur = undefined;
			var tempOnInput = undefined;
			var tempOnClick = undefined;

			if($(tempRowElements[j]).prop("tagName") == "DIV")
			{
				var divElements = $(tempRowElements[j]).children();

				for(var k = 0; k < divElements.length; k++)
				{
					if($(divElements[k]).attr('class') == 'labori_autocomplete_single_holder')
					{
						var prevElement = $(divElements[k]).prev();
						var actualElement = undefined;

						if(prevElement != undefined && prevElement.children().length > 0)
						{
							actualElement = $(prevElement.children()[0]);

							if(actualElement.attr('class') != undefined)
							{
								tempClasses = actualElement.attr('class').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
							}

							tempRowObj = {
							"orig_id":actualElement.attr('id'),
							"id": tempID + "_" + actualElement.attr('id'),
							"classes":  tempClasses,
							"style":  actualElement.attr('style'),
							"tag":  actualElement.prop("tagName"),
							"html_type":  actualElement.attr("type"),
							"meta_id":  actualElement.attr("labori__meta_id"),
							"field_type":  'masked_value',
							'original_field_type' : 'autocomplete_single',
							"group_id": actualElement.attr("labori__group_id"),
							"required":  actualElement.attr("required"),
							"readonly":  actualElement.attr("readonly"),
							"value_attr": $(divElements[k]).find('.labori_autocomplete_single_holder_item').attr('value'),
							"value_val":  $(divElements[k]).find('.labori_autocomplete_single_holder_item').attr('value'),
							"value_html":   $(divElements[k]).find('.labori_autocomplete_single_holder_item_html').html()
							};
							rowElements.push(tempRowObj);
							break;
						}	
					}
					else if($(divElements[k]).attr('class') == 'labori_select-options')
					{
						var tempParent = $($(divElements[k]).parent());
						var tempVal = tempParent.find('.labori_select-hidden').val();
						var tempHTML = tempParent.find('.labori_select-styled').html().trim();
						var actualElement = $(tempParent.find('select')[0]);

						if(actualElement.attr('class') != undefined)
						{
							tempClasses = actualElement.attr('class').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
						}

						tempRowObj = {
							"orig_id":actualElement.attr('id'),
							"id": tempID + "_" + actualElement.attr('id'),
							"classes":  tempClasses,
							"style":  actualElement.attr('style'),
							"tag":  "INPUT",
							"html_type":  actualElement.attr("type"),
							"meta_id":  actualElement.attr("labori__meta_id"),
							"field_type":  'masked_value',
							'original_field_type' : 'labori_select',
							"group_id": actualElement.attr("labori__group_id"),
							"required":  actualElement.attr("required"),
							"readonly":  actualElement.attr("readonly"),
							"value_attr":tempVal,
							"value_val":  tempVal,
							"value_html":   tempHTML
							};
							rowElements.push(tempRowObj);
							break;
					}
				}
			}
			else
			{
				if($(tempRowElements[j]).attr('class') != undefined)
				{
					tempClasses = $(tempRowElements[j]).attr('class').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
				}

				if($(tempRowElements[j]).attr('onblur') != undefined)
				{
					tempOnBlur = $(tempRowElements[j]).attr('onblur').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
					tempOnBlur = tempOnBlur.replace(oldSaveBtnId, newBtnId);
					tempOnBlur = tempOnBlur.replace( $(tempRowElements[j]).attr('id'), tempID + "_" + $(tempRowElements[j]).attr('id'));
				}

				if($(tempRowElements[j]).attr('oninput') != undefined)
				{
					tempOnInput = $(tempRowElements[j]).attr('oninput').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
					tempOnInput = tempOnInput.replace(oldSaveBtnId, newBtnId);
					tempOnInput = tempOnInput.replace( $(tempRowElements[j]).attr('id'), tempID + "_" + $(tempRowElements[j]).attr('id'));
				}

				if($(tempRowElements[j]).attr('onclick') != undefined)
				{
					tempOnClick = $(tempRowElements[j]).attr('onclick').replace('labori_required_for_' + oldSaveBtnId, 'labori_required_for_' + newBtnId);
					tempOnClick = tempOnClick.replace(oldSaveBtnId, newBtnId);
					tempOnClick = tempOnClick.replace( $(tempRowElements[j]).attr('id'), tempID + "_" + $(tempRowElements[j]).attr('id'));
				}

				tempRowObj = {
					"orig_id": $(tempRowElements[j]).attr('id'),
					"id": tempID + "_" + $(tempRowElements[j]).attr('id'),
					"classes":  tempClasses,
					"style":  $(tempRowElements[j]).attr('style'),
					"tag":  $(tempRowElements[j]).prop("tagName"),
					"html_type":  $(tempRowElements[j]).attr("type"),
					"meta_id":  $(tempRowElements[j]).attr("labori__meta_id"),
					"field_type":  $(tempRowElements[j]).attr("labori__field_type"),
					"original_field_type":  $(tempRowElements[j]).attr("labori__field_type"),
					"group_id":  $(tempRowElements[j]).attr("labori__group_id"),
					"required":  $(tempRowElements[j]).attr("required"),
					"readonly":  $(tempRowElements[j]).attr("readonly"),
					"value_attr":  $(tempRowElements[j]).attr("value"),
					"onblur":  tempOnBlur,
					"onclick":  tempOnClick,
					"oninput":  tempOnInput,
					"value_val":  $(tempRowElements[j]).val(),
					"value_html":  $(tempRowElements[j]).html()
				};

				rowElements.push(tempRowObj);
			}
		}	
	}

	var genHTML = "<tr id='new_row_" + tempID + "' ";
	var dateFieldArray = Array();
	var valsToAdd = Array();

	if(groupId != undefined && metaId != undefined)
	{
		genHTML += ' labori__meta_id="' + tempID + "_" + metaId + 
				   '" class="labori_table_row labori__field  group" labori__field_type="json_group" labori__group_id="' + groupId + '">';
	}
	else
	{
		genHTML += "' class='labori_table_row>";
	}

	for(var i = 0; i < rowElements.length; i++)
	{
		genHTML += "<td id='new_column_" + tempID + "' class='labori_table_row_item'>";

		if(rowElements[i].value_val != undefined)
		{
			genHTML += '<div id="' + rowElements[i].id + '_shownVal" class="' + tempID + '_vgid">' + rowElements[i].value_val + '</div>';
		}
		else if(rowElements[i].value_attr != undefined)
		{
			genHTML += '<div id="' + rowElements[i].id + '_shownVal" class="' + tempID + '_vgid">' + rowElements[i].value_attr + '</div>';
		}	
		else if(rowElements[i].value_html != undefined)
		{
			genHTML += '<div id="' + rowElements[i].id + '_shownVal" class="' + tempID + '_vgid">' + rowElements[i].value_html + '</div>';
		}

		genHTML += '<div style="display:none;" class="' + tempID + '_vgid">';

		if(rowElements[i].tag == "INPUT" || rowElements[i].tag == "TEXTAREA")
		{
			genHTML +="<input ";
			genHTML += "id='" +  rowElements[i].id + "' ";
			genHTML += "class='" +  rowElements[i].classes + "' ";
			genHTML += "labori__group_id='new_row_" +  tempID + "' ";

			if(rowElements[i].classes.includes("labori_date_field"))
			{
				dateFieldArray.push(rowElements[i].id);
			}

			if(rowElements[i].style != undefined)
			{
				genHTML += "style='" +  rowElements[i].style + "' ";
			}

			if(rowElements[i].html_type != undefined)
			{
				genHTML += "type='" +  rowElements[i].html_type + "' ";
			}

			if(rowElements[i].onblur != undefined)
			{
				genHTML += "onblur=\"" +  rowElements[i].onblur + "\" ";
			}

			if(rowElements[i].onclick != undefined)
			{
				genHTML += "onclick=\"" +  rowElements[i].onclick + "\" ";
			}

			if(rowElements[i].oninput != undefined)
			{
				genHTML += "oninput=\"" +  rowElements[i].oninput + "\" ";
			}

			if(rowElements[i].required != undefined)
			{
				genHTML += "required ";
			}

			if(rowElements[i].meta_id != undefined)
			{
				genHTML += "labori__meta_id='" +  rowElements[i].meta_id + "' ";
			}

			if(rowElements[i].field_type != undefined)
			{
				genHTML += "labori__field_type='" +  rowElements[i].field_type + "' ";
			}

			if(rowElements[i].readonly != undefined)
			{
				genHTML += "readonly ";
			}

			if(rowElements[i].value_val != undefined)
			{
				genHTML += "actual_value= \"" + rowElements[i].value_val + "\"/>";

				if(rowElements[i].field_type == 'masked_value')
				{
					valsToAdd.push({
										"id":rowElements[i].id,
										"value":rowElements[i].value_val,
										"show_value": rowElements[i].value_html
									});
				}
				else
				{
					valsToAdd.push({
										"id":rowElements[i].id,
										"value":rowElements[i].value_val
									});
				}

				if(rowElements[i].original_field_type == 'autocomplete_single')
				{
					$('#' + rowElements[i].orig_id + '-autocomplete-single-holder').find('.labori_autocomplete_single_holder_item').remove();
					$('#' + rowElements[i].orig_id).val('');
					$('#' + rowElements[i].orig_id).show();
				}
				else
				{
					$('#' + rowElements[i].orig_id).val('');
				}
			}
			else if(rowElements[i].value_attr != undefined)
			{
				genHTML += "value= \"" + rowElements[i].value_attr + "\"";
				genHTML += "/>";
				$('#' + rowElements[i].orig_id).val('');		
			}	
			else if(rowElements[i].value_html != undefined)
			{
				genHTML += ">" + rowElements[i].value_html + "</input>";
				$('#' + rowElements[i].orig_id).html('');
			}		
		}

		genHTML += '</div>';
		genHTML += "</td>";

	}

	if($('#' + oldSaveBtnId).attr('labori_dont_disable') === undefined)
	{
		$('#' + oldSaveBtnId).addClass('disabled');
	}

	genHTML += "<td style='text-align:center;'>";
	genHTML += '<div class="labori_tooltip labori_button_round red_button ' + tempID + '_vgid"	onclick="labori_prepareYesNoDialog(' +
														 	"&quot;<i class='fa fa-warning' aria-hidden='true'></i> Confirm Deletion&quot;," +
														 	"&quot;Are you sure you want to delete this <span class='imp_text_red'>data</span>?<br><br>(Note that changes will not be committed until you hit <span class='imp_text_green'>Save</span>)&quot;," +
														 	'&quot;$(\'#new_row_' + tempID + '\').remove(); labori_toggleTableRequirement(\'' + parentTable + '\');&quot;)">';
	genHTML += '<i class="fa fa-close" aria-hidden="true"></i><span class="labori_tooltiptext">Delete Row</span>';
	genHTML += '</div>';

	genHTML += "</td>";
	genHTML += "</tr>";

	$('#' + insertDataRowId).before(genHTML);
	labori_toggleTableRequirement(parentTable);

	for(var i = 0; i < valsToAdd.length; i++)
	{
		if(valsToAdd[i].show_value != undefined)
		{
			$('#' + valsToAdd[i].id).val(valsToAdd[i].value);
			$('#' + valsToAdd[i].id + '_shownVal').html(valsToAdd[i].show_value);
		}
		else
		{
			$('#' + valsToAdd[i].id).val(valsToAdd[i].value);
			$('#' + valsToAdd[i].id + '_shownVal').html(valsToAdd[i].value);
		}
	}

	for(var i = 0; i < dateFieldArray.length; i++)
	{
		labori_convertDatePickers("#" + dateFieldArray[i]);
	}
}

/**********************************************************/
/*LOCKING UTILITIES                               	  	  */
/**********************************************************/
function labori_pollResourceLock(resourceType, resourceID, interval)
{
    var result = false;
    requestArgs = {};
    requestArgs["resource_type"] = resourceType;
    requestArgs["resource_id"] = resourceID;

	var requestPayload = {
					"type":"serv", 
					"class":"Service_ResourceLock", 
					"parent_route":"", 
					"action":"request_refreshLock", 
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
			console.log('blah');
			setTimeout(function(){
				labori_pollResourceLock(resourceType, resourceID, interval)
		    }, interval); 
		}
	});
    
}

function labori_setupResourceLockPolling(resourceType, resourceID) 
{
    setTimeout(function()
    {
		labori_pollResourceLock(resourceType, resourceID, 20000)
    }, 20000);
}

/**********************************************************/
/*JOB UTILITIES                               	  	  	  */
/**********************************************************/
function labori_pollJob(jobUUID, jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, progressBar, interval)
{
    var result = false;
    requestArgs = {};
    requestArgs["job_uuid"] = jobUUID;

	var requestPayload = {
					"type":"serv", 
					"class":"Service_Jobs", 
					"parent_route":"", 
					"action":"request_checkJob", 
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
			console.log(data);

			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					var progress = parseFloat(data.response["job_progress"]);

					if(progress >= 100.0)
					{
						result = true;
					}
					else
					{
						result = false;
					}

					$('#' + jobLogHolder).html(data.response["job_log"]);
					$('#' + jobStatusHolder).html(data.response["job_status"]);
					progressBar.animate(progress/100.0);

					if(result) 
			        {
			            toggleVisualGroupVisibility(jobVisibilityGroup);
			        }
			        else
			        {
			            setTimeout(function(){
							labori_pollJob(jobUUID, jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, progressBar, interval)
					    }, interval);
			        }
				}
				else
				{
					var progress = 100;
					$('#' + jobLogHolder).html(data.response["job_log"]);
					$('#' + jobStatusHolder).html(data.response["job_status"]);
					progressBar.animate(progress/100.0);
 					toggleVisualGroupVisibility(jobVisibilityGroup);
				}
			}
			else
			{
				return 0;
			}
		}
	});
    
}

function labori_setupJobPolling(jobUUID, jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, interval) 
{
	$('#' + jobLogHolder).html('Initializing server log.');
	$('#' + jobStatusHolder).html('Staring Job');

	$('#' + jobProgressHolder).html('');

	var progressBar = new ProgressBar.Circle('#' + jobProgressHolder, {
		color: '#F1F1F1',
		// This has to be the same size as the maximum width to
		// prevent clipping
		strokeWidth: 12,
		trailWidth: 6,
		easing: 'easeInOut',
		duration: 1400,
		text: 
		{
			autoStyleContainer: false
		},
		from: 
		{ 
			color: labori_js_color, width: 12
		},
		to: 
		{ 
			color: labori_js_color, width: 12
		},
		
		// Set default step function for all animate calls
		step: function(state, circle) 
		{
			circle.path.setAttribute('stroke', state.color);
			circle.path.setAttribute('stroke-width', state.width);

			var value = Math.round(circle.value() * 100);
			if (value === 0) 
			{
				circle.setText('0');
			} 
			else 
			{
				circle.setText(value);
			}

		}
	});

	progressBar.text.style.fontFamily = '"opensans", Helvetica, sans-serif';
	progressBar.text.style.fontSize = '1rem';

	progressBar.animate(0);  // Number from 0.0 to 1.0
	toggleVisualGroupVisibility(jobVisibilityGroup);
    setTimeout(function()
    {
		labori_pollJob(jobUUID, jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, progressBar, interval)
    }, interval);
}

/**********************************************************/
/*REQUEST UTILITIES                               	  	  */
/**********************************************************/
function labori_setupUploadWithoutJob(fileInputId, buttonTextId, loadingHTML, groupID, optArgs, type, 
									  targetClass, parentRoute, action, callBackFunction, callBackArgs)
{
	$('#' + buttonTextId).html(loadingHTML);
	$('#' + buttonTextId).addClass("disabled");

	var metaFields = labori_getAllMetaFields(groupID);
	var passesAllChecks = true;

	for(var i = 0; i < metaFields.length; i++)
	{
		if(!labori_validate(metaFields[i]))
		{
			passesAllChecks = false;
			break;
		}
	}

	if(passesAllChecks)
	{
		var requestArgs = {};
		requestArgs["button_text_id"] = buttonTextId;

		if(optArgs != null)
		{
			Object.keys(optArgs).forEach(function(key) 
			{
				requestArgs[key] = optArgs[key];	
			});
		}

		for(var i = 0; i < metaFields.length; i++)
		{
			requestArgs[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
		}	

		var requestPayload = {
					"type":type, 
					"class":targetClass, 
					"parent_route":parentRoute, 
					"action":action, 
					"request_key":"TODO", 
					"args": JSON.stringify(requestArgs),
					};

		var formData = new FormData();

		var fileInput = document.getElementById(fileInputId);
		var files = fileInput.files;

		for (var i = 0; i < files.length; i++) 
		{
			var file = files[i];
			formData.append('file', file, file.name);
		}

		formData.append('request_payload', JSON.stringify(requestPayload));

		$.ajax(
		{
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
			cache: false,

			success: function(data)
			{
				var tempFunc = labori_getFunctionFromString(callBackFunction);
				callBackArgs = labori_replaceAll("`", "\"", callBackArgs);
				tempFunc(data, callBackArgs);
				$('#' + buttonTextId).removeClass("disabled");
			}
		});
	}
}
function labori_setupUploadWithJob(jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, fileInputId, 
								   groupID, optArgs, type, targetClass, parentRoute, action, callBackFunction, callBackArgs)
{
	requestArgs = {};
	var requestPayload = {
					"type":"serv", 
					"class":"Service_Jobs", 
					"parent_route":"", 
					"action":"request_createJob", 
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
			console.log(data);

			if(labori_isValidJSON(data))
			{
				data = JSON.parse(data);

				if(data.success)
				{
					var jobUUID = data.response;
					labori_setupJobPolling(jobUUID, jobVisibilityGroup, jobLogHolder, jobStatusHolder, jobProgressHolder, 1000);

					var metaFields = labori_getAllMetaFields(groupID);
					var passesAllChecks = true;

					for(var i = 0; i < metaFields.length; i++)
					{
						if(!labori_validate(metaFields[i]))
						{
							passesAllChecks = false;
							break;
						}
					}

					if(passesAllChecks)
					{
						var requestArgs = {};

						if(optArgs != null)
						{
							Object.keys(optArgs).forEach(function(key) 
							{
								requestArgs[key] = optArgs[key];	
							});
						}

						for(var i = 0; i < metaFields.length; i++)
						{
							requestArgs[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
						}

						requestArgs["job_uuid"] = jobUUID;	

						var requestPayload = {
									"type":type, 
									"class":targetClass, 
									"parent_route":parentRoute, 
									"action":action, 
									"request_key":"TODO", 
									"args": JSON.stringify(requestArgs),
									};

						var formData = new FormData();

						var fileInput = document.getElementById(fileInputId);
						var files = fileInput.files;

						for (var i = 0; i < files.length; i++) 
						{
							var file = files[i];
							formData.append('file', file, file.name);
						}

						formData.append('request_payload', JSON.stringify(requestPayload));

						$.ajax(
						{
							type: "POST",
							data: formData,
							processData: false,
							contentType: false,
							url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
							cache: false,

							success: function(data)
							{
								var tempFunc = labori_getFunctionFromString(callBackFunction);
								callBackArgs = labori_replaceAll("`", "\"", callBackArgs);
								tempFunc(data, callBackArgs);
							}
						});
					}
				}
			}
		}
	});
}

function labori_setupAutoServiceCall(optArgs, type, targetClass, parentRoute, action, callBackFunction, callBackArgs)
{
	var requestArgs = {};

	if(optArgs != null)
	{
		Object.keys(optArgs).forEach(function(key) 
		{
			requestArgs[key] = optArgs[key];	
		});
	}

	var requestPayload = {
				"type":type, 
				"class":targetClass, 
				"parent_route":parentRoute, 
				"action":action, 
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
			var tempFunc = labori_getFunctionFromString(callBackFunction);
			callBackArgs = labori_replaceAll("`", "\"", callBackArgs);
			tempFunc(data, callBackArgs);
		}
	});
}

function labori_setupButton(buttonID, processingText, completeText, groupID, optArgs, type, 
						    targetClass, parentRoute, action, callBackFunction, callBackArgs, 
						    priorCheckFunction, priorCheckCallbackFunction)
{
	var requestArgs = {};
	var stopNormalCallback = false;

	if(priorCheckFunction.trim().length > 0)
	{
		var tempPriorCheck = labori_getFunctionFromString(priorCheckFunction);

		try
		{
			var priorCheckResults = tempPriorCheck();

			if(priorCheckResults === true)
			{
				if(priorCheckCallbackFunction.trim().length > 0)
				{
					var tempPriorCallback = labori_getFunctionFromString(priorCheckCallbackFunction);
					var callbackRetVal = tempPriorCallback();

					if(callbackRetVal === undefined || callbackRetVal === true)
					{
						return;
					}
					else if(typeof callbackRetVal === "object")
					{
						Object.keys(callbackRetVal).forEach(function(key) 
						{
							requestArgs[key] = callbackRetVal[key];	
						});
					}
				}

				stopNormalCallback = true;
			}
		}
		catch(err)
		{
			console.log(err);
			console.log("Prior check function failed.")
		}
	}

	if($('#' + buttonID).hasClass('disabled'))
	{
		return '';
	}

	$('#' + buttonID).addClass('disabled');
	$('#' + buttonID).html(processingText);

	var metaFields = labori_getAllMetaFields(groupID);
	var passesAllChecks = true;

	for(var i = 0; i < metaFields.length; i++)
	{
		if(!labori_validate(metaFields[i]))
		{
			passesAllChecks = false;
			break;
		}
	}

	if(passesAllChecks)
	{
		if(optArgs != null)
		{
			Object.keys(optArgs).forEach(function(key) 
			{
				requestArgs[key] = optArgs[key];	
			});
		}

		for(var i = 0; i < metaFields.length; i++)
		{
			requestArgs[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
		}

		var requestPayload = {
					"type":type, 
					"class":targetClass, 
					"parent_route":parentRoute, 
					"action":action, 
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
				if(!stopNormalCallback)
				{
					var tempFunc = labori_getFunctionFromString(callBackFunction);
					callBackArgs = labori_replaceAll("`", "\"", callBackArgs);
					tempFunc(data, callBackArgs);
				}

				$('#' + buttonID).removeClass('disabled');
				$('#' + buttonID).html(completeText);
			}
		});
	}
	else
	{
		$('#' + buttonID).removeClass('disabled');
		$('#' + buttonID).html(completeText);
	}
}

function labori_setupButtonForFileDownload(buttonID, processingText, completeText, groupID, optArgs, type, 
						    			   targetClass, parentRoute, action, callBackFunction, callBackArgs)
{
	if($('#' + buttonID).hasClass('disabled'))
	{
		return '';
	}

	$('#' + buttonID).addClass('disabled');
	$('#' + buttonID).html(processingText);

	var metaFields = labori_getAllMetaFields(groupID);
	var passesAllChecks = true;

	for(var i = 0; i < metaFields.length; i++)
	{
		if(!labori_validate(metaFields[i]))
		{
			passesAllChecks = false;
			break;
		}
	}

	if(passesAllChecks)
	{
		var formData = new FormData();
		var requestArgs = {};

		if(optArgs != null)
		{
			Object.keys(optArgs).forEach(function(key) 
			{
				requestArgs[key] = optArgs[key];	
			});
		}

		for(var i = 0; i < metaFields.length; i++)
		{
			requestArgs[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
		}

		formData.append("type", type);
		formData.append("class", targetClass);
		formData.append("parent_route", parentRoute);
		formData.append("action", action);
		formData.append("request_key", "TODO");
		formData.append("args", JSON.stringify(requestArgs));

		var request = new XMLHttpRequest();
		request.open('POST', '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php', true);
		request.responseType = 'blob';

		request.onload = function(e) 
		{
		    if (this.status === 200)
	     	{
	     		var filename = "report";
			    var disposition = this.getResponseHeader('Content-Disposition');
			    if (disposition && disposition.indexOf('attachment') !== -1) 
			    {
			        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
			        var matches = filenameRegex.exec(disposition);
			        if (matches != null && matches[1]) 
			        { 
			          	filename = matches[1].replace(/['"]/g, '');
			        }
			    }

		        var blob = this.response;
				if(window.navigator.msSaveOrOpenBlob) 
				{
					window.navigator.msSaveBlob(blob, filename);
				}
				else
				{
					var downloadLink = window.document.createElement('a');
					var contentTypeHeader = request.getResponseHeader("Content-Type");
					downloadLink.href = window.URL.createObjectURL(new Blob([blob], { type: contentTypeHeader }));
					downloadLink.download = filename;
					document.body.appendChild(downloadLink);
					downloadLink.click();
					document.body.removeChild(downloadLink);
	           }

	           $('#' + buttonID).removeClass('disabled');
			   $('#' + buttonID).html(completeText);
	       }
	   };

	   request.send(formData);
	}
	else
	{
		$('#' + buttonID).removeClass('disabled');
		$('#' + buttonID).html(completeText);
	}
}

function labori_setupForm(formID, groupID, optArgs, type, targetClass, parentRoute, action, callBackFunction, callBackArgs)
{
	var form = document.getElementById(formID);

	form.onsubmit = function(event) 
	{
		event.preventDefault();
		var metaFields = labori_getAllMetaFields(groupID);
		var passesAllChecks = true;

		for(var i = 0; i < metaFields.length; i++)
		{
			if(!labori_validate(metaFields[i]))
			{
				passesAllChecks = false;
				break;
			}
		}

		if(passesAllChecks)
		{
			var formData = new FormData();
			var requestArgs = {};

			if(optArgs != null)
			{
				Object.keys(optArgs).forEach(function(key) 
				{
					requestArgs[key] = optArgs[key];	
				});
			}

			for(var i = 0; i < metaFields.length; i++)
			{
				requestArgs[metaFields[i].attr('labori__meta_id')] = labori_extractMetaFieldValue(metaFields[i]);
			}

			formData.append("type", type);
			formData.append("class", targetClass);
			formData.append("parent_route", parentRoute);
			formData.append("action", action);
			formData.append("request_key", "TODO");
			formData.append("args", JSON.stringify(requestArgs));

			var xhr = new XMLHttpRequest();
			xhr.open('POST', '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php', true);
			xhr.send(formData);
			xhr.onload = function () 
			{
				var tempFunc = labori_getFunctionFromString(callBackFunction);
				callBackArgs = labori_replaceAll("`", "\"", callBackArgs);
				tempFunc(xhr, callBackArgs);
			};
		}
	}
}

/**********************************************************/
/*BUILT-IN CALLBACK FUNCTIONS                             */
/**********************************************************/
function labori_callback_debug(responseObj, args)
{
	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj.status === 200) 
		{
			console.log(responseObj.responseText);
		} 
		else 
		{
			console.log('An error occurred!');
		}
	}
	else
	{
		console.log(responseObj);
	}
}

function labori_callback_submitForm(responseObj, args)
{
	console.log(responseObj);
	if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(temp.success)
		{
			toggleVisualGroupVisibility('submission_vg');
		}
	}
}

//Args has to be the ID of a hidden ID field for the returned 
//upload UUID to go into
function labori_callback_formFileUpload(responseObj, args)
{
	if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(temp.success)
		{
			var tempData = temp.results;
			if(tempData.upload_success)
			{
				if(tempData.button_text_id !== undefined && 
				   tempData.file_upload_name !== undefined && 
				   tempData.file_uuid)
				{
					$('#' + tempData.button_text_id).html("Uploaded (" + tempData.file_upload_name + ")");
					$('#' + args).val(tempData.file_uuid);

					if($('#' + args).attr('required') !== undefined && tempData.label_id !== undefined)
					{
						$('#' + tempData.label_id).removeClass('red_button');
					}

					$('#' + args).addClass("labori_field_error");
					var tempWarningID = $('#' + args).attr('validate_warning_id');

					if(tempWarningID != undefined)
					{
						$('#' + tempWarningID).remove();
						$('#' + args).removeAttr('validate_warning_id');
					}

					$('#' + args).removeClass("labori_field_error");

					if(tempData.labori_required_for !== undefined)
					{
						labori_toggleButtonLockWithID(args, tempData.labori_required_for, 'labori_required_for_' + tempData.labori_required_for);
					}
				}
			}
			else
			{
				$('#' + args).val('');

				if(tempData.button_text_id !== undefined && tempData.button_text !== undefined)
				{
					$('#' + tempData.button_text_id).html(tempData.button_text);
					if($('#' + args).attr('required') !== undefined && tempData.label_id !== undefined)
					{
						$('#' + tempData.label_id).addClass('red_button');
					}
				}

				var warningID = labori_generateUniqueID();
				var tempWarningID = $('#' + args).attr('validate_warning_id');
				var errorMsg = "There was something invalid about your file (probably the file type).";

				if(tempData.error_txt !== undefined)
				{
					errorMsg = tempData.error_txt;
				}

				if(tempWarningID == undefined)
				{
					$('#' + args).attr('validate_warning_id', 'validate_warning_' + warningID);
					$('#' + args).before('<div error_message=\"' + errorMsg + '\" onclick=\"labori_changePopupDialog(\'Input Error\', ' + 
									   '$(\'#validate_warning_' + warningID + '\').attr(\'error_message\')' + '); $(\'#labori_popup_overlay\').show();\" ' + 
									   "id='validate_warning_" + warningID + "' class='labori_input_warning'><i class='fa fa-question-circle' aria-hidden='true'></i></div>");
				}
				else
				{
					$('#' + args).attr('error_message', errorMsg);
				}	

				if(tempData.labori_required_for !== undefined)
				{
					labori_toggleButtonLockWithID(args, tempData.labori_required_for, 'labori_required_for_' + tempData.labori_required_for);
				}
			}
		}
		else
		{

		}	
	}
}

function labori_callback_redirectOnSuccess(responseObj, args)
{
	var failure = true;
	var failureMsg = undefined;
	var response = "";
	var customDialog = false;
	var customDialogTitle = "";
	var redirect = '/' + labori_getRootDir();

	if(labori_isValidJSON(args))
	{
		var tempParse = JSON.parse(args);

		if(tempParse.redirect !== undefined)
		{
			redirect = tempParse.redirect;
		}
	}

	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj.status === 200 && labori_isValidJSON(responseObj.responseText)) 
		{
			var temp = JSON.parse(responseObj.responseText);

			if(temp.success)
			{
				labori_cancelDataCheck = true;
				window.location = redirect;
				failure = false;
			}
		} 
	}
	else if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(temp.success)
		{
			labori_cancelDataCheck = true;
			window.location = redirect;
			failure = false;
		}
		else
		{
			failureMsg = temp["response"];

			if(temp.custom_dialog !== undefined && temp.custom_dialog === true)
			{
				customDialog = true;

				if(temp.custom_title !== undefined)
				{
					customDialogTitle = temp.custom_title;
				}
			}
		}	
	}

	if(failure)
	{	
		if(failureMsg == undefined)
		{
			labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', 
								 "An error occurred while executing a site request and was logged." + 
								 "<br><br><span class='imp_text_red'>The error may have prevented data from being saved.</span>");	
			console.log(responseObj);
		}
		else
		{
			if(customDialog)
			{
				labori_changePopupDialogCustom(customDialogTitle, failureMsg);
				labori_showPopup();
			}
			else
			{
				labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', failureMsg);
			}
		}
	}
}

function labori_callback_reloadOnSuccessRetMessage(responseObj, args)
{
	var failure = true;
	var failureMsg = undefined;
	var response = "";
	var customDialog = false;
	var customDialogTitle = "";

	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(responseObj.status === 200 && labori_isValidJSON(responseObj.responseText)) 
		{
			var temp = JSON.parse(responseObj.responseText);

			if(temp.success)
			{
				labori_cancelDataCheck = true;
				window.location = JSON.parse(args)["redirect"];
				failure = false;
			}
			else
			{
				failureMsg = temp["response"];
			}
		} 
	}
	else if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		console.log(temp);

		if(temp.success)
		{
			labori_cancelDataCheck = true;
			window.location = JSON.parse(args)["redirect"];
			failure = false;
		}
		else
		{
			failureMsg = temp["response"];

			if(temp.custom_dialog !== undefined && temp.custom_dialog === true)
			{
				customDialog = true;

				if(temp.custom_title !== undefined)
				{
					customDialogTitle = temp.custom_title;
				}
			}
		}	
	}

	if(failure)
	{	
		if(failureMsg == undefined)
		{
			labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', 
								 "An error occurred while executing a site request and was logged." + 
								 "<br><br><span class='imp_text_red'>The error may have prevented data from being saved.</span>");	
		}
		else
		{
			if(customDialog)
			{
				labori_changePopupDialogCustom(customDialogTitle, failureMsg);
				labori_showPopup();
			}
			else
			{
				labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', failureMsg);
			}
		}
	}
}

function labori_callback_formRowInsert(responseObj, args)
{
	if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(temp.success)
		{
			if(args.trim().length == 0)
			{
				$('#new_question_name').val('');
				$('#new_question_id').val('');
				$('#new_question_text').html('');
				$('#new_question_text').val('');
				$('#new_question_script').val('');
				$('#settings_group').html('');

				labori_cache_holder.labori_js_editors[$('#new_question_script').attr('labori_attached_js_editor_id')].setValue('');

				labori_selectDropdownOpt('new_field_type', 'boiler_plate');
				var firstRow = $('[starting_row="true"]');
				firstRow.before(temp.results);
			}
			else
			{
				var row = $('#' + args);
				row.before(temp.results);
				$('#' + args).remove();
			}
		}	
	}
}

function labori_callback_reloadOnSuccess(responseObj, args)
{
	var failure = true;
	var response = "";

	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(responseObj.status === 200 && labori_isValidJSON(responseObj.responseText)) 
		{
			var temp = JSON.parse(responseObj.responseText);

			if(temp.success)
			{
				labori_cancelDataCheck = true;
				window.location = window.location;
				failure = false;
			}
		} 
	}
	else if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(temp.success)
		{
			labori_cancelDataCheck = true;
			window.location = window.location;
			failure = false;
		}	
	}

	if(failure)
	{
		labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', 
							 "An error occurred while executing a site request and was logged." + 
							 "<br><br><span class='imp_text_red'>The error may have prevented data from being saved.</span>");
	}
}

function labori_callback_simpleReload(responseObj, args)
{
	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj.status === 200) 
		{
			labori_cancelDataCheck = true;
			window.location = './' + window.location.search;
		} 
		else 
		{
			console.log('An error occurred!');
		}
	}
	else
	{
		labori_cancelDataCheck = true;
		window.location = './' + window.location.search;
	}
}

function labori_callback_replaceHTML(responseObj, args)
{
	var failure = true;
	var response = "";


	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(responseObj.status === 200 && labori_isValidJSON(responseObj.responseText)) 
		{
			var temp = JSON.parse(responseObj.responseText);

			if(temp.success)
			{
				$(args).html(temp.response);
				failure = false;
			}
		} 
	}
	else if(labori_isValidJSON(responseObj))
	{
		var temp = JSON.parse(responseObj);

		if(responseObj != null)
		{
			response = responseObj.responseText;
		}

		if(temp.success)
		{
			$(args).html(temp.response);
			failure = false;
		}	
	}

	if(failure)
	{
		labori_prepareDialog('<i class="fa fa-warning" aria-hidden="true"></i> Site Request Error', 
							 "An error occurred while executing a site request and was logged." + 
							 "<br><br><span class='imp_text_red'>The error may have prevented data from being saved.</span>");
	}
}

function labori_callback_simpleRedirect(responseObj, args)
{
	if(responseObj instanceof XMLHttpRequest)
	{
		if(responseObj.status === 200) 
		{
			labori_cancelDataCheck = true;
			window.location = '/' + labori_getRootDir() + '/' + JSON.parse(args)["redirect"];
		} 
		else 
		{
			console.log('An error occurred!');
		}
	}
	else
	{
		labori_cancelDataCheck = true;
		window.location = '/' + labori_getRootDir() + '/' + JSON.parse(args)["redirect"];
	}
}

/**********************************************************/
/*PAGE LOAD SCRIPTS                           		 	  */
/**********************************************************/
function labori_convertToJSEditor(selector, addToCache)
{
	var elements = $(selector);

	for(var i = 0; i < elements.length; i++)
	{
		var tempEditor = CodeMirror.fromTextArea($(elements[i]).get()[0], 
		{
			lineNumbers: true,
			indentWithTabs: true,
			indentUnit: 4,
			cursorHeight: 0.85,
			lineWrapping: true
		});

		tempEditor.on("change", function(inst, changeObj){
			inst.save();
		});

		if(addToCache)
		{
			if(labori_cache_holder.labori_js_editors === undefined)
			{
				labori_cache_holder.labori_js_editors = {};
			}

			var tempID = labori_generateUniqueID();
			labori_cache_holder.labori_js_editors[tempID] = tempEditor;
			$(elements[i]).attr('labori_attached_js_editor_id', tempID);
		}
	}
}

function labori_convertDatePickers(selector)
{
	flatpickr(selector, {});
	var elements = $(selector);

	for(var i = 0; i < elements.length; i++)
	{
		if($(elements[i]).attr('required') !== undefined)
		{
			$(elements[i]).addClass('labori_required_input_override');
		}

		$(elements[i]).on('input', function(){
			if($(this).val().trim().length <= 0)
			{
				$(this).addClass('labori_required_input_override');
			}
			else
			{
				$(this).removeClass('labori_required_input_override');
			}
		});

		$(elements[i]).blur(function(){
			if($(this).val().trim().length <= 0)
			{
				$(this).addClass('labori_required_input_override');
			}
			else
			{
				$(this).removeClass('labori_required_input_override');
			}
		});
	}
}

function labori_convertDatepickerToTextEntry(dateID, validationRequiredFor)
{
	$('#' + dateID).attr('readonly', false);
	$('#' + dateID).attr('validation_type', 'text_input');
	$('#' + dateID).attr('validate_regex', '/^(((0?[1-9]|1[0-2])\\/(0?[1-9]|1\\d|2\\d|3[01])\\/(19|20)\\d{2})|([0-9]{4}-[0-9]?[0-9]-[0-9]?[0-9]))$/');
	$('#' + dateID).attr('validate_date_format', 'MM/DD/YYYY');
	$('#' + dateID).attr('validate_custom_error', 'Date entered must match mm/dd/yyyy format and must be a valid date.');
	$('#' + dateID).addClass('labori_validation_required_for__' + validationRequiredFor);
}

function labori_convertDropdowns(selector)
{
	$(selector).each(function(){
		var $this = $(this), numberOfOptions = $(this).children('option').length;

		var isRequired = false;
		if($this.attr('labori_required_for') !== undefined)
		{
			isRequired = true;
		}

		$this.addClass('labori_select-hidden'); 
		
		if(isRequired)
		{
			$this.wrap('<div class="labori_select labori_required_input_override"></div>');
		}
		else
		{
			$this.wrap('<div class="labori_select"></div>');
		}
		

		$this.after('<div class="labori_select-styled"></div>');
		$this.parent().attr('style', $this.attr('style'));


		var $styledSelect = $this.next('div.labori_select-styled');
		
		if($this.attr("labori_multi_select") === undefined)
		{
			$styledSelect.text($this.children('option').eq(0).text());
		}
		else
		{
			$styledSelect.text("Please Select One or More");
		}

		var $list = $('<ul />', {
						'class': 'labori_select-options'
					}).insertAfter($styledSelect);

		for (var i = 0; i < numberOfOptions; i++) 
		{
			var tempObj = {
				text: $this.children('option').eq(i).text(),
				rel: $this.children('option').eq(i).val(),
				onclick: $this.children('option').eq(i).attr("onclick")
			};

			if($this.children('option').eq(i).attr("other_field_id") !== undefined)
			{
				tempObj.other_field_id = $this.children('option').eq(i).attr("other_field_id").trim();
			}

			$('<li />', tempObj).appendTo($list);
		}

		var $listItems = $list.children('li');

		$styledSelect.click(function(e) {
			e.stopPropagation();
			$('div.labori_select-styled.active').not(this).each(function(){
				$(this).removeClass('active').next('ul.labori_select-options').hide();
			});

			$(this).toggleClass('active').next('ul.labori_select-options').toggle();

			if($(this).parent().find('ul').css('display') != 'none')
			{
				/*$(this).parent().find('ul').css("top", "");
				var heightAdjustment = $(this).parent().find('ul').outerHeight() - $(this).parent().find('ul :last-child').outerHeight();
				var viewportHeight = $(window).height() - $(window).scrollTop();

				//if(heightAdjustment + $(this).parent().find('ul').offset().top + 30 - $(window).scrollTop() > viewportHeight)
				if(false)
				{

					$(this).parent().find('ul').css("top", "-" + heightAdjustment + "px");
				}
				else
				{
					$(this).parent().find('ul').css("top", "");
				}
				*/
			}

			$listItems.each(function(){
				if($(this).attr('rel') == "REMOVE___ME")
				{
					$(this).remove();
				}
			});
		});

		if($this.attr("labori_multi_select") === undefined)
		{
			$listItems.click(function(e) {
				e.stopPropagation();
				$styledSelect.text($(this).text()).removeClass('active');
				$this.val($(this).attr('rel'));
				$list.hide();
			});
		}
		else
		{
			$listItems.click(function(e) {
				e.stopPropagation();
				$styledSelect.text("Please Select One or More").removeClass('active');
				$this.val('');
				$list.hide();
			});
		}

		$(document).click(function() {
			$styledSelect.removeClass('active');
			$list.hide();
		});
	});
}

function labori_makeTableSortable(tableID)
{
	$(document).ready(function() 
	{
		var tempHtml = $("#" + tableID).find('.labori_table_header_title').html();
		if(tempHtml !== undefined)
		{
			$("#" + tableID).find('.labori_table_header_title').remove();
			var tempStyle = $("#" + tableID).attr("style");
			$("#" + tableID).attr("style", "");
			$("#" + tableID).before('<table class="labori_table" style="' + tempStyle + '"><tr sortable=\'false\' class="labori_table_header labori_table_header_title">' + tempHtml + '</tr></table>');
		}

		var tempHtml = $("#" + tableID).find('tr').not('.labori_table_header_title').eq(0).html();
		var tempHtml =$("#" + tableID).find('tr').not('.labori_table_header_title').eq(0).remove();
		$("#" + tableID).prepend("<thead></thead>");
		$("#" + tableID + " thead").html(tempHtml);
  		$("#" + tableID).tablesorter();
	});
}

$(document).ready(function() 
{
	$(document).click(function(){
		$('.labori_tooltip').removeClass('labori_tooltip_shown');
	});

	labori_convertDatePickers(".labori_date_field");
	labori_convertDropdowns("select");
	labori_groupSliders();
	labori_convertToJSEditor(".labori_js_editors", true);

	$($('.labori_wysiwyg').parent()).addClass('trumbowyg-dark');
 	$('.labori_wysiwyg').trumbowyg();


	$('#labori_core_sidebarCap_container').mCustomScrollbar({
				theme:"minimal"
			});

	labori_checkAllFields();
});

