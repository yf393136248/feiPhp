<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-3
 * Time: 上午10:35
 */
class View{
    protected $_module;
    protected $_controller;
    protected $_action;
    protected $_values  =   array();
    public function __construct($module, $controller, $action)
    {
        $this->_module  =   $module;
        $this->_controller  =   $controller;
        $this->_action  =   $action;
    }

    /**
     * 视图模板变量赋值
     * @param $name array | string  赋值名称 也可以是键值对的数组
     * @param $value mixed 赋值的值
     */
    public function assign($name, $value = '')
    {
        if(is_array($name)) {
            foreach ($name as $key => $val) {
                $this->_values[$key]    =   $val;
            }
        }else{
            $this->_values[$name]   =   $value;
        }
    }

    public function render($name = '')
    {
        extract($this->_values);
        $view_content   =   file_get_contents(APP_PATH . 'application/' . $this->_module . '/views/' . $this->_controller . '/' . $this->_action . '.html');
        $matches    =   array();
        preg_match_all('/\{\$(\w+)\}/', $view_content, $matches);
        $view_content   =   preg_replace_callback('/\{\$(\w+)\}/', function($matches){
            if(isset($this->_values[$matches[1]])) {
                return   $this->_values[$matches[1]];
            }
        }, $view_content);
        echo $view_content;exit(0);
    }
}