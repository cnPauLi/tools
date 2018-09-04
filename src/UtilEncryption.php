<?php
	/**
	 *加密字符串
	 *
	 *
	 * @author      libin<hansen.li@silksoftware.com>
	 * @version     1.0
	 * @since       1.0
	 */

	namespace Tool\Utils;

	/**
	 * Class UtilEncryption
	 * @package Tool\Utils
	 */
	class UtilEncryption
	{
		/**
		 * 采用HashHmac方法获取加密的字符串
		 * @param unknown $data 需要加密的字符串
		 * @param string $secret 加密秘钥
		 * @return string|boolean
		 */
		public static function encryptHashHmac($data, $secret)
		{
			$str = hash_hmac('sha256', $data, $secret, $raw = false);
			//$str=base64_encode($str);
			return $str;
		}

		/**
		 * 采用md5方法获取加密的字符串
		 * @param unknown $data 需要加密的字符串
		 * @param string $secret 加密秘钥
		 * @return string|boolean
		 */
		public static function encryptMd5($data, $secret = "")
		{
			return md5(md5($data . $secret) . $secret);
		}

		/**
		 * 生成随机字符串
		 * @param $param
		 * @param int $lenth
		 * @return string
		 */
		public static function getRandom($param, $lenth = 32)
		{
			$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$key = "";
			for ($i = 0; $i < $param; $i ++)
			{
				$key .= $str{mt_rand(0, $lenth)};    //生成php随机数
			}
			return $key;
		}

		/**
		 * 取得随机数,并可指定长度
		 *
		 * @param int $length 生成随机数的长度
		 * @param int $numeric 是否只产生数字随机数 1是0否
		 * @return string
		 */
		static function random($length, $numeric = 0)
		{
			$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
			$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
			$hash = '';
			$max = strlen($seed) - 1;
			for ($i = 0; $i < $length; $i ++)
			{
				$hash .= $seed{mt_rand(0, $max)};
			}
			return $hash;
		}

		/**
		 * 生成指定长度字随机
		 * @param int $length 指定长度
		 * @return int
		 */
		static public function RandomNumber($length = 6)
		{
			return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
		}


		/**
		 * 生成唯一单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
		 * 长度 =2位 + 10位 + 3位 + 3位  = 18位
		 * 1000个会员同一微秒提订单，重复机率为1/100
		 * @return string
		 */
		static public function MakeSn()
		{
			usleep(5);
			return
				sprintf('%010d', time() - 946656000)
				. sprintf('%03d', (float)microtime() * 1000)
				. sprintf('%03d', (float)microtime() * 1000)
				. mt_rand(10, 99);
		}
	}
