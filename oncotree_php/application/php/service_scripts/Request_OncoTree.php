<?php
	require_once dirname(__FILE__) . '/../../../labori_core/native/php/Labori_Core.php';

	class Request_OncoTree extends Request_Handler 
	{
		public function methodIsService($methodName)
		{
			if(Labori_Utl::streql($methodName, "request_uploadDxFile"))
			{
				return true;
			}
			else if(Labori_Utl::streql($methodName, "request_exportToExcel"))
			{
				return true;
			}

			return false;
		}

		public static function getUMLSExcelColumns()
		{
			$columns = array(
							array(
								"column_name" => "Original DX",
								"formatting" => array(
									"column_width" => 40,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "UMLS Name",
								"formatting" => array(
									"column_width" => 40,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),
							
							array(
								"column_name" => "UMLS Identifer",
								"formatting" => array(
									"column_width" => 10,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "UMLS Root Source",
								"formatting" => array(
									"column_width" => 10,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "UMLS URI",
								"formatting" => array(
									"column_width" => 40,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Name",
								"formatting" => array(
									"column_width" => 40,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Code",
								"formatting" => array(
									"column_width" => 10,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Color",
								"formatting" => array(
									"column_width" => 10,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Tissue",
								"formatting" => array(
									"column_width" => 20,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Tissue Level",
								"formatting" => array(
									"column_width" => 10,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Main Tumor Type",
								"formatting" => array(
									"column_width" => 40,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),

							array(
								"column_name" => "OncoTree Other References",
								"formatting" => array(
									"column_width" => 30,
									"alignment" => array(
										"wrap_text" => true
									)
								)
							),
						);


			return $columns;
		}

		public static function getOncotreeElementByCUI($cui)
		{
			$url = 'http://oncotree.mskcc.org/api/tumorTypes/search/umls/' . $cui . '?exactMatch=true&levels=2%2C3%2C4%2C5';
			$fields = [];
			$fields_string = http_build_query($fields);

			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			curl_close($ch);

			$parsedResult = json_decode($result, true);
        	$retArray = array();

        	if($parsedResult !== false)
        	{
        		if(isset($parsedResult["status"]) && 
        		   ($parsedResult["status"] == "404" || $parsedResult["status"] == "400" || $parsedResult["status"] == "503"))
        		{
        			return false;
        		}

        		foreach($parsedResult as $thisResult)
        		{
        			$tempEntry = array(
        				"code" => "",
        				"color" => "",
        				"name" => "",
        				"main_type" => "",
        				"tissue" => "",
        				"level" => "",
        				"references" => array()
        			);

        			if(isset($thisResult["code"]))
        			{
        				$tempEntry["code"] = $thisResult["code"];
        			}

        			if(isset($thisResult["color"]))
        			{
        				$tempEntry["color"] = $thisResult["color"];
        			}

        			if(isset($thisResult["name"]))
        			{
        				$tempEntry["name"] = $thisResult["name"];
        			}

        			if(isset($thisResult["mainType"]))
        			{
        				$tempEntry["main_type"] = $thisResult["mainType"];
        			}

        			if(isset($thisResult["tissue"]))
        			{
        				$tempEntry["tissue"] = $thisResult["tissue"];
        			}

        			if(isset($thisResult["level"]))
        			{
        				$tempEntry["level"] = $thisResult["level"];
        			}

        			if(isset($thisResult["externalReferences"]))
        			{
        				foreach($thisResult["externalReferences"] as $thisRefType => $thisReference)
        				{
        					foreach($thisReference as $thisInnerReference)
        					{
        						$tempEntry["references"][$thisRefType] = $thisInnerReference;
        					}
        				}
        			}

        			$retArray = $tempEntry;
        		}
        	}

        	return $retArray;
		}

		public static function getUMLSTicketGrantingTicket()
		{
			$url = 'https://utslogin.nlm.nih.gov/cas/v1/api-key';

			$fields = [
			    "apikey"	=> Labori_Core::getDeploymentOption("UMLS_API_KEY", true),
			];

			$fields_string = http_build_query($fields);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			curl_close($ch);

			$retArray = array();

			preg_match ('/<form action="(.*?)" method="POST">/i', 
			    $result, 
			    $retArray);
			
			if(count($retArray) >= 2)
			{
				return trim($retArray[1]);
			}
			else
			{
				return null;
			}
		}

		public static function getUMLSServiceTicket($ticketGrantingTicket, $requestedService = 'http://umlsks.nlm.nih.gov')
		{
			if(is_null($ticketGrantingTicket))
			{
				return null;
			}

			$url = $ticketGrantingTicket;

			$fields = [
			    "service"	=> $requestedService,
			];

			$fields_string = http_build_query($fields);

			$ch = curl_init();

			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		}

		public static function queryUMLSViaDXText($umlsServiceTicket, $dxText)
		{
			if(is_null($umlsServiceTicket))
			{
				return null;
			}

			$dxText = preg_replace('/\([^)]+\)/', '', $dxText);
			$dxText = str_replace(' ', '|', trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9 \\-]+/i', '', $dxText))));

			$url = "https://uts-ws.nlm.nih.gov/rest/search/current";

			$fields = [
				"string" => $dxText,
				"searchType" => "words",
				"ticket" => $umlsServiceTicket
			];

			$url .= "?" . http_build_query($fields);

			$ch = curl_init();
        	curl_setopt($ch,CURLOPT_URL, $url);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	$output = curl_exec($ch);

        	curl_close($ch);
        	
        	$parsedResult = json_decode($output, true);
        	$retArray = array();

        	if($parsedResult !== false && 
        	   isset($parsedResult["pageSize"]) && isset($parsedResult["pageNumber"]) && isset($parsedResult["result"]) &&
        	   isset($parsedResult["result"]["results"]))
        	{
        		foreach($parsedResult["result"]["results"] as $thisResult)
        		{
        			$tempEntry = array(
        				"original_dx" => $dxText,
        				"cui" => "",
        				"root_source" => "",
        				"uri" => "",
        				"name" => ""
        			);

        			if(isset($thisResult["ui"]))
        			{
        				if($thisResult["ui"] == "NONE")
        				{
        					break;
        				}
        				else
        				{
        					$tempEntry["cui"] = $thisResult["ui"];
        				}
        			}
        			else
        			{
        				break;
        			}

        			if(isset($thisResult["rootSource"]))
        			{
        				$tempEntry["root_source"] = $thisResult["rootSource"];
        			}

        			if(isset($thisResult["uri"]))
        			{
        				$tempEntry["uri"] = $thisResult["uri"];
        			}

        			if(isset($thisResult["name"]))
        			{
        				$tempEntry["name"] = $thisResult["name"];
        			}

        			$retArray[$thisResult["ui"]] = $tempEntry;
        		}
        	}

        	return $retArray;
		}

		public function parseDXFile($tempFileName)
		{
			$fileContents = Labori_File::readExcelFile($tempFileName);
			$tgt = Request_OncoTree::getUMLSTicketGrantingTicket();

			$umlsRows = array();
		
			foreach($fileContents as $thisSheet)
			{
				foreach($thisSheet as $thisRow)
				{

					$st = Request_OncoTree::getUMLSServiceTicket($tgt);
					$umlsConcepts = Request_OncoTree::queryUMLSViaDXText($st, $thisRow["A"]);
					$removalKeys = array();

					foreach($umlsConcepts as $thisCUI => $thisConcept)
					{
						$tempOncotree = Request_OncoTree::getOncotreeElementByCUI($thisCUI);

						if($tempOncotree !== false)
						{
							$umlsConcepts[$thisCUI]["oncotree"] = $tempOncotree;
						}
						else
						{
							$removalKeys[$thisCUI] = $thisCUI;
						}
					}

					foreach($removalKeys as $thisCUI)
					{
						unset($umlsConcepts[$thisCUI]);
					}

					if(!empty($umlsConcepts))
					{
						$first = true;
						foreach($umlsConcepts as $thisConcept)
						{
							$tempName = "";
							$tempReferences = "";

							if($first)
							{
								$first = false;
								$tempName = $thisRow["A"];
							}

							foreach($thisConcept["oncotree"]["references"] as $key => $value)
							{
								$tempReferences .= $key . ":" . $value . "\n";
							}

							$umlsRows[] = array(
							$tempName,
							$thisConcept["name"],
							$thisConcept["cui"],
							$thisConcept["root_source"],
							$thisConcept["uri"],
							$thisConcept["oncotree"]["name"],
							$thisConcept["oncotree"]["code"],
							$thisConcept["oncotree"]["color"],
							$thisConcept["oncotree"]["tissue"],
							$thisConcept["oncotree"]["level"],
							$thisConcept["oncotree"]["main_type"],
							$tempReferences,
						);
						}	
					}
					else
					{
						$umlsRows[] = array(
							$thisRow["A"],
							"No Valid Results",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
						);
					}
				}
			}

			return array('umls' => $umlsRows, 'noble' => array());
		}

		public function request_exportToExcel($args)
		{
			if(isset($_SESSION["parse_data"]) && isset($args["export_file_name"]))
			{
				$excelData[] = array(
						"name" => "UMLS Encoding",
						"data" => array(
							"columns" => Request_OncoTree::getUMLSExcelColumns(),
							"rows" => $_SESSION["parse_data"]["umls"]
						)
					);

				Labori_File::writeExcelFile($args["export_file_name"], $excelData);
			}
		}

		public function request_uploadDxFile($args)
		{
			if(isset($args["uploaded_files"]))
			{
				if(is_array($args["uploaded_files"]) && count($args["uploaded_files"]) >= 1)
				{
					$args["uploaded_files"] = $args["uploaded_files"][0];
				}

				if(isset($args["uploaded_files"]["name"]) && 
				   isset($args["uploaded_files"]["type"]) &&
				   isset($args["uploaded_files"]["tmp_name"]))
				{
					$retArray = array();
					$retArray["file_uuid"] = Labori_Utl::generateUUID_urlSafe();
					$retArray["file_upload_type"] = pathinfo(trim($args["uploaded_files"]["name"]), PATHINFO_EXTENSION);
					$retArray["file_upload_name"] = trim($args["uploaded_files"]["name"]);

					if(isset($args["accepted_file_extensions"]) && !empty(trim($args["accepted_file_extensions"])))
					{
						$tempAcceptedExtensions = Labori_Utl::parseDelimitedStr("~", $args["accepted_file_extensions"]);
						$canContinue = false;
						$acceptedFormatsStr = "";

						foreach($tempAcceptedExtensions as $thisExtension)
						{
							$acceptedFormatsStr .= $thisExtension . "<br>";

							if(Labori_Utl::streql($thisExtension, $retArray["file_upload_type"]))
							{
								$canContinue = true;
							}
						}

						if(!$canContinue)
						{
							if(isset($args["label_id"]))
							{
								$retArray["label_id"] = trim($args["label_id"]);
							}

							if(isset($args["labori_required_for"]))
							{
								$retArray["labori_required_for"] = trim($args["labori_required_for"]);
							}

							if(isset($args["button_text_id"]))
							{	
								$retArray["button_text_id"] = trim($args["button_text_id"]);
								$retArray["button_text"] = "Upload Aborted";
								$retArray["error_txt"] = "Only these file extensions are accepted: <br>" . $acceptedFormatsStr;
							}

							$retArray["upload_success"] = false;
							return json_encode(array("success"=> true, "results" => $retArray));
						}
					}

					$data = $this->parseDXFile($args["uploaded_files"]["tmp_name"]);
					$_SESSION["parse_data"] = $data;

					if(isset($args["button_text_id"]))
					{
						$retArray["button_text_id"] = trim($args["button_text_id"]);
					}

					if(isset($args["labori_required_for"]))
					{
						$retArray["labori_required_for"] = trim($args["labori_required_for"]);
					}

					if(isset($args["label_id"]))
					{
						$retArray["label_id"] = trim($args["label_id"]);
					}

					$retArray["upload_success"] = true;
					return json_encode(array("success"=> true, "results" => $retArray));
				}
			}

			return json_encode(array("success"=> false, "results" => "Upload has failed."));
		}
	}

	/*$dxText = "NOS";

	$tgt = OncoTree::getUMLSTicketGrantingTicket();
	$st = OncoTree::getUMLSServiceTicket($tgt);
	OncoTree::queryUMLSViaDXText($st, $dxText);*/
?>