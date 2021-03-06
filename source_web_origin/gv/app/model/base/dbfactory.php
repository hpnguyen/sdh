<?php
class DbFactory {
	public $itemsCount = 0;
	public static $instance;
	public $sql = null;
	public $result = null;
	public $stmt = null;
	public $dbConnect = null;
	public $connectString = null;
	public $user = null;
	public $pass = null;
	public $charset = null;
	public $host = null;
	public $port = null;
	public $sid = null;
	private $dbConfigName = null;
	
	function __construct() {
		
	}
	
	function __destruct() {
		$this->close();
	}
	
	private function __clone() {
		
	}
	
	public function setConnect($connectString = '',$user, $pass, $charset = 'UTF8',$host = 'localhost',$port = 1521,$sid){
		$this->connectString = $connectString;
		$this->user = $user;
		$this->pass = $pass;
		$this->charset = $charset;
		$this->host = $host;
		$this->port = $port;
		$this->sid = $sid;
		
		if($connectString == '') {
			$this->connectString  = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$host.")(PORT = ".$port.")))(CONNECT_DATA=(SID=".$sid.")))";
		}else{
			$this->connectString = $connectString;
		}
		
		return $this;
	}
	
	public function connect() {
		if ($this->dbConnect) {
			oci_close($this->dbConnect);	
		}
		$config = Helper::getHelper('functions/util')->getDbFileConfig($this->dbConfigName);
		
		$connectString = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$config['host'].")(PORT = ".$config['port'].")))(CONNECT_DATA=(SID=".$config['sid'].")))";
		
		$this->connectString = $connectString;
		$this->user = $config['user'];
		$this->pass = $config['pass'];
		$this->charset = $config['charset'];
		$this->host = $config['host'];
		$this->port = $config['port'];
		$this->sid = $config['sid'];
		
		
		$db_conn = oci_connect($config['user'],$config['pass'],$connectString,$config['charset']);
		
		return $db_conn;
	}
	
	public function close() {
		if ($this->dbConnect) {
			oci_close($this->dbConnect);	
		}
		$this->itemsCount = 0;
		$this->sql = null;
		$this->result = null;
		$this->stmt = null;
		$this->connectString = null;
		$this->user = null;
		$this->pass = null;
		$this->charset = null;
		$this->host = null;
		$this->port = null;
		$this->sid = null;
		
		return $this;
	}
	
	public function getConnect(){
		return $this->dbConnect;
	}
	
	public static function getInstance() {
		if (!(self::$instance instanceof self)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function query($sql) {
			$this->sql =  $sql;
			return $this;
	}
	
	public function select($table, $fields = '*') {
			$this->sql =  'SELECT '.$fields.' FROM '.$table;
			return $this;
	}
	
	public function insert($table, $fields) {
		$arrayKeys = array_keys($fields);
		$arrayValues = array();
		foreach ($fields as $key => $value) {
			if (strtoupper($value) == 'SYSDATE') {
				$arrayValues[] = "SYSDATE";
			}elseif (strtoupper($value) == 'SYSTIMESTAMP'){
				$arrayValues[] = "SYSTIMESTAMP";
			}elseif (strtoupper($value) == 'CURRENT_TIMESTAMP'){
				$arrayValues[] = "CURRENT_TIMESTAMP";
			}elseif (strtoupper($value) == 'EMPTY_LOB()'){
				$arrayValues[] = "EMPTY_LOB()";
			}elseif (strtoupper($value) == 'EMPTY_CLOB()'){
				$arrayValues[] = "EMPTY_CLOB()";
			}elseif (strtoupper($value) == 'EMPTY_BLOB()'){
				$arrayValues[] = "EMPTY_BLOB()";
			}elseif(is_string($value)){
				$arrayValues[] = "'".$value."'";
			}elseif(is_int($value) || is_integer($value)){
				$arrayValues[] = $value;
			}elseif(is_null($value) || strtolower($value) == 'null'){
				$arrayValues[] = "NULL";
			}else{
				$arrayValues[] = $value;
			}
			
		}
		$temp = $arrayKeys;
		foreach ($arrayKeys as $i => $rowKey) {
			$temp[$i] = strtolower($rowKey);
		}
		
		$arrayKeys = $temp;
		
		require_once (ROOT_DIR.'app/model/UserTabColumns.php');
		
		$model = new UserTabColumns();
		$columnCLOB = $model->getColumnsCLOB($table);
		unset($model);
		
		foreach ($columnCLOB as $k => $row) {
			$colName = strtolower($row['column_name']);
			if (! in_array($colName, $arrayKeys)){
				$arrayKeys[] = $colName;
				$arrayValues[] = "EMPTY_CLOB()";
			}
		}
		
		$this->sql =  "INSERT INTO ".$table." (".implode(',', $arrayKeys).") VALUES (".implode(',', $arrayValues).")";
		//echo $this->sql;
		return $this;
	}
	
	public function update($table, $fields) {
		$array = array();
		foreach ($fields as $key => $value) {
			if (strtoupper($value) == 'SYSDATE') {
				$array[] = $key." = SYSDATE";
			}elseif (strtoupper($value) == 'SYSTIMESTAMP'){
				$array[] = $key." = SYSTIMESTAMP";
			}elseif (strtoupper($value) == 'CURRENT_TIMESTAMP'){
				$array[] = $key." = CURRENT_TIMESTAMP";
			}elseif (strtoupper($value) == 'EMPTY_LOB()'){
				$array[] = $key." = EMPTY_LOB()";
			}elseif (strtoupper($value) == 'EMPTY_CLOB()'){
				$array[] = $key." = EMPTY_CLOB()";
			}elseif (strtoupper($value) == 'EMPTY_BLOB()'){
				$array[] = $key." = EMPTY_BLOB()";
			}elseif (is_string($value)){
				$array[] = $key." = '".$value."'";
			}elseif(is_int($value) || is_integer($value)){
				$array[] = $key." = ".$value;
			}elseif(is_null($value) || strtolower($value) == 'null'){
				$array[] = $key." = NULL";
			}else{
				$array[] = $key." = ".$value;
			}
			
		}
		
		$this->sql =  "UPDATE ".$table." SET ".implode(',', $array);
		return $this;
	}
	
	public function delete($table) {
		$this->sql =  "DELETE FROM ".$table;
		return $this;
	}
	
	public function drop($table) {
		$this->sql =  "DROP TABLE ".$table;
		return $this;
	}
	
	public function create($table, $primaryFieldArray, $fieldArray) {
		if (is_array($primaryFieldArray) && is_array($fieldArray)){
			$this->sql =  "CREATE TABLE ".$table;
			
			$fieldArrayTemp =array();
			foreach ($fieldArray as $key => $value) {
				if (count($primaryFieldArray) == 1 && strtoupper($key) != strtoupper($primaryFieldArray[0])) {
					$fieldArrayTemp[strtoupper($key)] = $value;
				}else if(count($primaryFieldArray) != 1){
					$fieldArrayTemp[strtoupper($key)] = $value;
				}
			}
			$primary = "";
			if (count($primaryFieldArray) == 1) {
				if(array_key_exists($primaryFieldArray[0],$fieldArray)){
					$option = $fieldArray[$primaryFieldArray[0]];
				}else{
					$option = $fieldArray[strtoupper($primaryFieldArray[0])];
				}
				$primary = strtoupper($primaryFieldArray[0])." ".strtoupper($option[0])." NOT NULL PRIMARY KEY";
			}else if (count($primaryFieldArray) > 1){
				$primary = " CONSTRAINT ".strtoupper($table)."_PK PRIMARY KEY (".implode(",", $primaryFieldArray).")";
			}
			
			$tempSql = "";
			
			foreach ($fieldArrayTemp as $column => $item) {
				if($tempSql != ""){
					$tempSql .=",
";
				}
				$tempSql .=" ".strtoupper($column)." ".strtoupper($item[0])." ".$item[1];
			}
			if($tempSql != "" && $primary != ""){
				$tempSql .=",
".$primary;
			}
			
			$this->sql = $this->sql."(".
$tempSql."
)";
		}
		
		return $this;
	}
	
	public function where($where) {
		$this->sql .= ' WHERE '.$where;
		return $this;
	}
	
	public function order($order) {
		$this->sql .= ' ORDER BY '.$order;
		return $this;
	}
	
	public function execute($executeOnly = false,$params = array(),$fieldNameLower = true,$reverseEscape = false) {
		$config = Helper::getHelper('functions/util')->getDbFileConfig($this->dbConfigName);
		$connectString = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$config['host'].")(PORT = ".$config['port'].")))(CONNECT_DATA=(SID=".$config['sid'].")))";
		
		$sdlString = "".$this->sql;
		$db_conn = oci_connect($config['user'],$config['pass'],$connectString,$config['charset']);
		$stmt = oci_parse($db_conn, $sdlString) or die ("SQL ERROR PARSE: " . oci_error($db_conn));
		oci_execute($stmt) or die ("SQL ERROR EXECUTE: " . oci_error($stmt));
		
		if (! $executeOnly) {
			$fetArray = array();
			while ($row = oci_fetch_assoc($stmt)) {
				$item = array();
				
				foreach($row as $key => $value){
					$name = $fieldNameLower ? strtolower($key) : $key;
					//Check CLOB or BLOB data need to load
					if (is_object($value)){
						$text = $value->load();
						if (! empty($text)){
							if($reverseEscape){
								$text = Helper::getHelper('functions/util')->reverse_escape($text);
							}
							$item[$name] = $text;
						}else{
							$item[$name] = null;
						}
					}else{
						$item[$name] =  $value;
					}
				}
					
				$fetArray[] = $item;
			}
			$this->itemsCount = count($fetArray);
			if($this->itemsCount > 0) {
				$this->result = $fetArray;
			}
		}
		
		oci_free_statement($stmt);
		if($db_conn) {
			oci_close($db_conn);
		}
		
		return $this;
	}
	
	public function bindExecute($executeOnly = false,$params = array(),$fieldNameLower = true) {
		$config = Helper::getHelper('functions/util')->getDbFileConfig($this->dbConfigName);
		$connectString = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$config['host'].")(PORT = ".$config['port'].")))(CONNECT_DATA=(SID=".$config['sid'].")))";
		
		$sdlString = "".$this->sql;
		$db_conn = oci_connect($config['user'],$config['pass'],$connectString,$config['charset']);
		$stmt = oci_parse($db_conn, $sdlString) or die ("SQL ERROR PARSE: " . oci_error($db_conn));
		
		//Use params bind to sql
		foreach ($params as $k => $v) {
			oci_bind_by_name($stmt, $k, $v);
		}
		oci_execute($stmt) or die ("SQL ERROR EXECUTE: " . oci_error($stmt));
		
		if (! $executeOnly) {
			$fetArray = array();
			while ($row = oci_fetch_assoc($stmt)) {
				$item = array();
				
				foreach($row as $key => $value){
					$name = $fieldNameLower ? strtolower($key) : $key;
					$item[$name] =  $value;
				}
					
				$fetArray[] = $item;
			}
			$this->itemsCount = count($fetArray);
			if($this->itemsCount > 0) {
				$this->result = $fetArray;
			}
		}
		
		oci_free_statement($stmt);
		if($db_conn) {
			oci_close($db_conn);
		}
		
		return $this;
	}
	
	public function executeCLOB($nameFiledCLOB, $dataCLOB, $escapeJsonString = false) {
		$config = Helper::getHelper('functions/util')->getDbFileConfig($this->dbConfigName);
		$connectString = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$config['host'].")(PORT = ".$config['port'].")))(CONNECT_DATA=(SID=".$config['sid'].")))";
		
		$sdlString = "".$this->sql." RETURNING ".$nameFiledCLOB." INTO :descriptor";
		// echo $sdlString;
		
		$db_conn = oci_connect($config['user'],$config['pass'],$connectString,$config['charset']);
		$stmt = oci_parse($db_conn, $sdlString) or die ("SQL ERROR PARSE: " . oci_error($db_conn));
		
		$descriptor = oci_new_descriptor($db_conn, OCI_D_LOB);
		oci_bind_by_name($stmt, ":descriptor", $descriptor, -1, OCI_B_CLOB);
		
		oci_execute($stmt, OCI_DEFAULT) or die ("SQL ERROR EXECUTE: " . oci_error($stmt));
		
		if ($escapeJsonString ){
			$dataCLOB = Helper::getHelper('functions/util')->escapeJsonString($dataCLOB);
		}
		
		$descriptor->save($dataCLOB);
		
		oci_commit($db_conn);
		$descriptor->free();
		oci_free_statement($stmt);
		if($db_conn) {
			oci_close($db_conn);
		}
		
		return $this;
	}
	
	public function parse($fieldNameLower = true) {
			if($this->stmt != null){
				$stmt = $this->stmt;
				// Read all the data back in as associative arrays
				$fetArray = array();
				$test = function_exists(oci_fetch_assoc);
				
				while ($row = oci_fetch_assoc($stmt,OCI_BOTH) != false) {
						$item = array();
						foreach($row as $key => $value){
							$name = $fieldNameLower ? strtolower($key) : $key;
							$item[$name] =  $value;
						}
						
						$fetArray[] = $item;
						
				}
				oci_free_statement($stmt);
				
				$this->itemsCount = count($fetArray);
				if($this->itemsCount > 0) {
					$this->result = $fetArray;
				}
				//var_dump($fetArray);
			}
			return $this;
	}
	
	public function fetchAll() {
			return $this->result;
	}
	
	public function fetchFirst() {
			if ($this->itemsCount > 0){
				return $this->result[0];
			}
			return null;
	}
	
	public function fetchLast() {
			if ($this->itemsCount > 0){
				return $this->result[$this->itemsCount - 1];
			}
			return null;
	}
	
	public function fetchRow($index) {
			if ($index >=0 &&  $index < $this->itemsCount){
				return $this->result[$index];
			}
			return null;
	}
	
	public function setDbConfigName($name = ''){
		$this->dbConfigName = $name;
	}
	
	public function getDbConfigName(){
		return $this->dbConfigName;
	}
}
