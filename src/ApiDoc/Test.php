<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/6/6
	 * Time: 下午5:40
	 */

	namespace Tool\Utils;


	use Tool\Utils\ApiDoc\ApiDoc;
	use think\Controller;

	class Test
	{


		/**
		 * 获取所有列表
		 * api GET api.php/index/index/all
		 * @param integer $page 页数
		 * @param integer $limit 每页个数
		 * @return integer $code 状态码
		 * @return string $msg 返回消息
		 * @return array $void 结果!!!
		 *
		 * // 地址中新增两个占位符写法
		 * // api.php/index/{controller}/{method}
		 * // {method} 会自动换成对应的方法名
		 * // {controller} 会自动换成文件名(大驼峰会转成匈牙利)。
		 * //--------------------------------
		 * // 默认是文件名和方法名都开启大驼峰转换
		 * // 文件名是大写字母出现1次以及以上就转换
		 * // 方法名是大写字母出现2次以及以上就转换
		 * // 可以通过下面方法去改变,参数1是文件名,参数2是方法名
		 * // $doc->setChange(true,true);
		 * // $doc->setTime(1,2);
		 */
		public function index()
		{
			$API_FILES_PATH_ARRAY = [
				'app\\common\\utils\\Test',
			];
			$doc = new ApiDoc($API_FILES_PATH_ARRAY);
			$doc->setName(RUNTIME_PATH . 'api');
			echo $doc->make(true);

		}

		public function push()
		{
			$API_FILES_PATH_ARRAY = [
				'app\\common\\utils\\Test',
			];
			$doc = new ApiDoc($API_FILES_PATH_ARRAY);
			$doc->setName(RUNTIME_PATH . 'api');
//			echo $doc->make(true);die;
			$data = $doc->make_md();
//			dump($data);die;


			/**
			 * 参数名    必选    类型    说明
			 * api_key    是    string    api_key，认证凭证。登录showdoc，进入具体项目后，点击右上角的”项目设置”-“开放API”便可看到
			 * api_token    是    string    同上
			 * cat_name    否    string    可选参数。当页面文档处于目录下时，请传递目录名。当目录名不存在时，showdoc会自动创建此目录
			 * cat_name_sub    否    string    可选参数。当页面文档处于更细分的子目录下时，请传递子目录名。当子目录名不存在时，showdoc会自动创建此子目录
			 * page_title    是    string    页面标题。请保证其唯一。（或者，当页面处于目录下时，请保证页面标题在该目录下唯一）。当页面标题不存在时，showdoc将会创建此页面。当页面标题存在时，将用page_content更新其内容
			 * page_content    是    string    页面内容，可传递markdown格式的文本或者html源码
			 * s_number    否    number    可选，页面序号。默认是99。数字越小，该页面越靠前
			 */

			$url = 'http://showdoc.lhp/server/index.php?s=/api/item/updateByApi';
			$api_key = 'd89c63191a16e04a99d5c1dd82e0fd5c932685343';
			$api_token = 'dc66a28019220a6387c11b9a45e804a41418390012';
			foreach ($data as $class)
			{

				foreach ($class['method'] as $method)
				{
					$data = [
						'api_key'      => $api_key,
						'api_token'    => $api_token,
						'cat_name'     => $class['title'],
						'cat_name_sub' => $method['data']['methodName'],
						'page_title'   => $method['data']['methodName'],
						'page_content' => $method['doc'],
						's_number'     => '1',
					];
//					dump($data);
					$dres = ApiDoc::curl_http_post($url, $data);

				}
			}
		}


	}