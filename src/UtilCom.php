<?php

	namespace Tool\Utils;


	class UtilCom
	{
		/**
		 * post数据到指定地址
		 * @param unknown $url
		 * @param string $post_data
		 * @return string|boolean
		 */
		public static function include_fetch($file, $parm = array())
		{
			extract($parm);         // Extract the vars to local namespace
			ob_start();                    // Start output buffering
			include($file);  // Include the file
			$contents = ob_get_contents(); // Get the contents of the buffer
			ob_end_clean();                // End buffering and discard
			return $contents;
		}
	}
