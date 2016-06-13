<?php
namespace dcom\components\database;
use dcom\controllers\Controller;
use dcom\components\exceptions\DExceptions;
	class TabBuilder extends Controller{
		/**
		*database name
		*@var string $_dbName
		**/
		private $_dbName=null;
		/**
		*sql query command
		*@var string $_dbName
		**/
		private $_sql='CREATE TABLE ';
		private $_req=null;
		/**
		*table name
		*@var string $_tabName
		**/
		private $_tabName=null;
		/**
		*table engine, default InnoDB
		*@var string $_engine
		**/
		private $_engine=null;
		/**
		* enable sql IF NOT EXISTS option
		*@var boolean $_exist
		*/
		private $_exist=null;
		/**
		*column of table
		*@var array $_cols
		**/
		private $_cols=null;
		/**
		*constraint sql pk or unique or foreing key
		*@var array $_constraint
		**/
		private $_constraint=null;
		/**
		*tabBuilter construct
		*@param string $tabName
		*@param boolean $exist
		*@param string $dbName
		*@param string $engine
		**/
		/*public function __construct($tabName=null,$exist=false,$dbName=DB_NAME,$engine='InnoDB'){
			$this->_tabName=$tabName;
			$this->_dbName=$dbName;
			$this->_engine=$engine;
			$this->_exist=$exist;
		}*/
		/**
		*set name of table will be created
		*@param string $tabName
		**/
		public function setName($tabName=null){
			//test tabName null or not,
			if ($tabName!=null) {

				$this->_tabName=$tabName;
				return true;
			}
			else{
				new DExceptions("dbName null for setName(dbName)", 405);
				return false;
			}
		}
		/**
		*add colone in the table
		*@param string $colName
		*@param string $colType
		*@param string $colLenght
		*@param int $colLenght
		*@param boolean $null
		*@param boolean $increment
		*@param string $colDefault
		**/
		public function addCol($colName,$colType="INT",$colLenght=10,$null='null',$increment='nAi',$colDefault=null){
				$this->_cols[$colName]= array(
						'_colType' =>$colType ,
						'_colLenght' =>$colLenght ,
						'_null' =>$null ,
						'_inc' =>$increment,
						'_colDefault' =>$colDefault,
					);
		}
		/**
		*set constraint of table like primary key or foreing key or unique
		*@param string $colName
		*@param string $constType
		*@param array $option specify the required option of foreing key
		**/
		public function setConst($colName,$constType,array $option=array('ref_tab'=>null,'ref_col'=>null)){
				$this->_constraint['type']=$constType;
				switch ($this->_constraint['type']) {
					case 'pk':
						//set constraint name like pk_tableName;
						$this->_constraint['name']='pk_'.$this->_tabName;
						//set colomn wich are the constraint will be attribute ;
						$this->_constraint['col']=$colName;
						break;
					case 'frng':
						//set constraint name like frng_tableName;
						$this->_constraint['name']='frng_'.$this->_tabName;
						//set colomn wich are the constraint will be attribute ;
						$this->_constraint['col']=$colName;
						//verify if required case of foreing key exist: referent table and the referent column
						if (($option['ref_tab']!=null)&&($option['ref_col']!=null)) {

							$this->_constraint['ref_tab']=$option['ref_tab'];
							$this->_constraint['ref_col']=$option['ref_col'];
						}
						break;
					case 'u':
						//set constraint name like u_tableName;
						$this->_constraint['name']='u_'.$this->_tabName;
						//set colomn wich are the constraint will be attribute ;
						$this->_constraint['col']=$colName;
						break;

					default:
						//default send error if none of those is checked, cause table always have constraint;
						new DExceptions("set name of collumn as primary key for setConst($col,$constType)", 405);
						break;
				}
		}
		public function execute(){
				//check the 1st option after CREATE TABLE and add if true,
				if ($this->_exist) {

					$this->_sql .= ' IF NOT EXISTS ';
				}
				//check the 3th option after CREATE TABLE and add table name if not null,
				if ($this->_tabName!=null) {
					$this->_sql.=$this->_tabName.' (';
				}
				else{
					new DExceptions("tabName null for setName(tabName)", 405);
				}
				//check the 4th option after CREATE TABLE and add column name  if not null,
				foreach ($this->_cols as $colName => $col) {
					// give colName INT(10);
			            $this->_sql.=$colName.' '.$col['_colType'].'('.$col['_colLenght'].') ';
			            //check not null option,
			            if ($col['_null']==='nNull') {

			            	$this->_sql .='NOT NULL ';
			            }
			            //check the default values of column
			            if ($col['_colDefault']!=null) {

			            	$this->_sql .="DEFAULT '".$col['_colDefault']."' ";
			            }
			            //check auto increment or not
			            if ($col['_inc']==='ai') {
			            	//only Int can be auto increment
			            	if ($col['_colType']==='int'||$col['_colType']==='INT') {

			            		$this->_sql .=" AUTO_INCREMENT ";
			            	}
			            	else{
			            		$this->_error='Increment imposible for not INT';
			            		new DExceptions("Increment not possible for not int at setConst(constType) ", 405);

			            	}
			            }
			            //limit for each column
			           		$this->_sql .=', ';
			    }
				//$this->_sql=preg_replace("#, $#","",$this->_sql);
			    //check if constraint exist and add constraint to the sql command
				if (isset($this->_constraint['name'])) {

					$this->_sql.='CONSTRAINT '.$this->_constraint['name'].' ';
				}
				//check if constraint type and column name are set,
				if (isset($this->_constraint['type'])&&isset($this->_constraint['col'])) {

					switch ($this->_constraint['type']) {
						case 'pk':
							//give PRIMARY KEY (colName)
							$this->_sql.='PRIMARY KEY ('.$this->_constraint['col'].')';
							break;
						case 'frng':
							//give FOREING KEY (colName) REFERENCES rbef_ta(ref_col)

							$this->_sql.='FOREING KEY ('.$this->_constraint['col'].') REFERENCES '.$this->_constraint['ref_tab'].' ('.$this->_constraint['ref_col'].')';
							break;
						case 'u':
							//give UNIQUE (colName)

							$this->_sql.='UNIQUE ('.$this->_constraint['col'].')';
							break;

						default:
							//default send error if none of those is checked, cause table always have constraint;
							new DExceptions("set name of collumn as primary key for setConst($col,$constType)", 405);
							break;
					}
				}
				//give ) ENGINE = engine name like InnoDB;
				$this->_engine='InnoDB';
				$this->_sql.=') ENGINE='.$this->_engine." AUTO_INCREMENT=0;";
			//conncet to server
				try{
						$this->_req=$this->get('framework::DbConnector')->connect();
						//select db to use
						$this->_req->query('USE '.strtolower($this->get('framework::config::database::dbname')));
						//ececute query
						//print_r($this->_sq);
						$this->_req->query($this->_sql);
						$this->reset();

				}
				catch(\PDOException $e){
						new DExceptions($e->getMessage(),$e->getCode());
				}
		}
		private function reset(){
			$this->_sql='CREATE TABLE ';
			$this->_req=null;
			$this->_tabName=null;
			$this->_exist=true;
			$this->_cols=null;
			$this->_constraint=null;
		}
		public function __destruct(){
			$this->reset();
		}
	}
?>
