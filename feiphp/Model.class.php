<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-3
 * Time: 下午2:47
 */
class Model extends Sql{
    protected $_model;
    protected $_table;

    public function __construct()
    {
        //连接数据库
        $this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $this->_model   =   get_class($this);
        //删除Model这几个字符:
        $this->_model   =   str_replace('Model', '', $this->_model);
        //数据库的名称和model名称保持一致
        $this->_table   =   strtolower($this->_model);
    }
}