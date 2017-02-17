<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-9
 * Time: 下午5:34
 */
class CurlCore {
    private $_ch    =   null;
    private $_options   =   array(
        "url"  =>  null,
        "requestMethod"    =>  "GET",
        "noBody" =>  0,
        "headerOut" =>  0,
        "autoReferer"   =>  1,
        "returnTransfer"    =>  0,
        "followLocation"    =>  1,
        "timeOut"       =>  60,
        "postField"     =>  array(),
        "header"        =>  array()
    );
    private $_config    =   array();
    private static $_instance   =   null;
    public function __construct(array $options)
    {
        if(!function_exists('curl_init')) {
            die('curl module not exist! please configure php modules!');
        }
        if(!isset($options['url'])) {
            return false;
        }
        $this->_options =   array_merge($this->_options, $options);
    }

    public static function getInstance(array $option = array())
    {
        if(!is_null(self::$_instance)  && self::$_instance instanceof self) {
            return self::$_instance;
        }else{
            return self::$_instance = new self($option);
        }
    }

    private function _setConfig($key, $val)
    {
        $this->_config[$key]    =   $val;
    }
    private function _getConfig($key)
    {
        return $this->_config[$key];
    }

    public function fetch()
    {
        $ch     =   curl_init($this->_options['url']);
        curl_setopt($ch, CURLOPT_POST, $this->_options['requestMethod'] == 'GET' ? 0 : 1);
        curl_setopt($ch, CURLOPT_NOBODY, $this->_options['noBody']);
        curl_setopt($ch, CURLOPT_HEADER, $this->_options['headerOut']);
        curl_setopt($ch, CURLOPT_AUTOREFERER, $this->_options['autoReferer']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->_options['returnTransfer']);
        //curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36");
        !empty($this->_options['header'])  && curl_setopt($ch, CURLOPT_HEADER, $this->_options['header']);
        !empty($this->_options['postField']) && curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->_options['postField']));
        $result =   curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function saveFile($fileName, $savePath = "curlData/")
    {
        $savePath   =   APP_PATH . $savePath;
        $ch =   curl_init();
        if(!is_dir($savePath)) {
            mkdir($savePath);
        }
        $url_exp    =   explode('.', $fileName);
        $fp =   fopen($savePath. mt_rand(50, 5000) .'.'. array_pop($url_exp), 'w');
        curl_setopt($ch, CURLOPT_URL, $fileName);
        curl_setopt($ch, CURLOPT_HEADER, $this->_options['header']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->_options['followLocation']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->_options['returnTransfer']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_options['timeOut']);
        $res    =   curl_exec($ch);
        curl_close($ch);
        fwrite($fp, $res);
        fclose($fp);
        return true;
    }
}