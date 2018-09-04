<?php
/**
 * des加密解密
 * 用法如下
 *  use common\utils\UtilDes;
 *  UtilDes::encrypt("test1","sdfas*sd");
 *  echo UtilDes::decrypt("CMoFz8IxAOY=","sdfas*sd");
 */
namespace Tool\Utils;


class UtilDes
{
  public static function encrypt($str, $key)  
  {  
      $block = mcrypt_get_block_size('des', 'ecb');  
      $pad = $block - (strlen($str) % $block);  
      $str .= str_repeat(chr($pad), $pad);  
      return base64_encode(mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB));
  }

  public static function decrypt($str, $key)  
  {    
      $str =base64_decode($str );
      $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);  
      $block = mcrypt_get_block_size('des', 'ecb');  
      $pad = ord($str[($len = strlen($str)) - 1]);  
      return substr($str, 0, strlen($str) - $pad);  
  }  
}