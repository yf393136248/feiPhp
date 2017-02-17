<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-1-21
 * Time: 下午2:18
 */

class IndexController extends Controller{
    public function index()
    {
        $options    =   array(
            'host'  =>  'localhost',
            'port'  =>  '6379'
        );
        /*$session_handler    =   RedisSession::getInstance($options)->begin();
        ini_set("session.save_handler", 'redis');
        ini_set("session.save_path", "tcp://localhost:6379");
        session_start();
        $_SESSION['age']    =   15;*/

        $user_model =   new UserModel();
        $user_list  =   $user_model->select();
        /*var_dump($res);*/
        $this->assign('content', $user_list[0]['name']);
        $this->assign('title', $user_list[1]['name']);
        $this->render();
    }
    public function demo()
    {
        $config =   array(
            'width' => 120,
            'height' => 30
        );
        $code   =   isset($_GET['code']) ? $_GET['code'] : '';
        if($code){
            var_dump(VerifyCode::verify($code));
        }else{
            VerifyCode::genCode($config);
        }
    }

    public function spider()
    {
        //curl模块
        $options    =   array(
            "url"   =>  "http://www.baidu.com",
            'autoReferer'   =>  0,
            "noBody"    =>  0,
            "returnTransfer"    =>  1,
            /*"header"    =>  array(
                "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36"
            )*/
        );
        $curlModule  =  CurlCore::getInstance($options);
        $res    =   $curlModule->fetch();
        $curlModule->saveFile("http://www.baidu.com/img/bd_logo.png");
    }

    public function test()
    {
        var_dump(Helper::I('get.age', array(), 'intval'));
    }
}
