<?php
namespace dcom;
use dcom\controllers\Controller;
use dcom\components\exceptions\DExceptions;
//error_reporting(0);
set_error_handler('dcom\components\exceptions\DExceptions::error_handler');
set_exception_handler('dcom\components\exceptions\DExceptions::exception_handler');
register_shutdown_function('dcom\components\exceptions\DExceptions::error_fatales_handler');
class Dcom extends Controller{
	protected $_controller=null;
	protected $_method=null;
	protected $_params=[];
	function __construct()
	{
      parent::__construct();
			if ($this->ready()){
				$routing=self::routing();
					if (!is_array($routing)) {
						new DExceptions('Routing : "'.$routing.'" not found');
					}

				if (isset($routing[0])&&$routing[0]!='') {
					$controller=self::className($routing[0]);
          if ($this->_controller=$this->get('user::'.$controller)) {
            if (isset($routing[1])&&$routing[1]!='') {
              $method=$routing[1];
              if (method_exists($this->_controller,$method)) {
                $this->_method=$method;
              }
              else{
                new DExceptions("Method ".$method.' canot be found in Class'.$controller,DExceptions::FOUND);
              }
            }
            else{
              $method='index';
              if (method_exists($this->_controller,$method)) {
								$this->_method=$method;
              }
              else{
                new DExceptions("Method ".$method.' canot be found in Class'.$controller,DExceptions::FOUND);
              }
            }
            $data=self::parseData();
						$this->set('request::data',$data);
						$this->params= $data ? array_values($data):[];
            call_user_func_array([$this->_controller,$this->_method],$this->params);
            return;
          }
          else{
            new DExceptions("Controller ".$controller.' canot be found',DExceptions::FOUND);
          }
				}
				else{
					return $this->render('default/index');
				}
			}
			else{
				return $this->render('conf/init',['title'=>'Configuration'],true);
			}
	}
	public function parseData(){
		if ($_SERVER['REQUEST_METHOD']=="GET") {
			$query=explode('&',$_SERVER['QUERY_STRING']);
			$i=0;
			$data='';
			foreach ($query as $key => $value) {
					$d=explode('=',$value);
					if($d[0]=='action'){
						unset($query[$i]);
					}else {
						$data[$d[0]]=str_replace('+',' ',$d[1]);
					}
					$i++;
				}
				return $data;
		}
		if ($_SERVER['REQUEST_METHOD']=="POST") {
			if(isset($_POST)){
	      return $_POST;
			}
		}
			return array();
	}
	public function parseUrl(){
		$query=explode('&',$_SERVER['QUERY_STRING']);
		foreach ($query as $key => $value) {
				if(explode('=',$value)[0]=='action'){
					$url= explode('=',$value)[1];
					return $url=explode('/',filter_var(rtrim($url,'/'),FILTER_SANITIZE_URL));
				}
			}
	}
	public static function className($arg){
		$var=str_split($arg);
		return preg_replace('#^[a-z]#i',strtoupper($var[0]), $arg);
	}
	public function routing(){
		$query=$_SERVER['REQUEST_URI'];
		if (!preg_match('#/$#',$query)) {
				$query.='/';
		}
		if (isset($_SERVER['BASE'])) {
			$query=str_replace($_SERVER['BASE'],'',$query);
		}
		$routing=$this->get('framework::routing');
		$path=explode('/',$query);
		foreach ($routing as $key => $value) {
				if (isset($value['path'])&&$value['path']=='/'.$path[1]) {
					if (isset($value['components'])&&is_array($value['components'])&&count($value['components'])!=0) {
						foreach ($value['components'] as $key => $val) {
							if (isset($val['path'])&& isset($path[2])&&$val['path']=='/'.$path[2]) {
								if (isset($val['action'])) {
									return explode('::',$val['action']);
								}
							}
						}
					}
					if (isset($value['action'])) {
						return explode('::',$value['action']);
					}
			}
		}
		return $query;

	}
}
