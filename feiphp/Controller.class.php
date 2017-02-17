<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-3
 * Time: 上午10:30
 */
class  Controller{
    protected $_module;
    protected $_controller;
    protected $_action;
    protected $_view;

    /**
     * Controller constructor. 构造函数,
     * @param $module string  模块名称
     * @param $controller string 控制器名称
     * @param $action string 方法名称
     */
    public function __construct($module, $controller, $action)
    {
        $this->_module  =   $module;
        $this->_controller  =   $controller;
        $this->_action  =   $action;
        $this->_view    =   new View($module, $controller, $action);    //试图渲染实例化类!
    }

    /**
     * 模板赋值
     * @param $name string | array 赋值名称
     * @param $value  string | array 赋值的值
     */
    public function assign($name, $value)
    {
        $this->_view->assign($name, $value);
    }

    /**
     * 视图文件渲染
     * @param string $name 需要渲染的名称
     */
    public function render($name = '')
    {
        $this->_view->render($name);
    }

    /**
     * 返回数据
     * @param string $type  返回数据类型,默认是json格式
     * @param $data
     */
    public function returnData($data, $type = 'json')
    {
        if($type == 'json') {
            if(!is_array($data)) {
                $data   =   array($data);
            }
            echo json_encode($data);
            exit();
        }
    }
}