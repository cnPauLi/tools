<?php
/**
 * 短文字加密解密
 * 用法如下
 *  use common\utils\UtilDes;
 *  UtilDes::encrypt("test1","sdfas*sd");
 *  echo UtilDes::decrypt("CMoFz8IxAOY=","sdfas*sd");
 */
namespace Tool\Utils;


class UtilLock
{

  public static function encrypt($txt, $key)  
  {  
      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";  
      $nh = rand(0,64);  
      $ch = $chars[$nh];  
      $mdKey = md5($key.$ch);  
      $mdKey = substr($mdKey,$nh%8, $nh%8+7);  
      $txt = base64_encode($txt);  
      $tmp = '';  
      $i=0;$j=0;$k = 0;  
      for ($i=0; $i<strlen($txt); $i++) {  
          $k = $k == strlen($mdKey) ? 0 : $k;  
          $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;  
          $tmp .= $chars[$j];  
      }

      $res=$ch.$tmp;

      $src  = array("-","+","=");
      $dist = array("_a","_b","_c");
      $res  = str_replace($src,$dist,$res);

      return $res;

  }

  public static function decrypt($txt, $key)  
  {    
      $src  = array("-","+","=");
      $dist = array("_a","_b","_c");
      $txt  = str_replace($dist,$src,$txt);

      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";  
      $ch = $txt[0];  
      $nh = strpos($chars,$ch);  
      $mdKey = md5($key.$ch);  
      $mdKey = substr($mdKey,$nh%8, $nh%8+7);  
      $txt = substr($txt,1);  
      $tmp = '';  
      $i=0;$j=0; $k = 0;  
      for ($i=0; $i<strlen($txt); $i++) {  
          $k = $k == strlen($mdKey) ? 0 : $k;  
          $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);  
          while ($j<0) $j+=64;  
          $tmp .= $chars[$j];  
      }  
      return base64_decode($tmp);  
  }  
}