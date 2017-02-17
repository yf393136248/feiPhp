<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-10
 * Time: 下午5:28
 */
class Cache {
    protected  $cacheMethod   =   'File';
    protected  $fp    =   null;
    protected  static $_instance    =   null;

    public  function __construct($method =   null)
    {
        is_null($method)  &&  self::$cacheMethod  =  $method;
    }

    public static function getInstance($method = null)
    {
        if(is_null(self::$_instance)) {
            return new self($method);
        }else{
            return self::$_instance;
        }
    }

    public  function set($key, $val)
    {
        $cache_path =   RUNTIME_PATH . '/cache/';
        switch ($this->cacheMethod) {
            case 'File':
                if(!is_dir($cache_path)) {
                    mkdir($cache_path, '0755');
                }
                $file_name  =   '~run_cache' .md5(date('Y-m-d')) . '.txt';
                if(!file_exists($cache_path . $file_name)) {
                    //ToDo 写入文件的内容,追加!
                }
                break;
        }
    }
}