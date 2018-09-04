<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/3/3
	 * Time: 上午9:22
	 */

	namespace Tool\Utils;

	class UtilDateTime
	{
		/**
		 * 获取时间差
		 * @param $start_time
		 * @param $end_time
		 * @param string $string
		 * @return false|string
		 */
		static public function wordTime($start_time, $end_time, $string = '前')
		{
			$int = $end_time - $start_time;
			if ($int < 0)
			{
				$int = abs($int);
			}


			if ($int <= 2)
			{
				$str = sprintf('刚刚', $int);
			} elseif ($int < 60)
			{
				$str = sprintf('%d秒' . $string, $int);
			} elseif ($int < 3600)
			{
				$str = sprintf('%d分钟' . $string, floor($int / 60));
			} elseif ($int < 86400)
			{
				$str = sprintf('%d小时' . $string, floor($int / 3600));
			} elseif ($int < 2592000)
			{
				$str = sprintf('%d天' . $string, floor($int / 86400));
			} else
			{
				$str = date('Y-m-d H:i:s', $start_time);
			}
			return $str;
		}

		/**
		 * 两个世界相差时间，*年*月*日*时*分*秒
		 * @param $end_time
		 * @param array $format_array
		 * @return array|string
		 */
		static public function getDifferenceTime($start_time, $end_time, $array = false, $format_array = array('年', '月', '日', '时', '分', '秒'))
		{

			$secondtime = $end_time - $start_time;//期限时间减去现在时间 剩余时间

			$second = $secondtime % 60;//取余得到秒数

			$nowtime = floor($secondtime / 60);//转化成分钟

			$minute = $nowtime % 60;//取余得到分钟数

			$nowtime = floor($nowtime / 60);//转化成小时

			$hour = $nowtime % 24;//取余得到小时数

			$nowtime = floor($nowtime / 24);//转化成天数

			$day = floor($nowtime);//得到天数

			if ($array)
			{
				return [$secondtime, $day, $hour, $minute, $second];
			}
			if ($day == 0 & $hour == 0 & $minute == 0 & $second >= 0)
			{
				return $second . $format_array[5];

			} elseif ($day == 0 & $hour == 0 & $minute >= 0 & $second >= 0)
			{
				return $minute . $format_array[4] . $second . $format_array[5];

			} elseif ($day == 0 & $hour >= 0 & $minute >= 0 & $second >= 0)
			{
				return $hour . $format_array[3] . $minute . $format_array[4] . $second . $format_array[5];

			} elseif ($day > 0 & $hour >= 0 & $minute >= 0 & $second >= 0)
			{
				return $day . $format_array[2] . $hour . $format_array[3] . $minute . $format_array[4] . $second . $format_array[5];

			} else
			{
				return false;
			}
		}

		/**
		 * 返回两个时间的相距时间，*年*月*日*时*分*秒
		 * @param int $one_time 时间一
		 * @param int $two_time 时间二
		 * @param int $return_type 默认值为0，0/不为0则拼接返回，1/*秒，2/*分*秒，3/*时*分*秒/，4/*日*时*分*秒，5/*月*日*时*分*秒，6/*年*月*日*时*分*秒
		 * @param array $format_array 格式化字符，例，array('年', '月', '日', '时', '分', '秒')
		 * @return String or false
		 */
		static public function getRemainderTime($one_time, $two_time, $return_type = 0, $format_array = array('年', '月', '日', '时', '分', '秒'))
		{
			if ($return_type < 0 || $return_type > 6)
			{
				return false;
			}

			if (!(is_int($one_time) && is_int($two_time)))
			{
				return false;
			}
			$remainder_seconds = abs($one_time - $two_time);
			//年
			$years = 0;
			if (($return_type == 0 || $return_type == 6) && $remainder_seconds - 31536000 > 0)
			{
				$years = floor($remainder_seconds / (31536000));
			}
			//月
			$monthes = 0;
			if (($return_type == 0 || $return_type >= 5) && $remainder_seconds - $years * 31536000 - 2592000 > 0)
			{
				$monthes = floor(($remainder_seconds - $years * 31536000) / (2592000));
			}
			//日
			$days = 0;
			if (($return_type == 0 || $return_type >= 4) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - 86400 > 0)
			{
				$days = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000) / (86400));
			}
			//时
			$hours = 0;
			if (($return_type == 0 || $return_type >= 3) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - 3600 > 0)
			{
				$hours = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400) / 3600);
			}
			//分
			$minutes = 0;
			if (($return_type == 0 || $return_type >= 2) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - 60 > 0)
			{
				$minutes = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600) / 60);
			}
			//秒
			$seconds = $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - $minutes * 60;
			$return = false;

			switch ($return_type)
			{

				case 0:
					if ($years > 0)
					{
						$return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					} else if ($monthes > 0)
					{
						$return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					} else if ($days > 0)
					{
						$return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					} else if ($hours > 0)
					{
						$return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					} else if ($minutes > 0)
					{
						$return = $minutes . $format_array[4] . $seconds . $format_array[5];
					} else
					{
						$return = $seconds . $format_array[5];
					}
					break;
				case 1:
					$return = $seconds . $format_array[5];
					break;
				case 2:
					$return = $minutes . $format_array[4] . $seconds . $format_array[5];
					break;
				case 3:
					$return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					break;
				case 4:
					$return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					break;
				case 5:
					$return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					break;
				case 6:
					$return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
					break;
				case 'array':
					$return['time_int'] = $one_time - strtotime($years . '-' . $monthes . '-' . $days . ' ' . $hours . ':' . $minutes . ':' . $seconds);

					$return['years'] = $years;
					$return['monthes'] = $monthes;
					$return['days'] = $days;
					$return['hours'] = $hours;
					$return['minutes'] = $minutes;
					$return['seconds'] = $seconds;


					break;
				default:
					$return = false;
			}
			return $return;
		}

		static public function datetype($datetype = null)
		{
			switch ($datetype)
			{
				case 'terday':
					return [strtotime(date('Y-m-d 0:0:0', time())), time()];
				case 'yesterday'://昨天的开始时间戳和结束时间戳
					$date[] = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
					$date[] = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;

					return $date;
					break;
				case 'week'://本周的开始时间戳和结束时间戳
					$date[] = strtotime(date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)));

					$date[] = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
					return $date;
					break;
				case 'month'://本月的开始时间戳和结束时间戳
					$date[] = strtotime(date("Y-m", time()));

					$date[] = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;

					return $date;
					break;
				case 'year'://本年的开始时间戳和结束时间戳

					$date[] = strtotime(date("Y", time()) . '-1-1');

					$date[] = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
					return $date;
					break;
				default:
					return [
						'yesterday' => [
							date('Y-m-d H:i:s', self::datetype('yesterday')[0]),
							self::datetype('yesterday')[0],
							date('Y-m-d H:i:s', self::datetype('yesterday')[1]),
							self::datetype('yesterday')[1],
						],
						'week'      => [
							date('Y-m-d H:i:s', self::datetype('week')[0]),
							self::datetype('week')[0],
							date('Y-m-d H:i:s', self::datetype('week')[1]),
							self::datetype('week')[1],
						],
						'month'     => [
							date('Y-m-d H:i:s', self::datetype('month')[0]),
							self::datetype('month')[0],
							date('Y-m-d H:i:s', self::datetype('month')[1]),
							self::datetype('month')[1],
						],
						'year'      => [
							date('Y-m-d H:i:s', self::datetype('year')[0]),
							self::datetype('year')[0],
							date('Y-m-d H:i:s', self::datetype('year')[1]),
							self::datetype('year')[1],
						],
					];
					break;
			}
		}


		static public function getYesterday()
		{
			//php获取昨日起始时间戳和结束时间戳
			return [
				'start' => mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')),
				'end'   => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1
			];
		}

		static public function getMonth()
		{
			//php获取本月起始时间戳和结束时间戳
			return [
				'start' => mktime(0, 0, 0, date('m'), 1, date('Y')),
				'end'   => mktime(23, 59, 59, date('m'), date('t'), date('Y'))
			];
		}

		static public function Yesterday()
		{
			return [
				'start' => strtotime(date('y-m-d h:i:s', time())),
				'end'   => strtotime(date("Y-m-d", strtotime("-1 day")))
			];
		}

		/**
		 *  获取本周开始时间戳和当前时间戳
		 * @return array
		 */
		static public function getWeek()
		{
			return [
				'start' => strtotime(date('y-m-d h:i:s', time())),
				'end'   => strtotime(date('Y-m-d h:i:s', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)))
			];

		}

		/**
		 * 获取本月1号的和当前的时间戳
		 * @return array
		 */
		static public function Month()
		{
			return [
				'start' => strtotime(date('y-m-d h:i:s', time())),
				'end'   => mktime(0, 0, 0, date('m'), 1, date('Y'))
			];
		}

		/**
		 * 获取本年1月1号的和当前的时间戳
		 * @return array
		 */
		static public function getYear()
		{

			return [
				'start' => strtotime(date('y-m-d h:i:s', time())),
				'end'   => strtotime(date("Y", time()) . '-1-1'),
			];

		}

		/**
		 * 计算两个日期相隔多少天
		 * ram $date2
		 */
		static function diffDate($date1, $date2)
		{
			$datetime1 = date_create($date1);
			$datetime2 = date_create($date2);
			$interval = date_diff($datetime1, $datetime2);
			return $interval->format('%a');
		}


		static public function someone()
		{
			date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)); //w为星期几的数字形式,这里0为周日


//本周日

			date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)); //同样使用w,以现在与周日相关天数算


//上周一

			date('Y-m-d', strtotime('-1 monday', time())); //无论今天几号,-1 monday为上一个有效周未


//上周日

			date('Y-m-d', strtotime('-1 sunday', time())); //上一个有效周日,同样适用于其它星期


//本月一日

			date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00')); //直接以strtotime生成


//本月最后一日

			date('Y-m-d', strtotime(date('Y-m', time()) . '-' . date('t', time()) . ' 00:00:00')); //t为当月天数,28至31天


//上月一日

			date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m', time()) . '-01 00:00:00'))); //本月一日直接strtotime上减一个月


//上月最后一日

			date('Y-m-d', strtotime(date('Y-m', time()) . '-01 00:00:00') - 86400); //本月一日减一天即是上月最后一日


			//php获取今日开始时间戳和结束时间戳
			$beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

			//php获取上周起始时间戳和结束时间戳
			$beginLastweek = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'));
			$endLastweek = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y'));

			$datetype = 'yesterday';
			switch ($datetype)
			{
				case 'yesterday'://昨天的开始时间戳和结束时间戳
					$date['start'] = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
					$date['end'] = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;

//					$date['start'] = strtotime(date('Y-m-d 00:00:00', time() - $day));
//					$date['end'] = strtotime(date('Y-m-d 23:59:59', time() - ($day * 1)));
					return $date;
					break;
				case 'week'://本周的开始时间戳和结束时间戳
					$date['start'] = strtotime(date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)));

					$date['end'] = strtotime(date("Y-m-d 23:59:59", time()));
					return $date;
					break;
				case 'month'://本月的开始时间戳和结束时间戳
					$date['start'] = strtotime(date("Y-m", time()));

					$date['end'] = strtotime(date("Y-m-d 23:59:59", time()));

					return $date;
					break;
				case 'year'://本年的开始时间戳和结束时间戳
					$year = date("Y", time());

					$date['start'] = strtotime("{$year}-01-01 00:00:00");

					$date['end'] = strtotime(date("Y-m-d 23:59:59", time()));

					return $date;
					break;

				default:
					return '参数错误！';

			}
		}


		/**
		 * 求两个日期之间相差的天数
		 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
		 * @param string $day1
		 * @param string $day2
		 * @return number
		 */
		public static function diffBetweenTwoDays($day1, $day2)
		{
			$second1 = strtotime($day1);
			$second2 = strtotime($day2);

			if ($second1 < $second2)
			{
				$tmp = $second2;
				$second2 = $second1;
				$second1 = $tmp;
			}
			return ($second1 - $second2) / 86400;
		}

	}