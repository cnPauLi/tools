<?php

	namespace Tool\Utils;

	class  UtilStateCode
	{

		/**
		 * 以下这三个状态码是设定浏览器的成功返回的状态码的。
		 */
		const SUCCESS = '200'; //成功返回的数据

		/**
		 * 以下的状态码是标示错误号码的
		 */
		const REQUEST_ERROR = '1000'; //请求失败
		const LOGIN_REQUIRE = '1001'; //未登录
		const ACCESS_ERROR  = '1002'; //没有权限
		const SOME_ERROR    = '1003'; //部分数据操作失败
		/**
		 * 部分数据操作失败
		 */
		const ALL_ERROR     = '1004'; //部分数据操作失败

	}