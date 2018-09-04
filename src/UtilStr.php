<?php
// +----------------------------------------------------------------------
// | 字符处理
// +----------------------------------------------------------------------
// | 字符串的一些处理
// +----------------------------------------------------------------------
// | 2016-6-14
// +----------------------------------------------------------------------
// | Author: 雷震子 
// +----------------------------------------------------------------------
	namespace Tool\Utils;

	class UtilStr
	{

		public static function substr_cut($user_name, $charset = 'utf-8')
		{
			$strlen = mb_strlen($user_name, $charset);
			$firstStr = mb_substr($user_name, 0, 1, $charset);
			$lastStr = mb_substr($user_name, - 1, 1, $charset);
			switch ($strlen)
			{
				case 0:
					return '***';
				case 1:
				case 2:
					return $firstStr . str_repeat('*', mb_strlen($user_name, $charset) - 1);
				default:
					return $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
			}
			return '***';
		}

		/**
		 * 根据长度截取编码格式的字符
		 * @param $string string 中文字符串
		 * @param $limit int 长度
		 * @param $charset string 字符编码
		 * @return string
		 */
		public static function substr_limit($string, $limit = 6, $charset = 'utf-8')
		{
			$strlen = mb_strlen($string, $charset);
			$limit += floor(preg_match_all('/(\w){1,1}/', $string) / 2);
			if ($strlen > $limit)
			{
				return mb_substr($string, 0, $limit, $charset);
			}
			return $string;
		}

		/**
		 * 根据长度截取编码格式的字符,超出部分显示省略号
		 * @param $string string 中文字符串
		 * @param $limit int 长度
		 * @param $charset string 字符编码
		 * @return string
		 */
		public static function cut($string, $limit = 20, $charset = 'utf-8')
		{
			$strlen = mb_strlen($string, $charset);
			$limit += floor(preg_match_all('/(\w){1,1}/', $string) / 2);
			if ($strlen > $limit)
			{
				return mb_substr($string, 0, $limit, $charset) . '...';
			}
			return $string;
		}

		/**
		 * 截取字符串长度并拼接 *
		 * @param $value
		 * @param int $start
		 * @param int $end
		 * @return string
		 */
		static public function substr($value, $start = 0, $end = 5)
		{

			return mb_substr($value, $start, $end);
		}

		/**
		 * 截取字符串长度并拼接 *
		 * @param $value
		 * @param int $start
		 * @param int $end
		 * @return string
		 */
		static public function substr_($value, $start = 0, $end = 5)
		{

			$value = mb_substr($value, $start, $end);
			return "{$value}******";
		}

		/**
		 * 数组转字符串拼接
		 * 键=值逗号分隔
		 * @param $array
		 * @return string
		 */
		static public function arrayToString($array, $keyLink = '=', $link = ',')
		{
			$scene = [];
			foreach ($array as $key => $param)
			{
				$scene[] = $key . $keyLink . $param;
			}
			return join($link, $scene);
		}
	}
