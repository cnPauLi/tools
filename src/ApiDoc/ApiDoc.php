<?php

	namespace Tool\Utils\ApiDoc;

	class ApiDoc
	{

		private   $mainRegex        = '/(\/\*\*.*?\*\sapi.*?\*\/\s*(public|private|protected)?\s*function\s+.*?\s*?\()/s';
		protected $documentPath;
		protected $NameSpacePath;
		protected $savePath;
		protected $name             = 'api';
		protected $controllerChange = true;
		protected $controllerTimes  = 1;
		protected $methodChange     = true;
		protected $methodTimes      = 2;

		public static function test()
		{
			echo 'hello';
		}

		public function __construct($NameSpacePath, $savePath = null)
		{
			$this->NameSpacePath = $NameSpacePath;

			if ($savePath == null)
			{
				$this->savePath = getcwd() . DIRECTORY_SEPARATOR;
			} else
			{
				$this->savePath = $savePath;
			}
		}

		/**
		 * 设置项目名称
		 * @param string $name 项目名称
		 * @return void
		 */
		public function setName($name)
		{
			$this->name = $name;
		}

		/**
		 * 设置是否开启驼峰转匈牙利
		 * @param bool $controller 文件名 true/false
		 * @param bool $method 方法名 true/false
		 * @return void
		 */
		public function setChange($controller = true, $method = true)
		{
			$this->controllerChange = $controller;
			$this->methodChange = $method;
		}

		/**
		 * 驼峰转匈牙利转换条件 (出现几次大写字母才转换)
		 * @param integer $controller 文件名
		 * @param integer $method 方法名
		 * @return void
		 */
		public function setTimes($controller = 1, $method = 2)
		{
			$this->controllerTimes = $controller;
			$this->methodTimes = $method;
		}

		/**
		 * 大驼峰命名法转匈牙利命名法
		 * @param string $str 字符串
		 * @param integer $times 出现几次大写字母才转换,默认1次
		 * @return string
		 */
		private function humpToLine($str, $times = 1)
		{
			if (preg_match_all('/[A-Z]/', $str) >= $times)
			{
				$str = preg_replace_callback('/([A-Z]{1})/', function($matches)
				{
					return '_' . strtolower($matches[0]);
				}, $str);
				if ($str[0] == '_')
				{
					$str = substr_replace($str, '', 0, 1);
				}
				return $str;
			}
			return $str;
		}

		/**
		 * 递归法获取文件夹下文件
		 * @param string $path 路径
		 * @param array $fileList 结果保存的变量
		 * @param bool $all 可选,true全部,false当前路径下,默认true.
		 */
		private function getFileList($path, &$fileList = [], $all = true)
		{
			if (!is_dir($path))
			{
				$fileList = [];
				return;
			}
			$data = scandir($path);
			foreach ($data as $one)
			{
				if ($one == '.' or $one == '..')
				{
					continue;
				}
				$onePath = $path . DIRECTORY_SEPARATOR . $one;
				$isDir = is_dir($onePath);
				$extName = substr($one, - 4, 4);
				if ($isDir == false and $extName == '.php')
				{
					$fileList[] = $onePath;
				} elseif ($isDir == true and $all == true)
				{
					$this->getFileList($onePath, $fileList, $all);
				}
			}
		}

		/**
		 * 获取代码文件中所有可以生成api的注释
		 * @param string $data 代码文件内容
		 */
		private function catchEvery($data)
		{
			preg_match_all($this->mainRegex, $data, $matches);
			if (empty($matches[1]))
			{
				return [];
			} else
			{
				return $matches[1];
			}
		}

		protected function jx($data)
		{
			$return = [];
			preg_match_all('/(public|private|protected)?\s*function\s+(.*?)\(/', $data, $matches);
			$return['funcName'] = !empty($matches[2][0]) ? $matches[2][0] : '[null]';
			preg_match_all('/\/\*\*\s+\*\s+(.*?)\s+\*\s+api\s+/s', $data, $matches);
			$return['methodName'] = !empty($matches[1][0]) ? $matches[1][0] : '[null]';
			preg_match_all('/\s+\*\s+api\s+(.*?)\s+(.*?)\s+(\s+\*\s+@)?.*/', $data, $matches);

			$return['requestName'] = !empty($matches[1][0]) ? $matches[1][0] : '[null]';
			$return['requestUrl'] = !empty($matches[2][0]) ? $matches[2][0] : '[null]';
			return $return;
		}

		protected function jxd($data)
		{
			preg_match_all('/\s+\*\s+@deprecated\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);

			return !empty($matches[1]) ? $matches[1][0] : '居然不写类说明';
		}

		/**
		 * 解析每一条可以生成API文档的注释成数组
		 * @param string $data 注释文本 catchEvery返回的每个元素
		 * @param string $fileName 文件名
		 * @return array
		 */
		private function parse($data, $fileName)
		{
			$return = $this->jx($data);

			if ($this->controllerChange == true)
			{
				$return['requestUrl'] = str_replace('{controller}', $this->humpToLine($fileName, $this->controllerTimes), $return['requestUrl']);
			}
			if ($this->methodChange == true)
			{
				$return['requestUrl'] = str_replace('{method}', $this->humpToLine($return['funcName'], $this->methodTimes), $return['requestUrl']);
			}

			preg_match_all('/\s+\*\s+@param\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
			if (empty($matches[1]))
			{
				$return['param'] = [];
			} else
			{
				for ($i = 0; $i < count($matches[1]); $i ++)
				{
					$type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
					$var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
					$about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
					$return['param'][] = [
						'type'  => $type,
						'var'   => $var,
						'about' => $about,
					];
				}
			}
			preg_match_all('/\s+\*\s+@return\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
			$return['return'] = [];
			if (empty($matches[1]))
			{
				$return['return'] = [];
			} else
			{
				for ($i = 0; $i < count($matches[1]); $i ++)
				{
					$type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
					$var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
					$about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
					if (strpos($about, '*/') !== false)
					{
						$about = $var;
						$var = '';
					}


					if ($var != '*/' and $var != '')
					{
						// echo "<script>console.log('{$fileName}-{$return['funcName']}-{$var}')</script>";
						$return['return'][] = [
							'type'  => $type,
							'var'   => $var,
							'about' => $about,
						];
					}

				}
			}
			return $return;
		}

		/**
		 * 每个API生成表格
		 * @param array $data 每个API的信息 由parse返回的
		 * @return string html代码
		 */
		private function makeTable($data)
		{

			$return = '<div id="' . base64_encode($data['requestUrl']) . '" class="api-main">
        <div class="title">' . $data['methodName'] . '</div>
        <div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                        ' . $data['requestName'] . '
                        </th>
                        <th rowspan="3">
                        ' . $data['requestUrl'] . '
                        </th>
                    </tr>
                </thead>
            </table>
        </div>';

			if (!empty($data['param']))
			{
				$return .= '                    <div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                            参数名称
                        </th>
                        <th>
                            请求类型
                        </th>
                        <th>
                            请求说明
                        </th>
                    </tr>
                </thead>
                <tbody>';
				foreach ($data['param'] as $param)
				{
					$return .= '<tr>
                <td>
                    ' . $param['var'] . '
                </td>
                <td>
                ' . $param['type'] . '
                </td>
                <td>
                ' . $param['about'] . '
                </td>
            </tr>';
				}
				$return .= '</tbody>
            </table>
        </div>';
			}
			if (!empty($data['return']))
			{
				$return .= '<div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                            返回名称
                        </th>
                        <th>
                            返回类型
                        </th>
                        <th>
                            返回说明
                        </th>
                    </tr>
                </thead>
                <tbody>';
				foreach ($data['return'] as $param)
				{
					$return .= '<tr>
                <td>
                    ' . $param['var'] . '
                </td>
                <td>
                ' . $param['type'] . '
                </td>
                <td>
                ' . $param['about'] . '
                </td>
            </tr>';
				}
				$return .= '</tbody>
            </table>
        </div>';
			}

			$return .= ' <hr>
        </div>';

			return $return;
		}

		/**
		 * 生成侧边栏
		 * @param array $rightList 侧边列表数组
		 * @return string html代码
		 */
		private function makeRight($rightList)
		{
			$return = '';
			foreach ($rightList as $d => $file)
			{
				$return .= '<blockquote class="layui-elem-quote layui-quote-nm right-item-title">' . $d . '</blockquote>
            <ul class="right-item">';
				foreach ($file as $one)
				{
					$return .= '<li><a href="#' . base64_encode($one['requestUrl']) . '"><cite>' . $one['methodName'] . '</cite><em>' . $one['requestUrl'] . '</em></a></li>';
				}
				$return .= '</ul>';
			}

			return $return;
		}

		/**
		 * 开始执行生成
		 * @param bool $fetch 是否方法返回,make(true) 可以用来直接输出
		 */
		public function make($fetch = false)
		{
			$inputData = ''; // 主体部分表格
			$rightList = array(); // 侧边栏列表
			foreach ($this->NameSpacePath as $key => $fileName)
			{
				$fileData = $this->getFileInfo($fileName);

				//dump([$fileName, $fileData]);
				foreach ($fileData['method'] as $one)
				{
					$fileName = explode('\\', $fileName);
					$fileName = array_pop($fileName);

					if (!strripos($one['method_doc'], 'api'))
					{
						continue;
					}
					$infoData = $this->parse($one['method_doc'], $fileName);

					//if ($infoData['funcName'] != '[null]' || $infoData['requestUrl'] != '[null]')
					if (!empty($infoData['param']))
					{
						$rightList[$fileName][] = [
							'methodName' => $infoData['methodName'],
							'requestUrl' => $infoData['requestUrl'],
						];
					}
					$inputData .= $this->makeTable($infoData);
				}
			}

			$tempData = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp.html');
			$tempData = str_replace('{name}', $this->name, $tempData);
			$tempData = str_replace('{main}', $inputData, $tempData);
			$tempData = str_replace('{right}', $this->makeRight($rightList), $tempData);
			$tempData = str_replace('{date}', date('Y-m-d H:i:s'), $tempData);
			if ($fetch == false)
			{
				file_put_contents($this->savePath . $this->name . '.html', $tempData);
			} else
			{
				return $tempData;
			}
		}

		/**
		 * 开始执行生成
		 * @param bool $fetch 是否方法返回,make(true) 可以用来直接输出
		 */
		public function make_md()
		{
			$inputData = []; // 主体部分表格
			$rightList = array(); // 侧边栏列表
			foreach ($this->NameSpacePath as $key => $fileName)
			{
				$fileData = $this->getFileInfo($fileName);
				$inputData[$key]['title'] = $this->jxd($fileData['class_doc']);
				$inputData[$key]['name'] = $fileName;
				//dump([$fileName, $fileData]);
				foreach ($fileData['method'] as $ky => $one)
				{
					$fileName = explode('\\', $fileName);
					$fileName = array_pop($fileName);

					if (!strripos($one['method_doc'], 'api'))
					{
						continue;
					}
					$infoData = $this->parse($one['method_doc'], $fileName);

					//if ($infoData['funcName'] != '[null]' || $infoData['requestUrl'] != '[null]')
					if (!empty($infoData['param']))
					{
						$rightList[$fileName][] = [
							'methodName' => $infoData['methodName'],
							'requestUrl' => $infoData['requestUrl'],
						];
					}
					$inputData[$key]['method'][$ky]['data'] = $infoData;
					$inputData[$key]['method'][$ky]['doc'] = $this->md($infoData);
				}
			}

			return $inputData;
		}

		public function md($data)
		{
			$params = ' ';
			if (!empty($data['param']))
			{
				foreach ($data['param'] as $datum)
				{
					$params .= "|{$datum['var']}|{$datum['type']}|{$datum['about']}|" . PHP_EOL;
				}
			} else
			{
				$params = ' |||||';
			}

			$response = $this->indent(json_encode($data['param']));

			$return = ' ';
			if (!empty($data['return']))
			{
				foreach ($data['return'] as $datum)
				{
					$return .= "|{$datum['var']}|{$datum['type']}|{$datum['about']}|" . PHP_EOL;
				}
			} else
			{
				$return = ' |||||';
			}


			return <<<ETO
**简要描述：** 

- {$data['methodName']}

**请求URL：** 
- ` {$data['requestUrl']}`
  
**请求方式：**
- {$data['requestName']} 

**参数：** 

|参数名|类型|说明|
|:----|:---|:-----|
{$params}

 **返回示例**
``` 
  {$response}
```
 **返回参数说明** 

|参数名|类型|说明|
|:----|:---|:-----|
{$return}
ETO;

		}


		public $ignore_method = ['__construct', '_initialize'];

		public function getFileInfo($NameSpace)
		{
			$reflection = new \ReflectionClass($NameSpace);
			//通过反射获取类的注释
			$class_doc = $reflection->getDocComment();

			//获取类中的方法，设置获取public,protected类型方法
			$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
			//遍历所有的方法
			$ClassInfo = [
				'class_name' => $NameSpace,
				'class_doc'  => $class_doc,
			];
			foreach ($methods as $method)
			{
				$method_name = $method->getName();
				$method_doc = $method->getDocComment();

				if (!in_array($method_name, $this->ignore_method) && $method_doc)

					$ClassInfo['method'][$method_name] = array(
						'method_name' => $method_name,
						//获取方法的注释
						'method_doc'  => $method_doc,

					);
			}

			return $ClassInfo;
		}

		/**
		 * Formats a JSON string for pretty printing
		 *
		 * @param string $json The JSON to make pretty
		 * @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
		 * @return string The prettified output
		 */
		protected function indent($json, $html = false)
		{
			$tabcount = 0;
			$result = '';
			$inquote = false;
			$ignorenext = false;
			if ($html)
			{
				$tab = "   ";
				$newline = "<br/>";
			} else
			{
				$tab = "\t";
				$newline = "\n";
			}
			for ($i = 0; $i < strlen($json); $i ++)
			{
				$char = $json[$i];
				if ($ignorenext)
				{
					$result .= $char;
					$ignorenext = false;
				} else
				{
					switch ($char)
					{
						case '{':
							$tabcount ++;
							$result .= $char . $tab . $newline . str_repeat($tab, $tabcount);
							break;
						case '}':
							$tabcount --;
							$result = trim($result) . $tab . $newline . str_repeat($tab, $tabcount) . $char;
							break;
						case ',':
							$result .= $char . $newline . str_repeat($tab, $tabcount);
							break;
						case '"':
							$inquote = !$inquote;
							$result .= $char;
							break;
						case '\\':
							if ($inquote)
								$ignorenext = true;
							$result .= $char;
							break;
						default:
							$result .= $char;
					}
				}
			}
			return $result;
		}

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
			return $body;
		}

	}
