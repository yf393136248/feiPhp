<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-1-21
 * Time: 上午11:26
 */
defined('FRAME_PATH') or define('FRAME_PATH', __DIR__ . '/');   //框架目录
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');  //项目目录
defined('APP_DEBUG') or define('APP_DEBUG', true);  //是否开启调试模式
defined('CONFIG_PATH') or define('CONFIG_PATH', APP_PATH . 'config/');   //配置文件的目录
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH .  'runtime/');  //临时执行缓存文件的目录
defined('VENDOR_PATH') or define('VENDOR_PATH', FRAME_PATH . 'Vendor/'); //引入文件路径

require CONFIG_PATH . 'config.php';
//包含核心框架类
require FRAME_PATH . 'Core.php';

//实例化核心框架类
$core   =   new Core();
$core->run();