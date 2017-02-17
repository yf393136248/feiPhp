<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-10
 * Time: 下午2:05
 */

class Helper  {
    /**
     * 过滤内容:  $data : post.|post.age
     * $func : 过滤函数,通过使用","拼接的字符串
     * @param $data
     * @param $default
     * @param $func
     */
    public static  function I($data, $default = null, $func = null)
    {
        $filter_func    =   array();
        if(is_null($func)) {
            $func = self::C('defaultFilterFunc');
            is_null($func) && $func = 'htmlspecialchar,trim';
        }
        $filter_func   =   array_filter(explode(',', $func));
        $request_method =   $data;
        $request_allow_method   =   array('get', 'post', 'put', 'delete');
        $filter_field   =   null;
        $filter_result  =   null;
        if(false !== strpos($data, '.')) {
            $data_explode   =   explode('.', $data);
            $request_method =   $data_explode[0];
            !empty($data_explode[1])  &&   $filter_field   =   $data_explode[1];
        }
        if(!in_array($request_method, $request_allow_method))  return $default;
        //没有设置需要过滤的字段,就需要过滤请求的所有字段
        switch ($request_method) {
            case 'get':
                $filter_result  =   $_GET;
                break;
            case 'post':
                $filter_result  =   $_POST;
                break;
            default:
                break;
        }
        foreach ($filter_func as $key => $val) {
            $filter_result  =   self::_filterDataByFunc(is_null($filter_field) ? $filter_result : $filter_result[$filter_field], $val);
        }
        return !$filter_result ? $default : $filter_result;
    }

    /**
     * 获取配置内容, 如果val存在,那么可以赋值
     * @param $key  配置键
     * @param array $val
     */
    public static function C($key, $val = null)
    {
        if(file_exists(APP_PATH . 'config/config.php')) {
            $config_array   =   include APP_PATH . 'config/config.php';
            if(is_null($val)) {
                if(array_key_exists($key, $config_array)) {
                    return $config_array[$key];
                }else{
                    $web_config =   @$GLOBALS['webConfig'];
                    if(isset($web_config[$key])) {
                        return $web_config[$key];
                    }
                    return null;
                }
            }else{
                //对配置赋值
                $GLOBALS['webConfig'][$key] =   $val;
                return true;
            }
        }
    }

    private static function _filterDataByFunc($data, $func)
    {
        if(is_array($data)) {
             array_walk($data, function(&$val, $key, $_func){
               $val =   self::_filterDataByFunc($val, $_func);
            }, $func);
            return $data;
        }else{
            return call_user_func($func, $data);
        }
    }
}