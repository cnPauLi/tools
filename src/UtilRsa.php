<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/7/10
	 * Time: 上午9:43
	 */

	namespace Tool\Utils;


	class UtilRsa
	{
		private $PriKye;
		private $PubKey;

		public function __construct()
		{
			$this->PriKye = $this->_getRsaPriKey();
			$this->PubKey = $this->_getRsaPubKey();
		}

		public function getRsaPubKeyToFile($PubKey_file_path)
		{
			return file_get_contents($PubKey_file_path);
		}

		public function getRsaPrKeyToFile($PrKey_file_path)
		{
			return file_get_contents($PrKey_file_path);
		}

		/**
		 * RSA私钥加密
		 * @param string $private_key 私钥
		 * @param string $data 要加密的字符串
		 * @return string $encrypted 返回加密后的字符串
		 * @author mosishu
		 */
		public function privateEncrypt($data)
		{
			$encrypted = '';
			$pi_key = openssl_pkey_get_private($this->PriKye); //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
			//最大允许加密长度为117，得分段加密
			$plainData = str_split($data, 100); //生成密钥位数 1024 bit key
			foreach ($plainData as $chunk)
			{
				$partialEncrypted = '';
				$encryptionOk = openssl_private_encrypt($chunk, $partialEncrypted, $pi_key); //私钥加密
				if ($encryptionOk === false)
				{
					return false;
				}
				$encrypted .= $partialEncrypted;
			}

			$encrypted = base64_encode($encrypted); //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
			return $encrypted;
		}


		/**
		 * RSA公钥解密(私钥加密的内容通过公钥可以解密出来)
		 * @param string $public_key 公钥
		 * @param string $data 私钥加密后的字符串
		 * @return string $decrypted 返回解密后的字符串
		 * @author mosishu
		 */
		public function publicDecrypt($data)
		{
			$decrypted = '';
			$pu_key = openssl_pkey_get_public($this->PubKey); //这个函数可用来判断公钥是否是可用的
			$plainData = str_split(base64_decode($data), 128); //生成密钥位数 1024 bit key
			foreach ($plainData as $chunk)
			{
				$str = '';
				$decryptionOk = openssl_public_decrypt($chunk, $str, $pu_key); //公钥解密
				if ($decryptionOk === false)
				{
					return false;
				}
				$decrypted .= $str;
			}
			return $decrypted;
		}


		/**
		 * RSA公钥加密
		 */
		public function publicEncrypt($data)
		{
			$encrypted = '';
			$pu_key = openssl_pkey_get_public($this->PubKey);
			$plainData = str_split($data, 100);
			foreach ($plainData as $chunk)
			{
				$partialEncrypted = '';
				$encryptionOk = openssl_public_encrypt($chunk, $partialEncrypted, $pu_key); //公钥加密
				if ($encryptionOk === false)
				{
					return false;
				}
				$encrypted .= $partialEncrypted;
			}
			$encrypted = base64_encode($encrypted);
			return $encrypted;
		}


		/**
		 * RSA私钥解密
		 */
		public function privateDecrypt($data)
		{
			$decrypted = '';
			$pi_key = openssl_pkey_get_private($this->PriKye);
			$plainData = str_split(base64_decode($data), 128);
			foreach ($plainData as $chunk)
			{
				$str = '';
				$decryptionOk = openssl_private_decrypt($chunk, $str, $pi_key); //私钥解密
				if ($decryptionOk === false)
				{
					return false;
				}
				$decrypted .= $str;
			}
			return $decrypted;
		}

		/**
		 * 签名
		 */
		public function sign($data)
		{

			//转换为openssl密钥，必须是没有经过pkcs8转换的私钥
			$res = openssl_get_privatekey($this->PriKye);

			//调用openssl内置签名方法，生成签名$sign
			openssl_sign($data, $sign, $res);

			//释放资源
			openssl_free_key($res);
			return base64_encode($sign);

		}

		/**
		 * 验证签名
		 */
		public function verify($data, $sign)
		{
			$sign = base64_decode($sign);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($this->PubKey);

			//调用openssl内置方法验签，返回bool值
			$result = (bool)openssl_verify($data, $sign, $res);

			//释放资源
			openssl_free_key($res);

			return $result;

		}

		/**
		 * 生成证书
		 */
		function exportOpenSSLFile()
		{
			$config = array(
				"digest_alg"       => "sha512",
				"private_key_bits" => 384, //字节数  512 1024 2048  4096 等
				"private_key_type" => OPENSSL_KEYTYPE_RSA //加密类型
			);
			$res = openssl_pkey_new($config);
			if ($res == false)
				return false;
			openssl_pkey_export($res, $private_key);
			$public_key = openssl_pkey_get_details($res);
			$public_key = $public_key["key"];
			file_put_contents("./cert_public.key", $public_key);
			file_put_contents("./cert_private.pem", $private_key);
			openssl_free_key($res);
		}

		/**
		 * 获取Rsa私钥
		 */
		public function _getRsaPriKey()
		{
			$rsaPriKey = <<<EOF
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC3//sR2tXw0wrC2DySx8vNGlqt3Y7ldU9+LBLI6e1KS5lfc5jl
TGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2klBd6h4wrbbHA2XE1sq21ykja/
Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o2n1vP1D+tD3amHsK7QIDAQAB
AoGBAKH14bMitESqD4PYwODWmy7rrrvyFPEnJJTECLjvKB7IkrVxVDkp1XiJnGKH
2h5syHQ5qslPSGYJ1M/XkDnGINwaLVHVD3BoKKgKg1bZn7ao5pXT+herqxaVwWs6
ga63yVSIC8jcODxiuvxJnUMQRLaqoF6aUb/2VWc2T5MDmxLhAkEA3pwGpvXgLiWL
3h7QLYZLrLrbFRuRN4CYl4UYaAKokkAvZly04Glle8ycgOc2DzL4eiL4l/+x/gaq
deJU/cHLRQJBANOZY0mEoVkwhU4bScSdnfM6usQowYBEwHYYh/OTv1a3SqcCE1f+
qbAclCqeNiHajCcDmgYJ53LfIgyv0wCS54kCQAXaPkaHclRkQlAdqUV5IWYyJ25f
oiq+Y8SgCCs73qixrU1YpJy9yKA/meG9smsl4Oh9IOIGI+zUygh9YdSmEq0CQQC2
4G3IP2G3lNDRdZIm5NZ7PfnmyRabxk/UgVUWdk47IwTZHFkdhxKfC8QepUhBsAHL
QjifGXY4eJKUBm3FpDGJAkAFwUxYssiJjvrHwnHFbg0rFkvvY63OSmnRxiL4X6EY
yI9lblCsyfpl25l7l5zmJrAHn45zAiOoBrWqpM5edu7c
-----END RSA PRIVATE KEY-----
EOF;
			return $rsaPriKey;
		}

		/**
		 * 获取Rsa公钥
		 */
		public function _getRsaPubKey()
		{
			$rsaPubKey = <<<EOF
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC3//sR2tXw0wrC2DySx8vNGlqt
3Y7ldU9+LBLI6e1KS5lfc5jlTGF7KBTSkCHBM3ouEHWqp1ZJ85iJe59aF5gIB2kl
Bd6h4wrbbHA2XE1sq21ykja/Gqx7/IRia3zQfxGv/qEkyGOx+XALVoOlZqDwh76o
2n1vP1D+tD3amHsK7QIDAQAB
-----END PUBLIC KEY-----
EOF;
			return $rsaPubKey;
		}

		public function test()
		{

			$ps = new self ();

			$str = '我是谁，我在哪';

			//私钥加密
			$private = $ps->privateEncrypt($str);
			echo "<br/>privateEncrypt {$private}  <br/>";

			//公钥解密
			$public = $ps->publicDecrypt($private);
			echo "<br/>publicDecrypt {$public}  <br/>";

			$sign = $ps->sign('234234');
			echo "<br/>sign {$sign}  <br/>";

			$verify = $ps->verify('234234', $sign);
			echo "<br/>verify {$verify}  <br/>";

			//生成证书
			$ps->exportOpenSSLFile();

		}

		static public function test1()
		{

			$ps = new self ();
			$data = [];
			$str = '我是谁，我在哪';
			$data['$str'] = $str;
			//私钥加密
			$data['privateEncrypt'] = $ps->privateEncrypt($str);


			//公钥解密
			$data['publicDecrypt'] = $ps->publicDecrypt($data['privateEncrypt']);


			$data['sign'] = $ps->sign($str);


			$data['verify'] = $ps->verify($str, $data['sign']);

			return $data;
			//生成证书
			$ps->exportOpenSSLFile();

		}
	}

