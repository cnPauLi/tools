<?php
	/**
	 *防止sql注入的一些过滤条件
	 *
	 *
	 * @author      libin<hansen.li@silksoftware.com>
	 * @version     1.0
	 * @since       1.0
	 */

	namespace Tool\Utils;


	class UtilFilter
	{
		/**
		 * 处理 '成\'
		 * @param unknown $str 需要处理的字符串
		 * @return string
		 */
		public static function addslashesStr($str)
		{
			return addslashes($str);
		}

		/**
		 * 处理 \'成'
		 * @param unknown $str 需要处理的字符串
		 * @return string
		 */
		public static function stripslashesStr($str)
		{
			return stripslashes($str);
		}
	}
