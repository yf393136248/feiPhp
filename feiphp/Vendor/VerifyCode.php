<?php
/**
 * Created by PhpStorm.
 * User: yefei
 * Date: 17-2-7
 * Desc: 图片验证码生成类
 * Time: 上午11:00
 */

class VerifyCode {
    protected static $code     =   '';
    protected static $config   =   array(
        'width'   =>  120,
        'height'  =>  45,
        'fontLength'    =>  5,
        'fontSize'      =>  14,
        'fontContent'   =>  'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789'
    );
    private static function init($config)
    {
        if(is_array($config)) {
            foreach ($config as $key => $value) {
                self::$config[$key] =   $value;
            }
        }else{
            throw Exception('构造函数参数必须是数组!');
        }
    }

    /**
     * 生成验证码,并返回
     */
    public static function genCode($config)
    {
        self::init($config);
        $config =   self::$config;
        ($img    =   @imagecreatetruecolor($config['width'], $config['height']) ) || die('您还未安装任何GD库扩展!');
        $black =   imagecolorallocate($img, 0x00, 0x00, 0x00);
        $green =   imagecolorallocate($img, 0x00, 0xFF, 0x00);
        $white =   imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
        imagefill($img, 0, 0, $white);
        $ttf_file   =   __DIR__ . '/1.ttf';
        imagettftext($img, 20, 15, 25,35, $black, $ttf_file, self::make());
        //加入噪点干扰;
        for ($i = 0; $i < 300; $i++) {
            imagesetpixel($img, rand(0, 100), rand(0, 100), $black);
            imagesetpixel($img, rand(0, 100), rand(0, 100), $green);
        }
        //加入线段干扰
        for ($n = 0; $n <= 1; $n++) {
            imageline($img, 0, rand(0, 40), 100, rand(0, 40), $black);
            imageline($img, 0, rand(0, 40), 100, rand(0, 40), $white);
            imageline($img, 0, rand(0, 40), 100, rand(0, 40), $green);
        }
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }

    /**
     * 生成验证码文字
     * @return string
     */
     public static function make()
     {
         $config =   self::$config;
         $font_content  =   str_shuffle($config['fontContent']);
         self::$code    =   substr($font_content, 0, $config['fontLength']);
         session_start();
         $_SESSION['verify_code']   =   self::$code;
         return self::$code;
     }

    /**
     * 校验用户输入验证码
     * @param $cusCode
     * @return boolean
     */
     public static function verify($cusCode)
     {
         session_start();
         self::$code    =   $_SESSION['verify_code'];
         if($cusCode == strtolower(self::$code)) {
             $_SESSION['verify_code']   =   NULL;
         }
         return $cusCode == strtolower(self::$code) ? true : false;
     }

}