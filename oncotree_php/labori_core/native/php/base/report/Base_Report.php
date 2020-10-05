<?php
	require_once dirname(__FILE__) . '/../../Labori_Core.php';

	abstract class Base_Report extends Request_Handler
	{
		private $canExport_HTML = false;
		private $canExport_Excel = false;
		private $canExport_Word = false;
		private $canExport_PDF = false;
		private $canExport_CSV = false;
		private $reportName = "UNDEFINED";
		private $reportDescription = "UNDEFINED";
		private $possibleConditions = array();
		private $restrictToInstanceTypes = array();

		protected abstract function getCustomPermissionList(&$permissionList);

		protected function export_HTML($args)		{echo "Can't perform this export";}
		protected function preExport_Excel($args)	{echo "Can't perform this export";}
		protected function preExport_Word($args)	{echo "Can't perform this export";}
		protected function preExport_PDF($args)		{echo "Can't perform this export";}
		protected function preExport_CSV($args)		{echo "Can't perform this export";}
		protected function requestArgCheck($args)	{return true;}

		public function methodIsService($methodName)
		{
			if(Labori_Utl::streql($methodName, "request_generateReport"))
			{
				return true;
			}
		}

		public function setExports($html, $excel, $word, $pdf, $csv)
		{
			$this->canExport_HTML = $html;
			$this->canExport_Excel = $excel;
			$this->canExport_Word = $word;
			$this->canExport_PDF = $pdf;
			$this->canExport_CSV = $csv;
		}

		public function setRestrictedToInstanceTypes($typesArray)
		{
			$this->restrictToInstanceTypes = $typesArray;
		}

		public function setReportName($reportName)
		{
			$this->reportName = $reportName;
		}

		public function setReportDescription($reportDescription)
		{
			$this->reportDescription = $reportDescription;
		}

		public function addCondition($newCondition)
		{
			$tempID = $newCondition->getConditionID();
			$this->possibleConditions[$tempID] = $newCondition;
		}

		public function getObjectAsAssociativeArray()
		{
			$convertedConditionArray = array();

			foreach($this->possibleConditions as $thisConditionID => $thisCondition)
			{
				$convertedConditionArray[$thisConditionID] = $thisCondition->getObjectAsAssociativeArray();
			}

			return array(
				"possible_conditions" => $convertedConditionArray,
				"report_name" => $this->reportName,
				"report_description" => $this->reportDescription,
				"can_export_html" => $this->canExport_HTML,
				"can_export_excel" => $this->canExport_Excel,
				"can_export_word" => $this->canExport_Word,
				"can_export_pdf" => $this->canExport_PDF,
				"can_export_cvs" => $this->canExport_PDF,
			);
		}

		public function getExports()
		{
			return array(
				"html" => $this->canExport_HTML,
				"excel" => $this->canExport_Excel,
				"word" => $this->canExport_Word,
				"pdf" => $this->canExport_PDF,
				"csv" => $this->canExport_CSV
			);
		}

		public function getReportName()
		{
			return $this->reportName;
		}

		public function getReportDescription()
		{
			return $this->reportDescription;
		}

		public function getPossibleConditions()
		{
			return $this->possibleConditions;
		}

		public function generateExcelSummaryBlock($titleMap, $allNameValuePairArray = array())
		{
			foreach($allNameValuePairArray as $thisNVPKey =>  $thisNameValuePairArray)
			{
				uasort($thisNameValuePairArray, function($a, $b) { 
					if($a < $b)
					{
				    	return 1;
					}
					else if($a > $b)
					{
						return -1;
					}
					else
					{
						return -1;
					}
				});

				$allNameValuePairArray[$thisNVPKey] = $thisNameValuePairArray;
			}

			$summaryArray = array();
			$formattingArray = array();
			$i = 0;
			
			foreach($allNameValuePairArray as $thisNVPKey =>  $thisNameValuePairArray)
			{
				$summaryArray[] = array($titleMap[$thisNVPKey], "");
				$formattingArray[$i] = array("fill" => "000000", "font" => array("bold" => true), 
																 "border" => array("left" => array("type" => "none"),
																			       "right" => array("type" => "none")));
				$i++;

				foreach($thisNameValuePairArray as $thisName => $thisValue)
				{
					$summaryArray[] = array($thisName, $thisValue);
					$i++;	
				}	

				$summaryArray[] = array("", "");
				$formattingArray[$i] = array("fill" => "FFFFFF", "border" => array("top"=> array("type"=> "thin"), 
																				  "right" => array("type" => "none"), 
																				  "left" => array("type" => "none")));
				$i++;
			}

			return array(
					"columns" => array(array("column_name" => "", "formatting" => array(
																	"column_width" => 100,
																	"alignment" => array(
																		"wrap_text" => true
																	)
																)), 
										array("column_name" => "")),
					"no_headers" => true,
					"rows" => $summaryArray,
					"row_formatting" => $formattingArray
				);
		}

		public function generateSummaryBlock($title, $nameValuePairs = array())
		{
			uasort($nameValuePairs, function($a, $b) { 
				if($a < $b)
				{
			    	return 1;
				}
				else if($a > $b)
				{
					return -1;
				}
				else
				{
					return -1;
				}
			});

			$rowContent = '';

			foreach($nameValuePairs as $thisName => $thisValue)
			{
				$rowContent .= '<div class="labori_summary_detail_field group">
										<div class="labori_summary_detail_field_name">' . $thisName . ':</div>
										<div class="labori_summary_detail_field_value">' . $thisValue . '</div>
									</div>';
			}

			$retVal = '<div class="labori_summary_details_tabbed group">
							<div style="margin:10px;">
								<div class="labori_summary_details_title">' . $title . '</div>
								<div class="laborisummary_detail_row_container group">
								' . $rowContent . '
								</div>
							</div>
						</div>';

			return $retVal;
		}

		public function getIsRestrictedByInstanceTypes()
		{
			if(!empty($this->restrictToInstanceTypes))
			{
				foreach($this->restrictToInstanceTypes as $thisInstanceType)
				{
					if(Labori_Utl::streql($thisInstanceType, Instance_Settings::INSTANCE_TYPE))
					{
						return false;
					}
				}

				return true;
			}

			return false;
		}

		/**********************************************************/
		/*REQUEST SERVICES                                        */
		/**********************************************************/
		public function request_generateReport($args)
		{
			if(array_key_exists("export_type", $args) &&
			   array_key_exists("export_file_name", $args) &&
			   array_key_exists("query_uuid", $args) &&
			   $this->requestArgCheck($args))
			{
				if($this->canExport_HTML && Labori_Utl::streql($args["export_type"], "html"))
				{
					$this->export_HTML($args);
				}
				else if($this->canExport_Excel && Labori_Utl::streql($args["export_type"], "excel"))
				{
					Labori_File::writeExcelFile($args["export_file_name"], $this->preExport_Excel($args));
				}
				else if($this->canExport_PDF && Labori_Utl::streql($args["export_type"], "pdf"))
				{
					$this->preExport_PDF($args);
				}
				else if($this->canExport_Word && Labori_Utl::streql($args["export_type"], "word"))
				{
					Labori_File::writeWordFile($args["export_file_name"], $this->preExport_Word($args));
				}
				else if($this->canExport_CSV && Labori_Utl::streql($args["export_type"], "csv"))
				{
					Labori_File::writeCSVFile($args["export_file_name"], $this->preExport_CSV($args));
				}
			}
			else
			{
				echo "Missing required arguments";
			}
		}
	}
?>