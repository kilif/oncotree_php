<?php
	require_once dirname(__FILE__) . '/../Labori_Core.php';
	use ForceUTF8\Encoding;

	class Labori_DB
	{
		const TYPE_MYSQL = "mysql";
		const TYPE_MSSQL = "mssql";
		const TYPE_POSTGRESQL = "postgresql";

		public static function getConnectionDBType($connection)
		{
			if(is_null($connection))
			{
				return null;

			}

			if(is_resource($connection) && Labori_Utl::streql(get_resource_type($connection), "pgsql link"))
			{
				return self::TYPE_POSTGRESQL;
			}
			else if(Labori_Utl::streql(get_class($connection), "mysqli"))
			{
				return self::TYPE_MYSQL;
			}
			//PDO only used for SQL server
			else if(Labori_Utl::streql(get_class($connection), "pdo"))
			{
				return self::TYPE_MSSQL;
			}
		}

		public static function getConnectionInfo($databaseId)
		{
			if(array_key_exists($databaseId, Database_Config::DATABASE_LIST))
			{
				return Database_Config::DATABASE_LIST[$databaseId];
			}
			else
			{
				throw new Exception("Database Error: Database ID doesn't exist.");
			}
		}

		public static function escStr($conn, $field)
		{
			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					return $conn->escape_string($field);
				}
				else if(Labori_Utl::streql($connectionType, self::TYPE_MSSQL))
				{
					return $conn->quote($field);
				}

				return $connectionType;
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		public static function genConcatStatment($conn, $columnArray)
		{
			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$concatStatement = "CONCAT(";
					$toAdd = null;

					foreach($columnArray as $thisColumn)
					{
						

						$toAdd = Labori_Utl::addtoDelinatedStr($toAdd, $thisColumn, ", ");
						
					}

					return $concatStatement . $toAdd . ")";
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		public static function generateConn($configArray)
		{
			ini_set('mysql.connect_timeout', 900);
			ini_set('default_socket_timeout', 900); 

			if(!Labori_Utl::allKeysExist(array("host", "user", "pass", "name", "port", "type"), $configArray))
			{
				throw new Exception("Database Connection Failure");
			}

			if(Labori_Utl::streql($configArray["type"], self::TYPE_MYSQL))
			{
				$mysqli = new mysqli($configArray["host"], 
									 $configArray["user"], 
									 $configArray["pass"], 
									 $configArray["name"],
									 $configArray["port"]);
												 
				if ($mysqli->connect_errno) 
				{			
					throw new Exception("Database Error");
				}
				else
				{
					mysqli_set_charset($mysqli,"utf8");
					return $mysqli;
				}
			}
			if(Labori_Utl::streql($configArray["type"], self::TYPE_POSTGRESQL))
			{
				set_error_handler(function($errno, $errstr, $errfile, $errline ) {
				    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
				});

				$pgConn = @pg_connect("host=" . $configArray["host"] . 
								   " port=" . $configArray["port"] . 
								   " dbname=" . $configArray["name"] . 
								   " user=" . $configArray["user"] . 
								   " password=" . $configArray["pass"]);

				restore_error_handler();

				if(pg_connection_status($pgConn) === PGSQL_CONNECTION_OK)
				{
					pg_set_client_encoding($pgConn, "UTF-8");
					return $pgConn;
				}
				else
				{
					throw new Exception("Database Error");
				}
			}
			else if(Labori_Utl::streql($configArray["type"], self::TYPE_MSSQL))
			{
				try
				{
					return new PDO("sqlsrv:Server=" . $configArray["host"] . "," . $configArray["port"] . ";Database=" . $configArray["name"],
							   $configArray["user"], $configArray["pass"]);
				}
				catch(Exception $e)
				{
					throw new Exception("Database Error");
				}
			}
			else
			{
				throw new Exception("Database Error: Unsupported database type.");
			}
		}

		public static function genConnHelper($connectionID)
		{
			return Labori_DB::generateConn(Labori_DB::getConnectionInfo(Labori_Core::getDeploymentOption($connectionID, true)));
		}

		public static function getNextAutoIncrementNumber($conn, $connectionID, $table)
		{
			if(!is_null($conn))
			{
				$connectionInfo = Labori_DB::getConnectionInfo(Labori_Core::getDeploymentOption($connectionID, true));
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$query = 'SELECT AUTO_INCREMENT
							  FROM information_schema.TABLES
							  WHERE TABLE_SCHEMA = "' . $connectionInfo["name"] . '"
							  AND TABLE_NAME = "' . $table . '"';

					$result = $conn->query($query);

					while($row = mysqli_fetch_assoc($result))
					{
						return $row["AUTO_INCREMENT"];
					}
				}
				else
				{
					throw new Exception("Database Error: Unsupported database type.");
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}

			return null;
		}

		public static function moveDataFromDBAtoDBB($connA, $connB, $table)
		{
			if(!is_null($connA) && !is_null($connB))
			{
				if(Labori_Utl::streql(self::getConnectionDBType($connA), self::TYPE_MYSQL) && 
				   Labori_Utl::streql(self::getConnectionDBType($connB), self::TYPE_MYSQL))
				{
					self::performQuery($connB, "truncate $table");
					$results = self::performQuery($connA, "Select * from $table");
					
					foreach($results["rows"] as $thisRow)
					{
						$columns = array();
						$data = array();

						foreach($thisRow as $thisKey => $thisValue)
						{
							if(!is_null($thisValue))
							{
								$columns[$thisKey] = array();
								$data[$thisKey] = $thisValue;
							}
						}

						$insertStatement = self::genInsertStatement($connB, $columns, array($data), $table);
						self::performQuery($connB, $insertStatement);
						echo "Handled $table <br>";
					}
				}
				else
				{
					throw new Exception("Unsupported connections.");
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		public static function compareDatabases($connA, $connB)
		{
			if(!is_null($connA) && !is_null($connB))
			{
				if(Labori_Utl::streql(self::getConnectionDBType($connA), self::TYPE_MYSQL) && 
				   Labori_Utl::streql(self::getConnectionDBType($connB), self::TYPE_MYSQL))
				{
					$differencesArray = array();

					$tablesA = self::performQuery($connA, "show tables");
					$tablesB = self::performQuery($connB, "show tables");

					if(isset($tablesA["rows"]) && isset($tablesB["rows"]))
					{
						foreach($tablesA["rows"] as $thisTableRowA)
						{
							$tableNameA = reset($thisTableRowA);
							$foundTable = false;

							foreach($tablesB["rows"] as $thisTableRowB)
							{
								$tableNameB = reset($thisTableRowB);

								if(Labori_Utl::streql($tableNameA, $tableNameB))
								{
									$foundTable = true;

									$rowCheckA = self::performQuery($connA, "describe " . $tableNameA);
									$rowCheckB = self::performQuery($connB, "describe " . $tableNameB);

									if(isset($rowCheckA["rows"]) && isset($rowCheckB["rows"]))
									{
										foreach($rowCheckA["rows"] as $thisRowRowA)
										{
											$foundRow = false;

											foreach($rowCheckB["rows"] as $thisRowRowB)
											{
												if(Labori_Utl::streql($thisRowRowA["Field"], $thisRowRowB["Field"]))
												{
													if(!Labori_Utl::streql($thisRowRowA["Type"], $thisRowRowB["Type"]))
													{
														$differencesArray[] = "Type Difference from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Null"], $thisRowRowB["Null"]))
													{
														$differencesArray[] = "Null Difference from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Key"], $thisRowRowB["Key"]))
													{
														$differencesArray[] = "Key Difference from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Default"], $thisRowRowB["Default"]))
													{
														$differencesArray[] = "Default Difference from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Extra"], $thisRowRowB["Extra"]))
													{
														$differencesArray[] = "Extra Difference from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
													}

													$foundRow = true;
													continue;
												}
											}

											if(!$foundRow)
											{
												$differencesArray[] = "Missing Row from A to B: (" . $tableNameA . ":" . $thisRowRowA["Field"] . ")";
											}
										}
									}

									continue;
								}
							}

							if(!$foundTable)
							{
								$differencesArray[] = "Missing Table from A to B: (" . $tableNameA . ")";
							}
						}

						foreach($tablesB["rows"] as $thisTableRowB)
						{
							$tableNameB = reset($thisTableRowB);
							$foundTable = false;

							foreach($tablesA["rows"] as $thisTableRowA)
							{
								$tableNameA = reset($thisTableRowA);

								if(Labori_Utl::streql($tableNameA, $tableNameB))
								{
									$foundTable = true;

									$rowCheckA = self::performQuery($connA, "describe " . $tableNameA);
									$rowCheckB = self::performQuery($connB, "describe " . $tableNameB);

									if(isset($rowCheckA["rows"]) && isset($rowCheckB["rows"]))
									{
										foreach($rowCheckB["rows"] as $thisRowRowB)
										{
											$foundRow = false;

											foreach($rowCheckA["rows"] as $thisRowRowA)
											{
												if(Labori_Utl::streql($thisRowRowA["Field"], $thisRowRowB["Field"]))
												{
													if(!Labori_Utl::streql($thisRowRowA["Type"], $thisRowRowB["Type"]))
													{
														$differencesArray[] = "Type Difference from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Null"], $thisRowRowB["Null"]))
													{
														$differencesArray[] = "Null Difference from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Key"], $thisRowRowB["Key"]))
													{
														$differencesArray[] = "Key Difference from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Default"], $thisRowRowB["Default"]))
													{
														$differencesArray[] = "Default Difference from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
													}

													if(!Labori_Utl::streql($thisRowRowA["Extra"], $thisRowRowB["Extra"]))
													{
														$differencesArray[] = "Extra Difference from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
													}

													$foundRow = true;
													continue;
												}
											}

											if(!$foundRow)
											{
												$differencesArray[] = "Missing Row from B to A: (" . $tableNameA . ":" . $thisRowRowB["Field"] . ")";
											}
										}
									}

									continue;
								}
							}

							if(!$foundTable)
							{
								$differencesArray[] = "Missing Table from B to A: (" . $tableNameB . ")";
							}
						}
					}
					else
					{
						throw new Exception("Database Error: Could not find tables.");
					}

					return $differencesArray;
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		public static function performQuery($conn, $query, $indexBy=null, $suppressLogging=false)
		{
			$query = Encoding::fixUTF8($query);

			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$result = $conn->query($query);
				
					if(!$result)
					{
						if(!$suppressLogging)
						{
							throw new Exception("Database Error: An error was found in the query: (Error No. " . 
								   				mysqli_errno($conn) .") follows: " . mysqli_error($conn));
						}
						else
						{
							echo  "The provided sql query ($query) has an error in it. SQL error (Error No. " . 
								   mysqli_errno($conn) .") follows: " . mysqli_error($conn);
						}
					}
					else if($result === true)
					{
						return true;
					}
					else
					{
						$retArray = array();

						while($row = mysqli_fetch_assoc($result))
						{
							if(!is_null($indexBy) && isset($row[$indexBy]))
							{
								$retArray[$row[$indexBy]] = $row;
							}
							else
							{
								$retArray[] = $row;
							}
						}

						return array("row_count" => mysqli_num_rows($result),
		 						     "rows" => $retArray);
					}
				}
				else if(Labori_Utl::streql($connectionType, self::TYPE_MSSQL))
				{
					try
					{
						$result = $conn->query($query);
						$retArray = array();

						while($row = $result->fetch(PDO::FETCH_ASSOC))
						{  
   							if(!is_null($indexBy) && isset($row[$indexBy]))
							{
								$retArray[$row[$indexBy]] = $row;
							}
							else
							{
								$retArray[] = $row;
							}
						} 

						return array("row_count" => count($retArray),
		 						     "rows" => $retArray);
					}
					catch(Exception $e)
					{
						if(!$suppressLogging)
						{
							throw new Exception("Database Error: An error was found in the query.");
						}
						else
						{
							echo  "The provided sql query ($query) has an error in it.";
						}
					}
				}
				else if(Labori_Utl::streql($connectionType, self::TYPE_POSTGRESQL))
				{		
					$result = pg_query($conn, $query);
					
					if(!$result) 
					{
						throw new Exception("Database Error: An error was found in the query.");
					}

					$retArray = array();

					while ($row = pg_fetch_assoc($result))
					{
						if(!is_null($indexBy) && isset($row[$indexBy]))
						{
							$retArray[$row[$indexBy]] = $row;
						}
						else
						{
							$retArray[] = $row;
						}
					}

					return array("row_count" => count($retArray),
	 						     "rows" => $retArray);
				}
				else
				{
					throw new Exception("Database Error: Unsupported database type.");
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		/*
		This function accepts the following array structures:

			$columns = 
			{
				columnName1:
				{
					escape:(true|false;default true) 
				},
				columnName2:
				{
					escape:(true|false;default true)
				},
				...
			}

			$data =
				{
					columnName1:value1,
					columnName2:value1,
					...
				}
		*/
		public static function genUpdateStatement($conn, $columns, $data, $table)
		{
			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$updateStatement = "UPDATE " . $table . " SET ";
					$toAdd = null;

					foreach($columns as $thisColumnId => $meta)
					{
						if(isset($data[$thisColumnId]))
						{
							$valueToAdd = "'" . mysqli_escape_string($conn, $data[$thisColumnId]) . "'";
								
							if(isset($meta["escape"]))
							{
								if(!$meta["escape"])
								{
									$valueToAdd = "'" . $data[$thisColumnId] . "'";
								}
							}
							else if(isset($meta["no_escape_no_quotes"]))
							{
								if($meta["no_escape_no_quotes"])
								{
									$valueToAdd = $data[$thisColumnId];
								}
							}

							$toAdd = Labori_Utl::addtoDelinatedStr($toAdd, $thisColumnId . "=" . $valueToAdd, ", ");
						}
					}

					return $updateStatement . $toAdd;
				}
				else
				{
					throw new Exception("Database Error: Unsupported database type.");
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		/*
		This function accepts the following array structures:

			$columns = 
			{
				columnName1:
				{
					escape:(true|false;default true) 
				},
				columnName2:
				{
					escape:(true|false;default true)
				},
				...
			}

			$data =
			[
				{
					columnName1:value11,
					columnName2:value12,
					...
				},
				{
					columnName1:value21,
					columnName2:value22,
					...
				},
				...
			]
		*/
		public static function genInsertStatementsMulti($conn, $columns, $data, $table, $updateOnDuplicateKey = false)
		{
			$dataChunks = array_chunk($data, 20, true);
			$retList = array();

			foreach($dataChunks as $thisChunk)
			{
				$retList[] = self::genInsertStatement($conn, $columns, $thisChunk, $table, $updateOnDuplicateKey);
			}

			return $retList;
		}

		public static function genInsertStatement($conn, $columns, $data, $table, $updateOnDuplicateKey = false)
		{
			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$insertStatement = "INSERT INTO " . $table . "(";
					$toAdd = null;
					$valueOnDupKey = null;
					
					foreach($columns as $thisColumnId => $meta)
					{
						$toAdd = Labori_Utl::addtoDelinatedStr($toAdd, $thisColumnId, ", ");

						if($updateOnDuplicateKey)
						{
							$valueOnDupKey = Labori_Utl::addtoDelinatedStr($valueOnDupKey, $thisColumnId . " = VALUES(" . $thisColumnId . ")", ", ");
						}
					}

					$insertStatement .= $toAdd . ') VALUES ';
					$valueAddStr = null;

					foreach($data as $thisRow)
					{
						$tempAdd = null;

						foreach($columns as $thisColumnId => $meta)
						{
							if(isset($thisRow[$thisColumnId]))
							{
								$valueToAdd = "'" . mysqli_escape_string($conn, $thisRow[$thisColumnId]) . "'";
								
								if(isset($meta["escape"]))
								{
									if(!$meta["escape"])
									{
										$valueToAdd = "'" . $thisRow[$thisColumnId] . "'";
									}
								}
								else if(isset($meta["no_escape_no_quotes"]))
								{
									if($meta["no_escape_no_quotes"])
									{
										$valueToAdd = $thisRow[$thisColumnId];
									}
								}

								$tempAdd = Labori_Utl::addtoDelinatedStr($tempAdd, $valueToAdd, ", ");
							}
						}

						$tempAdd = "(" . $tempAdd .")";

						$valueAddStr = Labori_Utl::addtoDelinatedStr($valueAddStr, $tempAdd, ", ");
					}

					if($updateOnDuplicateKey)
					{
						return $insertStatement . $valueAddStr . " ON DUPLICATE KEY UPDATE " . $valueOnDupKey;
					}
					else
					{
						return $insertStatement . $valueAddStr;
					}
				}
				else
				{
					throw new Exception("Database Error: Unsupported database type.");
				}
			}
			else
			{
				throw new Exception("Database Error: Connection is null.");
			}
		}

		public static function closeConnection($conn)
		{
			if(!is_null($conn))
			{
				$connectionType = self::getConnectionDBType($conn);

				if(Labori_Utl::streql($connectionType, self::TYPE_MYSQL))
				{
					$conn->close();
				}
				else if(Labori_Utl::streql($connectionType, self::TYPE_POSTGRESQL))
				{
					pg_close($conn);
				}
			}
		}
	}
?>