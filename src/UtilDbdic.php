<?php
	/**
	 * PHP 数据字典，直接给数据库连接信息即可 不依赖框架.
	 * 可以使用锚点快速定位表
	 * User: mybook-lhp
	 * Date: 18/6/20
	 * Time: 下午2:52
	 */

	namespace Tool\Utils;

	use \PDO;
	use \PDOException;

	class UtilDbdic
	{
		protected        $html_tpl;
		protected        $table_tpl;
		protected        $table_data;
		static protected $Init = null;

		static public function Init()
		{
			if (static::$Init == null)
			{
				static::$Init = new static();
			}
			return static::$Init;
		}

		static public function flash($DbConfig)
		{
			return static::Init()->export_dict($DbConfig)->table()->html();
		}

		/**
		 * @param $config ['database']
		 * @param $config
		 * @return $this
		 */
		public function export_dict($config)
		{
			$dsn = 'mysql:dbname=' . $config['database'] . ';host=' . $config['hostname'];
			//数据库连接
			try
			{
				$con = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
			} catch (PDOException $e)
			{
				die('Connection failed: ' . $e->getMessage());
			}


			$tables = $con->query('SHOW tables')->fetchAll(PDO::FETCH_COLUMN);

			//取得所有的表名
			foreach ($tables as $table)
			{
				$this->table_data[]['TABLE_NAME'] = $table;
			}

			//循环取得所有表的备注及表中列消息
			foreach ($this->table_data as $k => $v)
			{

				$sql = 'SELECT * FROM ';
				$sql .= 'INFORMATION_SCHEMA.TABLES ';
				$sql .= 'WHERE ';
				$sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$config['database']}'";
				$tr = $con->query($sql)->fetch(PDO::FETCH_ASSOC);
				$this->table_data[$k]['TABLE_COMMENT'] = $tr['TABLE_COMMENT'];

				$sql = 'SELECT * FROM ';
				$sql .= 'INFORMATION_SCHEMA.COLUMNS ';
				$sql .= 'WHERE ';
				$sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$config['database']}'";
				$fields = [];
				$field_result = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);

				foreach ($field_result as $fr)
				{
					$fields[] = $fr;
				}
				$this->table_data[$k]['COLUMN'] = $fields;
			}
			unset($con);
			return $this;

		}

		/**
		 * @return $this
		 */
		public function table()
		{
			$this->table_tpl = '';

			//循环所有表
			foreach ($this->table_data as $k => $v)
			{
				$this->table_tpl .= '<a name="'.$v['TABLE_NAME'].'"></a>';
				$this->table_tpl .= '<table  class="zebra">';

				$this->table_tpl .= '<caption>' . $v['TABLE_NAME'] . $v['TABLE_COMMENT'] . '</caption>';
				$this->table_tpl .= '<br>' . PHP_EOL;
				$this->table_tpl .= '<thead><tr><th>字段名 </th><th> 数据类型</th><th>默认值</th><th>允许非空</th><th>自动递增</th><th>备注</th></tr></thead><tbody>';
				foreach ($v['COLUMN'] as $f)
				{
					$this->table_tpl .= '<tr>';
					$this->table_tpl .= '<td>' . $f['COLUMN_NAME'] . '</td><td>' . $f['COLUMN_TYPE'] . '</td><td>' . $f['COLUMN_DEFAULT'] . '</td><td>' . $f['IS_NULLABLE'] . '</td><td>' . ($f['EXTRA'] == 'auto_increment' ? '是' : '') . '</td><td>' . (empty($f['COLUMN_COMMENT']) ? '-' : str_replace('|', '/', $f['COLUMN_COMMENT'])) . '</td>' . PHP_EOL;
					$this->table_tpl .= '</tr>';
				}

				$this->table_tpl .= '</tbody></table>';
			}
			return $this;
		}

		/**
		 * @param $table
		 * @return string
		 */
		public function html()
		{
			return <<<EOC
	<!DOCTYPE html>
<html  >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Beautiful design tables in HTML in the style of a zebra.</title>
    <style>
     
html, body{
	padding:0;
	margin:0;
	position:relative;
	background:url(../img/body.jpg);
	background-repeat:repeat;
	color:#151515;
	letter-spacing:1px;
	font-family:Georgia, "Times New Roman", Times, serif;
}

.zebra caption{
	font-size:20px;
	font-weight:normal;
	background:url(../img/zebratable.png);
	background-repeat:no-repeat;
	background-position: 130px center;
	padding-top: 20px;
	height:50px;}

#container{
	padding-top:20px;
	width:960px;
	margin:0 auto;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
	width:100%;
	-webkit-box-shadow:  0px 2px 1px 5px rgba(242, 242, 242, 0.1);
    box-shadow:  0px 2px 1px 5px rgba(242, 242, 242, 0.1);
}

.zebra {
    border: 1px solid #555;
}

.zebra td {
    border-left: 1px solid #555;
    border-top: 1px solid #555;
    padding: 10px;
    text-align: left;    
}

.zebra th, .zebra th:hover {
    border-left: 1px solid #555;
	border-bottom: 1px solid #828282;
    padding: 20px;  
    background-color:#151515 !important;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#151515), to(#404040)) !important;
    background-image: -webkit-linear-gradient(top, #151515, #404040) !important;
    background-image:    -moz-linear-gradient(top, #151515, #404040) !important;
    background-image:     -ms-linear-gradient(top, #151515, #404040) !important;
    background-image:      -o-linear-gradient(top, #151515, #404040) !important;
    background-image:         linear-gradient(top, #151515, #404040) !important;
	color:#fff !important;
	font-weight:normal;
}

.zebra tbody tr:nth-child(even) {
    background: #000 !important;
	color:#fff;
}

.zebra tr:hover *{
    background: #eeeeee;
	color:#000;
}

.zebra tr {
	background:#404040;
	color:#fff;
}
    </style>
</head>

<body>

<div id="container">   
{$this->table_tpl}
</div>
    
</body>
</html>

EOC;
		}

	}