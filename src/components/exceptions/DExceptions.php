<?php
	namespace dcom\components\exceptions;
	use dcom\controllers\Controller;
	use Exception;
	class DExceptions extends Controller{
		const FOUND=404;
		const ALLOW=405;
		const FORBIDEN=403;
		public function __construct($msg,$code=''){
			if ($this->get('framework::config::run_mod')&&$this->get('framework::config::run_mod')=='dev') {
				$this->render('error/error',['msg'=>$msg],true);
				die();
			}
			if ($this->get('framework::config::error_view')) {
				$this->render($this->get('framework::config::error_view'),['msg'=>$msg]);
				die();
			}
			header('location:'.$this->get('root'));
		}
	}
?>
