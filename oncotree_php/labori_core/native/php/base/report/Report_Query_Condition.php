<?php
	require_once dirname(__FILE__) . '/../../Labori_Core.php';

	class Report_Query_Condition
	{
		const TYPE_UNDEFINED = 'undefined';
		const TYPE_SPECIAL = 'special';
		const TYPE_TEXT = 'text';
		const TYPE_NUMBER = 'number';
		const TYPE_DATE = 'date';
		const TYPE_DROPDOWN = 'dropdown';
		const TYPE_MULTI_DROPDOWN = 'multi_dropdown';
		const TYPE_MULTI_TEXT = 'multi_text';

		private $tableNameHumanReadable = "UNDEFINED";
		private $conditionName = "UNDEFINED";
		private $conditionType = "UNDEFINED"; 	
		private $conditionTable = "UNDEFINED";	
		private $conditionColumn = "UNDEFINED";	
		private $canBeHistoric = false;			
		private $conditionValues = array();		

		function __construct($conditionName, $conditionType, $tableNameHumanReadable, $conditionTable, 
							 $conditionColumn, $conditionValues = array(), $canBeHistoric = false)
		{
			$this->tableNameHumanReadable = $tableNameHumanReadable;
			$this->conditionName = $conditionName;
			$this->conditionType = $conditionType;
			$this->conditionTable = $conditionTable;
			$this->conditionColumn = $conditionColumn;
			$this->conditionValues = $conditionValues;
			$this->canBeHistoric = $canBeHistoric;
		}

		public function getObjectAsAssociativeArray()
		{
			return array(
				"table_name_human_readable" => $this->tableNameHumanReadable,
				"condition_name" => $this->conditionName,
				"condition_type" => $this->conditionType,
				"condition_table" => $this->conditionTable,
				"condition_column" => $this->conditionColumn,
				"condition_values" => $this->conditionValues,
				"condition_historic" => $this->canBeHistoric,
				"condition_id" => $this->getConditionID()
			);
		}

		public function getTableNameHumanReadable()
		{
			return $this->tableNameHumanReadable;
		}

		public function getConditionID()
		{
			return $this->conditionTable . "~~" . $this->conditionColumn;
		}

		public function getConditionHistoric()
		{
			return $this->canBeHistoric;
		}

		public static function getConditionIncludeTextArray()
		{
			return array(
				self::TYPE_TEXT => array(
					"include" => "Text Equals",
					"exclude" => "Text Does Not Equal",
					"not_empty" => "Text is Not Empty",
					"empty" => "Text is Empty",
					"include_regex" => "Text Matches Pattern",
					"exclude_regex" => "Text Does Not Match Pattern",
				),

				self::TYPE_MULTI_TEXT => array(
					"include" => "Text Equals",
					"exclude" => "Text Does Not Equal",
					"not_empty" => "Text is Not Empty",
					"empty" => "Text is Empty",
					"include_regex" => "Text Matches Pattern",
					"exclude_regex" => "Text Does Not Match Pattern",
				),

				self::TYPE_NUMBER => array(
					"include" => "Number Equals",
					"exclude" => "Number Does Not Equal",
					"not_empty" => "Number is Not Empty",
					"empty" => "Number is Empty",
					"include_greater" => "Number is Greater Than",
					"include_lesser" => "Number is Less Than",
					"include_greater_equal" => "Number is Greater Than/Equal",
					"include_lesser_equal" => "Number is Less Than/Equal",
				),

				self::TYPE_DATE => array(
					"include" => "Date Equals",
					"exclude" => "Date Does Not Equal",
					"not_empty" => "Date is Not Empty",
					"empty" => "Date is Empty",
					"include_greater" => "Date is Greater Than",
					"include_lesser" => "Date is Less Than",
					"include_greater_equal" => "Date is Greater Than/Equal",
					"include_lesser_equal" => "Date is Less Than/Equal",
					"include_years_prior" => "Date is X Years Prior to Today",
					"include_years_forward" => "Date is X Years From Today",
				),

				self::TYPE_DROPDOWN => array(
					"include" => "Value Equals",
					"exclude" => "Value Does Not Equal",
					"not_empty" => "Value is Not Empty",
					"empty" => "Value is Empty",
				),

				self::TYPE_MULTI_DROPDOWN => array(
					"include" => "Value Equals",
					"exclude" => "Value Does Not Equal",
					"not_empty" => "Value is Not Empty",
					"empty" => "Value is Empty",
				),
			);
		}

		/*
			Loaded Condition:
			{
				type:			 TEXT|DROPDOWN|etc...
				include_if:		 string
				table:			 string
				columns:		 array()
				value:			 
			}

			{
				type:			 SPECIAL
				special_id:		 GLOBAL__start_date|GLOBAL__end_date|etc.			 
			}
		*/
		public static function findIfValueMatchesConditions($valueToQuery, $loadedCondition)
		{
			if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_TEXT))
			{
				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					$matchedOne = false;
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(Labori_Utl::streql($tempConditionValue, $valueToQuery))
						{
							$matchedOne = true;
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(Labori_Utl::streql($tempConditionValue, $valueToQuery))
						{
							return false;
						}
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if(empty(trim($valueToQuery)))
					{
						return false;
					}	
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if(!empty(trim($valueToQuery)))
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_regex"))
				{
					$matchedOne = false;

					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(preg_match($tempConditionValue, $valueToQuery))
						{
							$matchedOne = true;
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude_regex"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(preg_match($tempConditionValue, $valueToQuery))
						{
							return false;
						}
					}
				}
			}
			else if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_NUMBER))
			{
				if(is_array($loadedCondition["value"]) && !empty($loadedCondition["value"]))
				{
					$loadedCondition["value"] = array_pop($loadedCondition["value"]);
				}

				if(!(Labori_Utl::streql($loadedCondition["include_if"], "not_empty") ||
					  Labori_Utl::streql($loadedCondition["include_if"], "empty")) &&
					(!is_numeric($valueToQuery) || !is_numeric($loadedCondition["value"])))
				{
					return false;
				}
				else
				{
					$loadedCondition["value"] = floatval($loadedCondition["value"]);
					$valueToQuery = floatval($valueToQuery);
				}

				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					if($valueToQuery == null || $loadedCondition["value"] != $valueToQuery)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					if($loadedCondition["value"] == $valueToQuery)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if($valueToQuery == null || empty(trim($valueToQuery)))
					{
						return false;
					}	
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if($valueToQuery != null || !empty(trim($valueToQuery)))
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_greater"))
				{
					if($valueToQuery == null || $valueToQuery <= $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_lesser"))
				{
					if($valueToQuery == null || $valueToQuery >= $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_greater_equal"))
				{
					if($valueToQuery == null || $valueToQuery < $loadedCondition["value"])
					{	
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_lesser_equal"))
				{
					if($valueToQuery == null || $valueToQuery > $loadedCondition["value"])
					{
						return false;
					}
				}
			}
			else if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_DATE))
			{
				if(is_array($loadedCondition["value"]) && !empty($loadedCondition["value"]))
				{
					$loadedCondition["value"] = array_pop($loadedCondition["value"]);
				}
				
				if(isset($loadedCondition["value"]))
				{
					$loadedCondition["value"] = strtotime($loadedCondition["value"]);
				}
				
				$valueToQuery = strtotime($valueToQuery);
				
				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					if($loadedCondition["value"] != $valueToQuery)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					if($loadedCondition["value"] == $valueToQuery)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if(empty(trim($valueToQuery)))
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if(!empty(trim($valueToQuery)))
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_greater"))
				{
					if($valueToQuery <= $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_lesser"))
				{
					if($valueToQuery >= $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_greater_equal"))
				{
					if($valueToQuery < $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_lesser_equal"))
				{
					if($valueToQuery > $loadedCondition["value"])
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_years_prior"))
				{
					if(is_numeric($loadedCondition["value"]))
					{
						$loadedCondition["value"] = strtotime("-" . $loadedCondition["value"] . " years", strtotime("today"));
					}
					else
					{
						return false;
					}

					if($valueToQuery < $loadedCondition["value"])
					{
						return false;
					}


				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_years_forward"))
				{
					if(is_numeric($loadedCondition["value"]))
					{
						$loadedCondition["value"] = strtotime("+" . $loadedCondition["value"] . " years", strtotime("today"));
					}
					else
					{
						return false;
					}

					if($valueToQuery > $loadedCondition["value"])
					{
						return false;
					}
				}	
			}
			else if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_DROPDOWN))
			{
				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					$matchedOne = false;
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(Labori_Utl::streql($tempConditionValue, $valueToQuery))
						{
							$matchedOne = true;
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						if(Labori_Utl::streql($tempConditionValue, $valueToQuery))
						{
							return false;
						}
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if(empty(trim($valueToQuery)))
					{
						return false;
					}	
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if(!empty(trim($valueToQuery)))
					{
						return false;
					}
				}
			}
			else if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_MULTI_DROPDOWN))
			{
				$valuesToQuery = explode("~", $valueToQuery);

				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					$matchedOne = false;
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(Labori_Utl::streql($tempConditionValue, $thisValueToQuery))
							{
								$matchedOne = true;
								break;
							}
						}

						if($matchedOne)
						{
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(Labori_Utl::streql($tempConditionValue, $thisValueToQuery))
							{
								return false;
							}
						}
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if(empty(trim($valueToQuery)))
					{
						return false;
					}	
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if(!empty(trim($valueToQuery)))
					{
						return false;
					}
				}
			}
			else if(Labori_Utl::streql($loadedCondition["type"], self::TYPE_MULTI_TEXT))
			{
				$valuesToQuery = explode("~", $valueToQuery);

				if(Labori_Utl::streql($loadedCondition["include_if"], "include"))
				{
					$matchedOne = false;
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(Labori_Utl::streql($tempConditionValue, $thisValueToQuery))
							{
								$matchedOne = true;
								break;
							}
						}

						if($matchedOne)
						{
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(Labori_Utl::streql($tempConditionValue, $thisValueToQuery))
							{
								return false;
							}
						}
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "not_empty"))
				{
					if(empty(trim($valueToQuery)))
					{
						return false;
					}	
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "empty"))
				{
					if(!empty(trim($valueToQuery)))
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "include_regex"))
				{
					$matchedOne = false;

					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(preg_match($tempConditionValue, $thisValueToQuery))
							{
								$matchedOne = true;
								break;
							}
						}

						if($matchedOne)
						{
							break;
						}
					}

					if(!$matchedOne)
					{
						return false;
					}
				}
				else if(Labori_Utl::streql($loadedCondition["include_if"], "exclude_regex"))
				{
					foreach($loadedCondition["value"] as $tempConditionValue)
					{
						foreach($valuesToQuery as $thisValueToQuery)
						{
							if(preg_match($tempConditionValue, $thisValueToQuery))
							{
								return false;
							}
						}
					}
				}
			}

			return true;
		}
	}
?>