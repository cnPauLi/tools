<?php

	namespace Tool\Utils;


	class UtilArray
	{
		/**
		 * @param $Array1
		 * @param $Array2
		 * @return array|bool
		 */
		static public function OneToTwo($Array)
		{

			$newArray = [];
			$i = 0;
			foreach ($Array as $key => $item)
			{
				foreach ($item as $k => $value)
				{
					$newArray[$k][$key] = $value;
				}
			}
			return $newArray;

		}

		/**
		 * 判断数组是否相同
		 * @param $Array1
		 * @param $Array2
		 * @return bool
		 */
		static public function CheckArray($Array1, $Array2)
		{
			return (array_keys($Array1) == array_keys($Array2)) ? true : false;
		}

		/**
		 * 获取数组键并按照key进行排序
		 * @param $Array
		 * @return bool
		 */
		static public function getKeysByKsort($Array)
		{
			return ksort(array_keys($Array));
		}


		/***
		 * 下划线转驼峰
		 * @param $str
		 * @return null|string|string[]
		 */
		static public function convertUnderline($str)
		{
			$str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function($matches)
			{
				return strtoupper($matches[2]);
			}, $str);
			return $str;
		}

		/***
		 * 驼峰转下划线
		 * @param $str
		 * @return null|string|string[]
		 */
		static public function humpToLine($str)
		{
			$str = preg_replace_callback('/([A-Z]{1})/', function($matches)
			{
				return '_' . strtolower($matches[0]);
			}, $str);
			return $str;
		}

		static public function convertHump(array $data)
		{
			$result = [];
			foreach ($data as $key => $item)
			{
				if (is_array($item) || is_object($item))
				{
					$result[self::humpToLine($key)] = self::convertHump((array)$item);
				} else
				{
					$result[self::humpToLine($key)] = $item;
				}
			}
			return $result;
		}


	}
