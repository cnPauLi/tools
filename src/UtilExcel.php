<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/2/6
	 * Time: 上午10:03
	 */

	namespace Tool\Utils;

	/**
	 * Class UtilFiles
	 * @package Tool\Utils
	 */
	class UtilExcel
	{
		static protected $Instance = null;

		static public function Instance()
		{
			if (static::$Instance == null) {
				static::$Instance = new static();
			}

			return static::$Instance;
		}

		/**
		 * 读取文件数据到数组
		 * @param array $fielInfo 文件基本信息
		 * @param bool $oneRoweTitle 是否将第一行作为数组键
		 * @return mixed
		 * @throws \PHPExcel_Exception
		 * @throws \PHPExcel_Reader_Exception
		 */
		static public function ReadToArray($fielInfo, $oneRoweTitle = false)
		{
			set_time_limit(0); //设置页面等待时间
			if ($fielInfo['ext']) {

				if ($fielInfo['ext'] == 'xlsx' || $fielInfo['ext'] == 'xls') {
					$reader = \PHPExcel_IOFactory::createReader('Excel2007'); // 读取 excel 文档
				} else if ($fielInfo == 'csv') {
					$reader = \PHPExcel_IOFactory::createReader('CSV'); // 读取 excel 文档
				} else {
					die('文件类型不正确，请重新输入！');
				}
				$PHPExcel = $reader->load($fielInfo['pathname']); // 载入excel文件
				$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
				$highestRow = $sheet->getHighestRow(); // 取得总行数
				$highestColumm = $sheet->getHighestColumn(); // 取得总列数
				$highestColumm = \PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27

				if ($oneRoweTitle == true) {
					for ($column = 0; $column < $highestColumm; $column ++) {//列数是以第0列开始
						$oneRowe[] = $sheet->getCellByColumnAndRow($column, 1)->getValue();
					}

					/** 循环读取每个单元格的数据 */
					for ($row = 2; $row <= $highestRow; $row ++) {//行数是以第1行开始
						for ($column = 0; $column < $highestColumm; $column ++) {//列数是以第0列开始
							$excelarr[$row - 1][$oneRowe[$column]] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
						}
					}
				} else {
					/** 循环读取每个单元格的数据 */
					for ($row = 1; $row <= $highestRow; $row ++) {//行数是以第1行开始
						for ($column = 0; $column < $highestColumm; $column ++) {//列数是以第0列开始
							$excelarr[$row][] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
						}
					}
				}

				return $excelarr;
			}
		}
	}