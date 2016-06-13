<?php
	namespace dcom\components\database;
	use dcom\controllers\Controller;
	use dcom\components\database\DbBuilder;
	use dcom\components\exceptions\DExceptions;
    class Db extends Controller{
		/**
		*database name
		*@var string $_dbname
		*/
			private $_dbName=null;
		/**
		* charset of data which will be stored in the database
		*@var string $_dataType
		*/
			private $_data=null;
		/**
		* sql query command
		*@var string $_req pdo object
		*/
			private $_req;

		/**
		* dbBuilder construnctor
		*@return void
		*
		*/
		public function __construct($dbName=null){
			    try{
							$this->_req=$this->get('framework::DbConnector')->connect();
							$this->_dbName=$this->get('framework::config::database::dbname');
							if ($this->_dbName) {
									$this->_req->exec('USE '. $this->_dbName);
							}
							else{
								new DExceptions("Canot select any database");
							}
	        	}
	        catch(\PDOException $e){
	        		new DExceptions($e->getMessage(),$e->getCode());
	        }
		}
		public function __destruct(){

		}
		public function createDb($dbName=null,$dataType="utf8",$exist=true,$collate="utf8_general_ci"){
					return $this->_req=new dbBuilder($dbName,$dataType,$exist,$collate);
		}
		public function delete(){
		        	$query=parent::prepare('DELETE FROM '.$this->_data['tabName'].' '.$this->_data['more'].'');
					$query->execute($this->_data['donnee']);
        }
       public function insert($tabName=null,$cols = array()){
        		$this->_data['tabName']=$tabName;
        		$this->_data['nbrChamp']='';
        		$this->_data['champs']='';
        		if (is_array($cols)) {
	        		$i=count($cols);
	        		$this->_data['donnee']=$cols;
	        		foreach ($cols as $key => $value) {
	        			$i--;
	        			$this->_data['champs'].=$key;
	        			$this->_data['nbrChamp'].=':'.$key;
	        			if ($i>0) {
		        			$this->_data['champs'].=',';
		        			$this->_data['nbrChamp'].=',';
	           			}
	        		}
					try {
						$req=$this->_req->prepare('INSERT INTO '.$this->_data['tabName'].' ('.$this->_data['champs'].') VALUES('.$this->_data['nbrChamp'].') ');
						$req->execute($this->_data['donnee']);
					}
					catch (\Exception $e) {
							new DExceptions($e->getMessage(),$e->getCode());
					}
        		}
        		else{
        			new DExceptions('argument error , db::insert($tabName,$cols=array()) ', 405);
        		}
       	}
        public function select($tabName,$selectField='*',$data=array()){
        			$this->_data['tabName']=$tabName;
        			$this->_data['selectField']=$selectField;
        			if (isset($data['more'])) {
        			$this->_data['more']=$data['more'];
        			}
        			else{
        				$this->_data['more']=null;
        			}
        			if (isset($data['donnee'])) {
        			$this->_data['donnee']=$data['donnee'];
        			}
        			else{
        				$this->_data['donnee']=array();
        			}
				  	try {
		          			$req=$this->_req->prepare('SELECT '.$this->_data['selectField'].' FROM '.$this->_data['tabName'].' '.$this->_data['more'].'');
										$req->execute($this->_data['donnee']);
										$return=null;
										while($data=$req->fetch()){
											$return[]=$data;
										}
				  					return $return;
							}catch (\Exception $e) {
									new DExceptions($e->getMessage(),$e->getCode());
							}
        }
        public function update($tabName,$selectField,$data){
        			$this->_data['tabName']=$tabName;
        			$this->_data['selectField']=$selectField.'=?';
        			$this->_data['more']=$data['more'];
        			$this->_data['donnee']=$data['donnee'];
					try {
        					$req=$this->_req->prepare('UPDATE '.$this->_data['tabName'].' SET '.$this->_data['selectField'].' '.$this->_data['more'].'');
									$req->execute($this->_data['donnee']);
									return $req;
					}catch (\Exception $e) {
						new DExceptions($e->getMessage(),$e->getCode());
					}
        }
		public function custom($arg,$dbName=null){
			if ($dbName==null) {
				# code...
				if (DB_NAME!=null) {
					# code...
					$this->_data['dbName']=DB_NAME;
				}
				else{
					echo 'DB_NAME null';
				}
			}
			else{
				$this->_data['dbName']=$dbName;
			}
			try {
					$this->_req->exec('USE '.$this->_data['dbName']);
					$this->_req->exec($arg);
					return $this->_req;

			} catch (\PDOException $e) {
					new DExceptions($e->getMessage(),$e->getCode());
			}
		}

	}
?>
