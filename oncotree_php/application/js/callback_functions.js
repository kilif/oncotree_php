function application_uploadCallback(responseObj, args)
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
				$('#import_holder').hide();
				$('#reset_holder').show();
				$('#export_holder').show();

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