<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-3
 * Time: 上午11:38
 */
class Sql{
    protected $_dbHandle;
    protected $_result;
    protected $filter =   '';
    protected $_table   =   "";

    public function connect($host, $user, $password, $dbName)
    {
        try{
            $dsn    =   sprintf("mysql:host=%s;dbname=%s;charset=utf8", $host, $dbName);
            $option =   array(PDO::ATTR_DEFAULT_FETCH_MODE =>  PDO::FETCH_ASSOC, PDO::ATTR_PERSISTENT => true);
            $this->_dbHandle    =   new PDO($dsn, $user, $password, $option);
        }catch (PDOException $e) {
            exit('数据库连接错误:'. $e->getMessage());
        }
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->_dbHandle->beginTransaction();
    }

    /**
     * 事务提交
     */
    public function commit(){
        $this->_dbHandle->commit();
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->_dbHandle->rollback();
    }
    /**
     * @param string $where mixed where条件,可以是键值对的数组也可以是字符串类型的原生数组
     * @return $this
     */
    public function where($where = '')
    {
        if(!empty($where)) {
            $this->filter   .= ' WHERE ';
            if(is_array($where)) {
                $separator  =   '';
                foreach ($where as $key => $val) {
                    $this->filter   .=  ($separator . $key . '='. $val);
                    $separator  =   ' AND ';
                }
            }elseif(is_string($where)) {
                $this->filter   .=  $where;
            }
        }
        return $this;
    }
    /**
     * @param string $order mixed order条件,可以是键值对的数组也可以是字符串类型的原生数组
     * @return $this
     */
    public function order($order =  '')
    {
        if(!empty($order)) {
            $this->filter   .=  'ORDER BY';
            if(is_array($order)) {
                $separator  =   '';
                foreach ($order as $key => $val) {
                    $this->filter  .=  ($separator . $key . $val);
                    $separator  =   ' , ';
                }
            }
            return $this;
        }
    }

    public function select()
    {
        $sql    =   sprintf("SELECT * FROM  `%s` %s", $this->_table, $this->filter);
        $sth    =   $this->_dbHandle->prepare($sql);
        $sth->execute();
        return $sth->fetchAll();
    }

    public function find($id)
    {
        $sql    =   sprintf("SELECT * FROM  `%s` WHERE `id` = '%s'", $this->_talbe, $id);
        $sth    =   $this->_dbHandle->prepare($sql);
        $sth->execute();
        return $sth->fetch();
    }

    public function delete($id)
    {
        $sql    =   sprintf("DELETE FROM `%s` WHERE  `id` = '%s'", $this->_table, $id);
        $sth    =   $this->_dbHandle->prepare($sql);
        $sth->execute();
        return $sth->rowCount();
    }

    public function add($data)
    {
        $sql    =   sprintf("INSERT INTO `%s` %s", $this->_table, $this->_formatInsert($data));
        return $this->query($sql);
    }

    public function update($data) {
        $sql    =   sprintf("UPDATE `%s` SET %s %s", $this->_formatUpdate($data), $this->filter);
        return $this->query($sql);
    }

    /**
     * 格式化插入数据
     * @param $data
     * @return string
     */
    private function _formatInsert($data)
    {
        $fields =   array();
        $values =   array();
        foreach ($data as $key => $val){
            $fields[]   =   sprintf("`%s`", $key);
            if(is_array($val)) $values  =   $val;
            if(is_string($val)) $values[]   =   sprintf("'%s'", $val);
        }
        $fields =   implode(',', $fields);
        if(is_array($values)) {
            $values =   '"' . implode('"),("', $values) . '"';
        }else{
            $values =   implode(',', $values);
        }
        return sprintf("(%s) VALUES (%s)", $fields, $values);
    }

    private function _formatUpdate($data)
    {
        //$separator  =   "";
        $format_data    =   array();
        //这是第一种写法,下面的是第二种写法!
        //foreach ($data as $key => $val) {
        //    $format_data    .=  ($separator . sprintf("`%s`", $key) . '=' . sprintf("'%s'", $val));
        //    $separator  =   " , ";
        //}
        foreach ($data as $key => $val){
            $format_data[]  =   sprintf("`%s` = '%s'", $key, $val);
        }
        $format_data    =   implode(',', $format_data);
        return $format_data;
    }

    public function query($sql)
    {
        /*$sth    =   $this->_dbHandle->prepare($sql);*/
        /*echo $sql;
        $this->_dbHandle->exec($sql);
        var_dump(debug_backtrace());
        debug_print_backtrace();
        file_put_contents('/home/debug', var_export([__CLASS__,microtime(),debug_backtrace()],true),FILE_APPEND);
        echo $this->_dbHandle->lastInsertId();*/
        $stmt = $this->_dbHandle->prepare($sql);
        $stmt->execute();
        echo $stmt->rowCount(); //查询中受影响(改动)的行数,插入失败时为0
        echo $this->_dbHandle->lastInsertId(); //插入的自增ID,插入失败时为0
         /*$sth->rowCount();*/
    }
}