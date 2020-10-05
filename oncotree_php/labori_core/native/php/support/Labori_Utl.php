<?php
	class Labori_Utl
	{
		/******************************************************/
		/*FILE UTILITIES								  	  */
		/******************************************************/
		public static function getParentDirectoryOfClass($class)
		{
			$child = new $class();
			$class_info = new ReflectionClass($child);
			$tempDir = dirname($class_info->getFileName());

			if(Labori_Utl::strContains("/", $tempDir))
			{
				$directories = explode("/", $tempDir);

				return $directories[count($directories) - 1];
			}
			else
			{
				$directories = explode("\\", $tempDir);

				return $directories[count($directories) - 1];
			}
		}
		public static function getParentDirectoryName($tempDir)
		{
			if(Labori_Utl::strContains("/", $tempDir))
			{
				$directories = explode("/", $tempDir);

				return $directories[count($directories) - 1];
			}
			else
			{
				$directories = explode("\\", $tempDir);

				return $directories[count($directories) - 1];
			}
		}

		/******************************************************/
		/*RANGE UTILITIES								  	  */
		/******************************************************/
		public static function rangesOverlap($startRangeA, $endRangeA, $startRangeB, $endRangeB)
		{
			if(!is_null($startRangeA) && !is_null($startRangeB))
			{
				if($startRangeA <= $startRangeB && (is_null($endRangeA) || $endRangeA >= $startRangeB))
				{
					return true;
				}
				else if($startRangeA >= $startRangeB && (is_null($endRangeB) || $startRangeA <= $endRangeB))
				{
					return true;
				}
			}
			else if(is_null($startRangeA))
			{
				if(is_null($endRangeA) || $endRangeA >= $startRangeB)
				{
					return true;
				}
			}
			else if(is_null($startRangeB))
			{
				if(is_null($endRangeB) || $startRangeA <= $endRangeB)
				{
					return true;
				}
			}


			return false;
		}

		public static function rangesOverlap_exclusive($startRangeA, $endRangeA, $startRangeB, $endRangeB)
		{
			if(!is_null($startRangeA) && !is_null($startRangeB))
			{
				if($startRangeA < $startRangeB && (is_null($endRangeA) || $endRangeA > $startRangeB))
				{
					return true;
				}
				else if($startRangeA > $startRangeB && (is_null($endRangeB) || $startRangeA < $endRangeB))
				{
					return true;
				}
			}
			else if(is_null($startRangeA))
			{
				if(is_null($endRangeA) || $endRangeA > $startRangeB)
				{
					return true;
				}
			}
			else if(is_null($startRangeB))
			{
				if(is_null($endRangeB) || $startRangeA < $endRangeB)
				{
					return true;
				}
			}

			return false;
		}

		/******************************************************/
		/*NUMBER UTILITIES								  	  */
		/******************************************************/
		public static function safeFormatNumber($number, $decimalPlaces = 2)
		{
			if(!is_null($number) && !empty($number) && is_numeric($number))
			{
				return number_format($number, $decimalPlaces);
			}
			else
			{
				return "0";
			}
		}

		/******************************************************/
		/*REQUEST UTILITIES								  	  */
		/******************************************************/
		public static function getURLRedirect($url)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$a = curl_exec($ch); // $a will contain all headers

			$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

			// Uncomment to see all headers
			/*
			echo "<pre>";
			print_r($a);echo"<br>";
			echo "</pre>";
			*/

			curl_close($ch);
			return $url; // Voila
		}
		
		public static function outsidePageRequest($url, $host, $cookieLoc, $postArray = array(), $noBody = false, $timeout = 0)
		{
			$ch = curl_init();

			if(count($postArray) > 0)
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,
            	http_build_query($postArray));
			}

			curl_setopt($ch, CURLOPT_URL, $url);

			if($noBody)
			{
				curl_setopt($ch, CURLOPT_NOBODY, true);
			}

			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieLoc);
			$http_headers = array(
						            'Host: ' . $host,
						            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
						            'Accept: */*',
						            'Accept-Language: en-us,en;q=0.5',
						            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
						            'Connection: keep-alive'
			          			);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			$output = curl_exec($ch);
			curl_close($ch);
			return $output;
		}

		/******************************************************/
		/*STRING UTILITIES								  	  */
		/******************************************************/
		public static function safeSubStrFromEnd($string, $amount = 1)
		{
			if(0 < strlen($string) - $amount)
			{
				return substr($string, 0, strlen($string) - $amount);
			}
			else
			{
				return $string;
			}
		}

		public static function truncate($string, $length)
		{
			if (strlen($string) > $length) 
			{
				$string = substr($string, 0, $length) . '...';
			}

			return $string;
		}


		public static function boolToStr($boolean)
		{
			if($boolean)
			{
				return "true";
			}
			else
			{
				return "false";
			}
		}

		public static function generateRandomString($length = 10) 
		{
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';

		    for ($i = 0; $i < $length; $i++) 
		    {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }

		    return $randomString;
		}
		
		public static function addtoDelinatedStr($accumStr, $newValue, $divider)
		{
			if(is_null($accumStr))
			{
				return $newValue;
			}
			else
			{
				return $accumStr . $divider . $newValue;
			}
		}

		public static function parseDelimitedStr($delimiter, $str)
		{
			$valList = array();

			if(self::strContains($delimiter, $str))
			{
				$valList = explode($delimiter, $str);
			}
			else
			{
				$valList[] = $str;
			}

			return $valList;
		}

		public static function strContains($subStr, $string)
		{
			if(is_null($subStr) || empty($subStr))
			{
				return false;
			}
			else if(strpos($string, $subStr) !== false) 
			{

				return true;
			}
			else
			{
				return false;
			}
		}

		public static function streql($a, $b)
		{
			if(is_null($a) && is_null($b))
			{
				return true;
			}
			else if(is_null($a) != is_null($b))
			{
				return false;
			}
			else if(empty($a) && empty($b))
			{
				return true;
			}
			else if(empty($a) != empty($b))
			{
				return false;
			}
			else
			{
				if(strcasecmp($a, $b) == 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		public static function escapeSingleQuotes($string)
		{
			$temp =  preg_replace("`([^\\\])'`","$1\'",$string);

			if(substr($temp, 0, 1) === "'")
			{
				return "\'" .substr($temp, 1);
			}
			else
			{
				return $temp;
			}
		}

		public static function escapeDoubleQuotes($string)
		{
			$temp =  preg_replace('`([^\\\])"`','$1\"',$string);

			if(substr($temp, 0, 1) === '"')
			{
				return '\"' .substr($temp, 1);
			}
			else
			{
				return $temp;
			}
		}

		/******************************************************/
		/*ID UTILITIES								  	 	  */
		/******************************************************/
		public static function generateUUID()
		{
			$t = microtime(true);
			$micro = sprintf("%06d",($t - floor($t)) * 1000000);
			$d = new DateTime(date('Y-m-d H:i:s.'.$micro, $t));

			return 'a' . uniqid("", true) . "_" . $d->format("Y-m-d H:i:s.u");
		}

		public static function generateUUID_urlSafe()
		{
			$t = microtime(true);
			$micro = sprintf("%06d",($t - floor($t)) * 1000000);
			$d = new DateTime(date('Y-m-d H:i:s.'.$micro, $t));

			$temp = uniqid("", true) . "_" . $d->format("Y-m-d H:i:s.u");
			$temp = str_replace(".", "_", $temp);
			$temp = str_replace(" ", "_", $temp);
			$temp = str_replace(":", "_", $temp);
			$temp = str_replace("-", "_", $temp);

			return 'a' . $temp;
		}

		/******************************************************/
		/*ARRAY UTILITIES								  	  */
		/******************************************************/
		public static function arraysContainSameElements($array1, $array2)
		{
			if(empty(array_diff($array1, $array2)))
			{
				return true;
			}

			return false;
		}

		public static function recursiveImplode($array, $glue) 
		{
			if(!is_array($array))
			{
				return '';
			}

		    $ret = '';

		    foreach ($array as $item) 
		    {
		        if (is_array($item)) 
		        {
		            $ret .= recursiveImplode($item, $glue) . $glue;
		        } 
		        else 
		        {
		            $ret .= $item . $glue;
		        }
		    }

		    $ret = substr($ret, 0, 0-strlen($glue));

		    return $ret;
		}

		public static function isAssociativeArray($arr)
		{
		    if (array() === $arr)
		    {
				return false;
		    } 
		    else
		    {
		    	return array_keys($arr) !== range(0, count($arr) - 1);
		    }   
		}

		public static function atLeastOneKeyShared($array1, $array2)
		{
			if(!is_array($array1) || !is_array($array2))
			{
				return self::genResponse(false, "One of the arrays passed to function are malformed.");
			}

			if (count($intersection = array_intersect_key($array1, array_flip($array2))) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public static function allKeysExist($keys, $array)
		{
			foreach($keys as $thisKey)
			{
				if(!isset($array[$thisKey]))
				{
					return false;
				}
			}

			return true;
		}
	}
?>