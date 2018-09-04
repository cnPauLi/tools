<?php
// +----------------------------------------------------------------------
// | 用户中心类库依赖的静态方法支持
// +----------------------------------------------------------------------
// | 数据处理的方法，包括字段验证处理
// +----------------------------------------------------------------------
// | 2016-6-14
// +----------------------------------------------------------------------
// | Author: 雷震子 
// +----------------------------------------------------------------------
namespace Tool\Utils;

use Yii;


class UtilValidate
{
    /**
     * 校验是否在规定长度内
     * @param string $input 输入
     * @param int $min
     * @param int $max
     * @param boolean 
     */
    static function length($input,$min,$max){
        if(self::min($input,$min) && self::max($input,$max)){
            return true;
        }
        return false;
    }
    
    /**
     * 校验是否在规定长度内
     * @param string $input 输入
     * @param int $min
     * @param boolean 
     */
    static function min($input,$min){
        $length = strlen($input);
        if($length < $min){
            return  false;
        }
        return true;
    }
    /**
     * 校验是否在规定长度内
     * @param string $input 输入
     * @param int $max
     * @param boolean 
     */
    static function max($input,$max){
        $length = strlen($input);
        if($length > $max){
            return  false;
        }
        return true;
    }
    
    /**
     * 校验是否邮箱格式
     * @param string $input 输入
     * @param boolean $out 输出
     * @param boolean 
     */
    static function isEmail($input,$out = false){
        $pattern  = "/\w+@(\w|\d)+\.\w{2,3}/i";
        $isMatch = preg_match($pattern , $input, $matches);
        if($out){
            return $matches;
        }
        return $isMatch;
    }
    
    /**
     * 校验是否手机格式
     * @param string $input 输入
     * @param boolean $out 输出
     * @param boolean 
     */
    static function isMobile($input,$out = false){
        $pattern  = '/^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/';
        $isMatch = preg_match($pattern ,$input, $matches);
        if($out){
            return $matches;
        }
        return $isMatch;
    }
    
    /**
     * 校验是否qq格式
     * @param string $input 输入
     * @param boolean $out 输出
     * @param boolean 
     */
    static function isQq($input,$out = false){
        $pattern  = '/^\d{6,12}$/';
        $isMatch = preg_match($pattern ,$input, $matches);
        if($out){
            return $matches;
        }
        return $isMatch;
    }
    

}
?>