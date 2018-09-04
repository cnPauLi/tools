<?php
	/**
	 * csv服务
	 */

	namespace Tool\Utils;


	class UtilCsv
	{

		public static function export($filename, $data, $charset = "GBK")
		{
			header("Content-type:text/csv");
			header("Content-Disposition:attachment;filename=" . $filename);
			header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
			header('Expires:0');
			header('Pragma:public');
			$data = mb_convert_encoding($data, $charset, 'utf-8');
			echo $data;
		}

		/**
		 * 导入CSV
		 * @param $Files
		 * @param int $title_row
		 * @return array|string
		 */
		static public function inport($Files, $title_row = 0)
		{
			$str = file_get_contents($Files, 'r');

			$encode = mb_detect_encoding($str, array('GB2312',"ASCII",'UTF-8',"GBK",'BIG5'));
			if($encode!=="UTF-8"){
				return 400;
			}
			$name = strrchr($Files, '.');
			//判断文件格式
			if ($name !== ".csv") {
				return '文件格式错误！';
			};
			$data_Csv = fopen($Files, 'r');
			$i = 1;
			$title = [];
			while (($row = fgetcsv($data_Csv)) !== FALSE) { //每次读取CSV里面的一行内容
				$row_ = count($row) > 1 ? $row : explode(',', $row[0]);
				if ($i > $title_row) {
					if (!empty($title)) {
						$row_array = [];
						foreach ($row_ as $key => $row_val) {
							$row_array[$title[$key]] = $row_val;
						}
						$lint[] = $row_array;
					}
				} else {
					$title = ['phone','balance','describe'];
				}
				$i ++;
			}

			return $lint;
		}


		/**
		 * 取得上传文件信息
		 * @param $file
		 * @return array
		 */
		static public function getUploadFile($file)
		{
			$file_info = $_FILES[$file];//取得上传文件基本信息
			$info = array();
			$info['type'] = strtolower(trim(stripslashes(preg_replace("/^(.+?);.*$/", "\\1", $file_info['type'])), '"'));//取得文件类型
			$info['temp'] = $file_info['tmp_name'];//取得上传文件在服务器中临时保存目录
			$info['size'] = $file_info['size'];//取得上传文件大小
			$info['error'] = $file_info['error'];//取得文件上传错误
			$info['name'] = $file_info['name'];//取得上传文件名
			$info['ext'] = self::get_ext($file_info['name']);//取得上传文件后缀
			return $info;
		}

		/**
		 * 获取文件后缀名
		 * @param string $file_name 文件路径
		 * @return string
		 */
		static public function get_ext($file)
		{
			$file = static::dir_replace($file);
			//return strtolower(substr(strrchr(basename($file), '.'),1));
			//return end(explode(".",$filename ));
			//return strtolower(trim(array_pop(explode('.', $file))));//取得后缀
			//return preg_replace('/.*\.(.*[^\.].*)*/iU','\\1',$file);
			return pathinfo($file, PATHINFO_EXTENSION);
		}

		/**
		 * 替换相应的字符
		 * @param string $path 路径
		 * @return string
		 */
		static public function dir_replace($path)
		{
			return str_replace('//', '/', str_replace('\\', '/', $path));
		}

	}
