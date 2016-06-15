<?php
	namespace dcom\components\exceptions;
	use dcom\controllers\Controller;
	use Exception;
	class DExceptions extends Controller{
		const FOUND=404;
		const ALLOW=405;
		const FORBIDEN=403;
		public function __construct($msg='',$code=''){
				if ($msg!='') {
					$this->showError($msg,$code);
				}
		}
	public function showError($msg,$code){
		$log = fopen('../app/logs/error.log', 'a+');
		fwrite($log, $msg . "\n");
		fclose($log);
			if ($this->get('framework::config::run_mod')&&$this->get('framework::config::run_mod')=='dev') {
				$this->render('error/error',['title'=>'Error'],true);
				die('<p class="bg-danger">'.$msg.'</p>');
			}
			if ($this->get('framework::config::error_view')) {
				$this->render($this->get('framework::config::error_view'),['title'=>'Error','msg'=>$msg]);
				die() ;
			}
			header('location:'.$this->get('root'));
		}
	public static function error_handler($type, $message, $file, $line)
			{
				switch ($type)
				{
					case E_ERROR:
					case E_PARSE:
					case E_CORE_ERROR:
					case E_CORE_WARNING:
					case E_COMPILE_ERROR:
					case E_COMPILE_WARNING:
					case E_USER_ERROR:
						$type_erreur = "Fatal Error";
						break;

					case E_WARNING:
					case E_USER_WARNING:
						$type_erreur = "Warning";
						break;

					case E_NOTICE:
					case E_USER_NOTICE:
						$type_erreur = "Notice";
						break;

					case E_STRICT:
						$type_erreur = "Syntax Error";
						break;

					default:
						$type_erreur = " Unknow Error";
				}

				$erreur = '[ '.date("d.m.Y H:i:s") . ' ] ' . $type_erreur.' : ' . $message . ' in line ' . $line . ' (' . $file . ')';
				new DExceptions($erreur);
			}
	public static function exception_handler($exception)
			{
				error_handler(E_USER_ERROR, $exception->getMessage(), $exception->getFile(), $exception->getLine());
			}

	public static function error_fatales_handler()
			{
				if (is_array($e = error_get_last()))
				{
					$type = isset($e['type']) ? $e['type'] : 0;
					$message = isset($e['message']) ? $e['message'] : '';
					$file = isset($e['file']) ? $e['file'] : '';
					$line = isset($e['line']) ? $e['line'] : '';

					if ($type > 0) self::error_handler($type, $message, $file, $line);
				}
			}
	}
?>
