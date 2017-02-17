<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-9
 * Time: 下午1:54
 */
class RedisSession implements SessionHandlerInterface{
    private $_options   =   array(
        'handler'   =>  null, //redis连接句柄
        'host'      =>  null,
        'port'      =>  null,
        'password'  =>  null,
        'lifeTime'  =>  null,
        'db'        =>  1
    );

    private static $_instance  =   null;

    public function __construct(array $options  =   array())
    {
        if( !class_exists('redis', false)) {
            die('您必须安装redis扩展');
        }
        foreach ($options as $key => $val) {
            if(array_key_exists($key, $this->_options))  {
                $this->_options[$key] =   $val;
            }
        }
        if(is_null($this->_options['lifeTime']) || $this->_options['lifeTime'] < 0) {
            $this->_options['lifeTime'] =   ini_get('session.gc_maxlifetime');
        }
    }

    public static function getInstance(array $options   =   array())
    {
        if(is_null(self::$_instance)) {
            return new self($options);
        }else{
            return self::$_instance;
        }
    }

    public function begin()
    {
        if(is_null($this->_options['host']) || is_null($this->_options['port']) || is_null($this->_options['lifeTime'])) {
            return false;
        }
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
    }

    /**
     * 自动开始会话或者session_start()开始会话后第一个调用的函数
     * @param string $save_path 默认的保存路径
     * @param string $name  默认的参数名PHPSESSID
     */
    public function open($save_path, $name)
    {
        if(is_resource($this->_options['handler'])) return true;
        $redisConn  =   new \Redis();
        $redisConn->connect($this->_options['host'], $this->_options['port']);
        if(!is_null($this->_options['password'])) {
            $redisConn->auth($this->_options['password']);
        }
        $redisConn->select($this->_options['db']);
        if(!$redisConn) {
            return false;
        }
        $redisConn->select($this->_options['db']);
        $this->_options['handler']  =   $redisConn;
        $this->gc(null);    //到底要不要运行这个方法? 连接成功后就初始化回收垃圾池
        return true;
    }

    public function close()
    {
        return $this->_options['handler']->close();
    }

    public function read($session_id)
    {
        echo $session_id;
        return $this->_options['handler']->get($session_id);
    }

    public function write($session_id, $session_data)
    {
        echo $this->_options['lifeTime']. ':lift-time';
        return $this->_options['handler']->setex($session_id, $this->_options['lifeTime'], "PHPREDIS_SESSION:". $session_data);
    }

    public function destroy($session_id)
    {
        return $this->_options['handler']->delete($session_id) >= 1 ? true : false;
    }

    public function gc($maxlifetime)
    {
        //获取所有的sessionid, 让过期的释放掉
        $this->_options['handler']->keys('*');
        return true;
    }
}