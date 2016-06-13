<?php
namespace dcom\components\database;
use dcom\controllers\Controller;
use dcom\components\exceptions\DExceptions;
use PDO;
class DbConnector extends Controller{
		private $_objPDO=null;
		private $_pdoBase=[];
		protected $_pdoOption=[
					PDO::ATTR_CASE               => PDO::CASE_NATURAL,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES   => true,
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
					PDO::ATTR_STRINGIFY_FETCHES  => false,
				];
		public function connect(){

			try{
				$this->_pdoBase=[
					'server'=>$this->get('framework::config::database::server'),
					'host'=>$this->get('framework::config::database::host'),
					'user'=>$this->get('framework::config::database::user'),
					'pass'=>$this->get('framework::config::database::pass'),
					'dbname'=>$this->get('framework::config::database::dbname')];
					$this->_objPDO=new PDO(
						$this->_pdoBase['server'].':host='.
						$this->_pdoBase['host'],
						$this->_pdoBase['user'],
						$this->_pdoBase['pass'],
						$this->_pdoOption);
			}
			catch(\Exception $e){
				new DExceptions($e->getMessage(),$e->getCode());
			}
			return $this->_objPDO;
		}
		public function __destruct(){
			$this->_objPDO=null;
		}
	}

?>
