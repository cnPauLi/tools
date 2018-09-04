<?php

	namespace Tool\Utils;


	class UtilHttp
	{
		/**
		 * post数据到指定地址
		 * @param unknown $url
		 * @param string $post_data
		 * @return string|boolean
		 */
		public static function curl_http_post($url, $post_data = null)
		{
			$post_fields = "";
			if (isset($post_data) && is_string($post_data) && strlen($post_data) > 0)
				$post_fields = $post_data;
			else if (isset($post_data) && is_array($post_data) && count($post_data) > 0)
				$post_fields = http_build_query($post_data, null, '&', PHP_QUERY_RFC3986);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			$result = curl_exec($ch);
			$curl_info = curl_getinfo($ch);
			$info = $curl_info['url'] . '|' . $curl_info['http_code'] . '|' . $curl_info['total_time'];
			$header_size = $curl_info['header_size'];
			$header = substr($result, 0, $header_size);
			$body = substr($result, $header_size);
			$success = true;
			$uri = $_SERVER["REQUEST_URI"];
			if ($curl_info['http_code'] == 0 || $curl_info['http_code'] >= 400)
			{
				$log_text = "[uri:$uri][$info][$header][$body]";
				//write_log("curl-http-post", $log_text, "error");
				$success = false;
			}
			curl_close($ch);
			if ($success)
			{
				return $body;
			}
			return false;
		}

		/**
		 *
		 */
		public static function curl_http_get($url)
		{
			$ch = curl_init();
			//设置超时
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec($ch);
			curl_close($ch);
			return $res;
		}

		/**
		 * POST 用户中心接口专用，与其他post方式有区别
		 * @param string $url
		 * @param array $param
		 * @param boolean $post_file 是否文件上传
		 * @return string content
		 */
		static function user_http_post($url, $param)
		{
			$oCurl = curl_init();
			if (stripos($url, "https://") !== FALSE)
			{
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
			}
			if (is_string($param))
			{
				$strPOST = $param;
			} else
			{
				$aPOST = array();
				foreach ($param as $key => $val)
				{
					if (is_array($val))
					{
						foreach ($val as $k => $v)
						{
							$aPOST[] = $key . "=" . urlencode($v);
						}
					} else
					{
						$aPOST[] = $key . "=" . urlencode($val);
					}
				}
				$strPOST = join("&", $aPOST);
			}
			curl_setopt($oCurl, CURLOPT_URL, $url);
			curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($oCurl, CURLOPT_POST, true);
			curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
			$sContent = curl_exec($oCurl);
			$aStatus = curl_getinfo($oCurl);
			curl_close($oCurl);
			if (intval($aStatus["http_code"]) == 200)
			{
				return $sContent;
			} else
			{
				return false;
			}
		}

		/**
		 * 下载远程文件并输出
		 * @param $url
		 * @param int $timeout
		 */
		static public function download($url, $timeout = 60)
		{

			$url = str_replace(" ", "%20", $url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$temp = curl_exec($ch);
			curl_close($ch);
			header("Content-type: image/jpeg");
			echo $temp;
			die;
		}
	}
