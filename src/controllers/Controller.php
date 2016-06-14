<?php
  /**
   *
   */
   namespace dcom\controllers;
   use dcom\components\exceptions\DExceptions;
   use Tale\Jade;
   use RecursiveIteratorIterator;
   use RecursiveDirectoryIterator;
   use RecursiveRegexIterator;
   use RegexIterator;
   use dcom\components\config\Config;
   use dcom\components\routing\Routing;
   use dcom\components\database\Db;
   use dcom\components\database\DbBuilder;
   use dcom\components\database\DbConnector;
   use dcom\components\database\TabBuilder;
  class Controller
  {
    private static $_config;
    private static $_routing;
    private static $_count=0;
		private static $_app=[];
    private static $_jade;
    public $_version='1.0';
    function __construct()
    {
      if (!$this->get('framework::config')) {
        self::$_config  = new Config(base_dir);
        $this->set('framework::config',self::$_config->getConf());
      }
      if (!$this->get('framework::routing')) {
        self::$_routing  = new Routing(base_dir);
        $this->set('framework::routing',self::$_routing->getRouting());
      }
      if (!self::$_jade) {
        self::$_jade    = new Jade\Renderer(['pretty' => true]);
      }
    }
    public function ready(){
      if (!$this->get('framework::DbConnector') && self::$_config->is_ready()) {
          $this->set('framework::DbConnector',new DbConnector);
          $this->set('framework::DbBuilder',new DbBuilder);
          $this->set('framework::TabBuilder',new TabBuilder);
          $this->set('framework::Db',new Db);
      }
      if (base_dir) {
        $user_dir=base_dir.'\src\components';
        $user_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($user_dir));
        $user_files = new RegexIterator($user_files,'/^.+\.php$/i',RecursiveRegexIterator::GET_MATCH);
        foreach ($user_files as $file) {
          $content_user = str_replace(base_dir,'',$file);
          //require '..'.$content_user[0];
          $content_user = str_replace('.php','',$content_user);
          if (class_exists($content_user[0])) {
            $key = str_replace('\src\components\\','',$content_user);
            $this->set('user::'.$key[0],new $content_user[0]);
          }
        }
      }
      if(!self::$_config->is_ready()){
        return false;
      }
      return true;
    }
    public function render($view='default/index',$data=array(),$app=false){
        $layout_dir='';
        if ($app) {
          $layout_dir='../vendor/dcom/framework/src/views/';
        }else{
          $layout_dir='../src/views/';
        }
        $engine=$this->get('framework::config::layout::engine');
        if ($engine=='jade'||$app) {
          $file=$layout_dir.$view.'.jade';
          if (file_exists($file)) {
              $root='';
              if (isset($_SERVER['BASE'])) {
                $root=$_SERVER['BASE'];
              }
              $data['root']=$root;
              self::$_jade->addPath($layout_dir);
              echo self::$_jade->render($file,$data);
              return ;
            }
            die($view.'.jade not found');
        }
        if ($engine=='html') {
          $file=$layout_dir.$view.'.php';
          if (file_exists($file)) {
            extract($data);
            $root='';
            if (isset($_SERVER['BASE'])) {
              $root=$_SERVER['BASE'];
            }
            require $file;
            return ;
          }
          die($view.'.php not found');
        }
        new DExceptions('Please select your Layout engine');
    }
    public function get($key,$app=array()){
			$result=$this->getTabVal($key,$app);
      return $result;
		}
    public function set($key,$val){
      if ($value=$this->getTabVal($key)) {
        echo $key.' = '.$value.'<br>';
        return false;
      }
      self::$_app=array_merge_recursive($this->setTabVal($key,$val),self::$_app);
      return true;
    }
    public function getTabVal($key,$tableaux=array()){
        self::$_count++;
        $tab=explode('::',$key);
        $parent=$tab[0];
        unset($tab[0]);
        if (!$tableaux) {
          $tableaux=self::$_app;
        }
        /*if(self::$_count==1){
          echo $parent;
        }*/
        $child=implode('::',$tab);
        if (key_exists($parent,$tableaux)) {
          $result= $tableaux[$parent];
          if(is_array($result)){
            if(count($tab)!==0){
              return $this->getTabVal($child,$result);
            }
            self::$_count=0;
            return $result;
          }
          self::$_count=0;
          return $result;
    }
    return false;
  }
    private function setTabVal($key,$val){
        $tab=explode('::',$key);
        $parent=$tab[0];
        unset($tab[0]);
        $child=implode('::',$tab);
        if ($child) {
          $result[$parent]=$this->setTabVal($child,$val);
          return $result;
        }
        $result[$parent]=$val;
        return $result;
    }
  }

 ?>
