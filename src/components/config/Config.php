<?php
  /**
   *
   */
   namespace dcom\components\config;
   use dcom\controllers\Controller;
   use RecursiveIteratorIterator;
   use RecursiveDirectoryIterator;
   use RecursiveRegexIterator;
   use RegexIterator;

   use VKBansal\FrontMatter\Parser;
   use VKBansal\FrontMatter\Document;
  class Config extends Controller
  {
    private $_is_ready=false;
    public $_config = array();
    function __construct($base_dir='')
    {
      if (isset($base_dir)&&$base_dir!=='') {
        $conf_dir=$base_dir.'\app\conf';
        $conf_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($conf_dir));
        $conf_files = new RegexIterator($conf_files,'/^.+\.yml$/i',RecursiveRegexIterator::GET_MATCH);
        foreach ($conf_files as $file) {
          $this->_config=array_merge_recursive($this->_config,Parser::parse(file_get_contents($file[0]))->getConfig());
        }
        return true;
      }
      //new DExceptions('canot find your base_dir ');
    }
    public function getConf(){
        return $this->_config;
    }
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
    public function setConf($array=array()){
      $file=base_dir.'\app\conf\config.yml';
      if ($f=fopen($file,'wa+')) {
fwrite($f,"---
run_mod: ".$array['run_mod']."
error_view: default/error
layout:
  engine: '".$array['engine']."'
database:
  server: 'mysql'
  host  : '".$array['host']."'
  user  : '".$array['user']."'
  pass  : '".$array['pass']."'
  dbname: '".$array['dbname']."'
---
---");
        fclose($f);
        header('location:'.$this->get('root'));
        return true;
      }
      return false;
    }
  }

 ?>
