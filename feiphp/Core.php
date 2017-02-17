<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-1-21
 * Time: 上午11:32
 */

class Core {

    public function run(){
        spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();
        $this->unRegisterGlobals();
        $this->removeMagicQuotes();
        $this->checkRequestMethod();
        $this->route();
    }
    //处理路由
    public function route()
    {
        $module_name    =   'Index';
        $controller_name    =   'Index';
        $action_name    =   'index';
        $param          =   array();
        $url    =   isset($_GET['url']) ? $_GET['url'] : false;
        //找到路由对应的控制器上
        if($url && trim($url) != '/'){
			//如果query_string开头已经含有了url了,那么就需要过滤去重,保证框架的使用!

            $url_array  =   explode('/', $url);
            $url_array  =   array_filter($url_array);
            $module_name    =   ucfirst($url_array[0]);
            array_shift($url_array);
            $controller_name    =   $url_array ? ucfirst($url_array[0]) : 'Index';
            array_shift($url_array);
            $action_name        =   $url_array ? strtolower($url_array[0]) : 'index';
            array_shift($url_array);
            $param              =   $url_array ? $url_array[0] : array();
        }
		//实例化该控制器
		$controller	=	$controller_name.'Controller';
		defined('CONTROLLER_NAME')  || define('CONTROLLER_NAME', $controller_name);
		defined('ACTION_NAME')  || define('ACTION_NAME', $action_name);
		defined('MODULE_NAME')  || define('MODULE_NAME', $module_name);
        $dispatch  =  new  $controller($module_name, $controller_name, $action_name);
        //判断当前请求的方法名称是否存在于请求的控制器之中!
        if((int)method_exists($dispatch, $action_name)) {
            $dispatch->$action_name($param);
        }else{
            throw new Exception('this method not exist in '.$controller);
        }
	}
    /**
     * 检测开发环境!
     */
    public function setReporting()
    {
        if(APP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        }else{
            error_reporting(E_ALL^E_NOTICE);
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', RUNTIME_PATH . 'logs/error.log');
        }
    }

    /**
     * 删除敏感字符
     */
    public function stripSlashesDeep($value)
    {
        $value =    is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    /**
     * 检测敏感字符并删除
     */
    public function removeMagicQuotes()
    {
        if(get_magic_quotes_gpc()) {
            $_GET   =   isset($_GET) ? $this->stripSlashesDeep($_GET) : '';
            $_POST  =   isset($_POST) ? $this->stripSlashesDeep($_POST) : '';
            $_COOKIE    =   isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION   =   isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    /**
     * 检测自定义全局变量(register globals)并移除
     */
    public function unRegisterGlobals()
    {
        if(ini_get('register_globals')) {
            $array  =   array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var){
                    if($var === $GLOBALS[$key]) {
                        //如果全局变量中含有请求体的局部变量,那么就需要注销掉这个在局部变量中的变量~
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    /**
     * 检查请求方式
     */
    public function checkRequestMethod()
    {
        define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
        define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
        define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
        define('IS_PUSH', REQUEST_METHOD == 'PUSH' ? true : false);
        define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
        define('IS_AJAX', REQUEST_METHOD == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false);
    }
    /**
     * 类自动加载注册使用!
     * @param $class
     * @throws Exception
     */
	public static function loadClass($class){
        $frameworks    =   FRAME_PATH . $class . '.class.php';
        $controllers    =   APP_PATH . 'application/Index/controllers/'. $class . '.php';
        $models         =   APP_PATH . 'application/Index/models/'. $class . '.php';
        $vendor_class   =   VENDOR_PATH . $class . '.php';
        //判断文件是否存在,存在就引入该文件!优先引入框架文件!
        if(file_exists($frameworks)) {
            include_once $frameworks;
        }elseif(file_exists($controllers)) {
            include_once $controllers;
        }elseif(file_exists($models)) {
            include_once $models;
        }elseif(file_exists($vendor_class)){
            include_once $vendor_class;
        }else{
            throw new Exception('this controller is not founded!');
        }
    }
}
