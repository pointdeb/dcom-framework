<?php
namespace dcom\components\database;
use dcom\components\database\DbConnector;
use dcom\components\exceptions\DExceptions;
 class DbBuilder extends DbConnector{
	/**
	*database name
	*@var string $_dbname
	*/
		private $_dbName=null;
	/**
	* charset of data which will be stored in the database
	*@var string $_dataType
	*/
		private $_dataType='utf8';
	/**
	* collate of data which will be stored in the database
	*@var string $_collate
	*/
		private $_collate='utf8_general_ci';
	/**
	* enable sql IF NOT EXISTS option
	*@var boolean $_exist
	*/
		private $_exist=true;
	/**
	* sql query command
	*@var string $_sql
	*/
		private $_sql='CREATE DATABASE ';
		private $_req=null;
		private $_auto=false;
	/**
	* dbBuilder construnctor
	*
	*/
	/*	public function __construct($dbName=null,$dataType="utf8",$exist=true,$collate="utf8_general_ci"){
			if (is_bool($dbName)) {
				$this->_auto=$dbName;
			}
			elseif(is_string($dbName)&&$dbName!=null){
				$this->setName($dbName);
			}
			$this->setDataType($dataType);
			$this->setCollate($collate);
			$this->exist($exist);
			$this->execute();
		}*/
		public function __destruct(){
			$this->_dbName=null;
			$this->_dataType=null;
			$this->_exist=null;
			$this->_collate=null;
			$this->_req=null;
		}

	/**
	* set name of the building database
	*@param string $dbName
	*@return boolean
	*/
		public function setName($dbName=null){
			//test dbname null or not,
			if ($dbName!=null) {

				$this->_dbName=$dbName;
			}
			else{
					new DExceptions("dbName null for setName(dbName)", 405);
			}
		}
	/**
	* enable sql IF NOT EXISTS option
	*@param boolean $exist
	*@return boolean
	*/
		public function exist($exist=true){
			if ($this->_exist!=null) {
				$this->_exist=$exist;
				return true;
			}
			else{
				return $this->_exist;
			}
		}
	/**
	* set charset of data which will be stored in the database
	*@param string $dataType
	*@return boolean
	*/
		public function setDataType($dataType="utf8"){
			if ($dataType!="") {
				//verify if the charset is compatible with the collate
				if ($dataType!=str_replace('_general_ci','', $this->_collate)) {

					$this->_dataType=$dataType;
					$this->_collate=$dataType.'_general_ci';
					return true;
				}
				else{
						$this->_dataType=$dataType;
						return true;
				}
			}
			else{
					new DExceptions("dataType null for setDataType(dataType)", 405);
					return false;
			}

		}
	/**
	* set collate of data which will be stored in the database
	*@param string $collate
	*@return boolean
	*/
		public function setCollate($collate='utf8_general_ci'){
			if ($collate!="") {
				//verify if the collate is compatible with the charset

				if ($collate!=$this->_dataType.'_general_ci') {

					$this->_collate=$collate;
					$this->_dataType=str_replace('_general_ci', '', $collate);
				}
				else{
						$this->_collate=$collate;
				}
			}
			else{
					new DExceptions("collate null for setCollate(collate)", 405);
			}
		}
	/**
	* execute the query
	*
	*@return boolean
	*/
		public function execute(){
				if ($this->_exist) {

				$this->_sql.=' IF NOT EXISTS ';
				}
				if (is_string($this->_dbName)&&$this->_dbName!=null) {

					$this->_sql.=$this->_dbName;
				}
				else{

					if (DB_NAME!=null&&$this->_auto!=false) {

						$this->_dbName=DATABASE;
						$this->_sql.=$this->_dbName;
					}
					else{
							new DExceptions("dbName null for setName(dbName) or dbBuilder(dbName) or You haven't set DB_NAME before", 405);
							return false;
					}
				}
				$this->_sql.=' DEFAULT CHARACTER SET '.$this->_dataType;
				$this->_sql.=' COLLATE '.$this->_collate.';';
				try {
						$this->_req=parent::connect();
						$req=$this->_req->query($this->_sql);
						return $req;
				} catch (\PDOException $e) {
						new DExceptions($e->getMessage(),$getCode());
				}

			}
}

?>
