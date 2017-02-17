<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-1-21
 * Time: 上午11:32
 */
defined('DB_NAME')  or define('DB_NAME', 'yefei');
defined('DB_USER') or define('DB_USER', 'root');
defined('DB_HOST') or define('DB_HOST', '192.168.1.127');
defined('DB_PASSWORD') or define('DB_PASSWORD', '123456');

return array(
    "defaultFilterFunc" =>  'htmlspecialchar, trim',
);