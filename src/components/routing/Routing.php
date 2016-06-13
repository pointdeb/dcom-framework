<?php
  /**
   *
   */
   namespace dcom\components\routing;
   use dcom\controllers\Controller;
   use RecursiveIteratorIterator;
   use RecursiveDirectoryIterator;
   use RecursiveRegexIterator;
   use RegexIterator;

   use VKBansal\FrontMatter\Parser;
   use VKBansal\FrontMatter\Document;
  class Routing extends Controller
  {
    private $_is_ready=false;
    public $_routing = array();
    function __construct($base_dir)
    {
      if (isset($base_dir)&&$base_dir!=='') {
        $routing_dir=$base_dir.'\app\routing';
        $routing_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routing_dir));
        $routing_files = new RegexIterator($routing_files,'/^.+\.yml$/i',RecursiveRegexIterator::GET_MATCH);
        foreach ($routing_files as $file) {
          $this->_routing= array_merge_recursive($this->_routing,Parser::parse(file_get_contents($file[0]))->getConfig());
        }
        return true;
      }
      echo 'canot find your base_dir \n';
    }
    public function getRouting(){
        return $this->_routing;
    }
    /*
    public function is_ready(){
      if (
      $this->get('framework::config::run_mod')          &&
      $this->get('framework::config::database::server') &&
      $this->get('framework::config::database::host')   &&
      $this->get('framework::config::database::user')   &&
      $this->get('framework::config::database::dbname')
      ) {
            $this->_is_ready=true;
      }else{
        $this->_is_ready=false;
      }
      return $this->_is_ready;
    }
    public static function setConf($array=array()){
      $file=base_dir.'\\app\\'.self::$_conf_file;
      if ((is_file($file)||$f=fopen($file,'wa+'))) {
fwrite($f,"---
run_mod: product
layout:
  engine: 'html'
database:
  server: 'mysql'
  host  : '".$array['host']."'
  user  : '".$array['user']."'
  pass  : '".$array['pass']."'
  dbname: '".$array['dbname']."'
---
---");
        fclose($f);
        return true;
      }
      return false;
    }*/
  }

 ?>
