<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/4/25
	 * Time: 下午5:04
	 */

	namespace Tool\Utils;


	class UtilConfig
	{
		static public function getConfigByFile($path, $keywords = [], $menu_type = '')
		{
			$module_ = [];
			foreach ($path as $app_path)
			{
				foreach ($keywords as $module)
				{
					if (strpos($app_path, $module . '/' . $menu_type) !== false)
					{

						$module_[$module] = require($app_path);
					}
				}
			}
			return $module_;

		}

		static public function getConfigPathByFile($path, $keywords = [])
		{
			$module_ = [];
			foreach ($path as $app_path)
			{
				foreach ($keywords as $module)
				{
					if (strpos($app_path, $module . '/menu') !== false)
					{
						$module_[$module] = $app_path;

					}
				}
			}
			return $module_;

		}
	}