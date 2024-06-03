<?php

	if (!defined("SMDateTime_lib_DEFINED"))
		{
			class SMDateTime
				{
					public static function Now()
						{
							return time();
						}
					
					public static function MakeTime($hr, $min, $sec, $mon, $day, $yr)
						{
							return mktime($hr, $min, $sec, $mon, $day, $yr);
						}
					
					public static function MakeTimeAMPM($hr, $hr_am_pm, $min, $sec, $mon, $day, $yr)
						{
							if ($hr_am_pm=='pm' && intval($hr)!=12)
								$hr+=intval($hr)+12;
							elseif ($hr_am_pm=='am' && $hr_am_pm=12)
								$hr=0;
							return SMDateTime::MakeTime(intval($hr), intval($min), intval($sec), intval($mon), intval($day), intval($yr));
						}

					public static function Month($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('m', $time));
						}
					
					public static function Date($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('d', $time));
						}
					
					public static function Year($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('Y', $time));
						}
					
					public static function Hour24($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('H', $time));
						}
					
					public static function Hour12($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('g', $time));
						}
					
					public static function Min($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('i', $time));
						}
					
					public static function AMPMUpperCased($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return strtoupper(date('A', $time));
						}
					
					public static function AMPMLowerCased($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return strtolower(date('A', $time));
						}
					
					public static function DayOfWeek($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return intval(date('N', $time));
						}
					
					public static function incYear($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time), SMDateTime::Date($time), SMDateTime::Year($time)+1);
						}
					
					public static function incMonth($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							if (SMDateTime::Date($time)<28)
								$time2=$time+3600*24*31;
							else
								$time2=$time+3600*24*10;
							$newtime=SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time2), SMDateTime::Date($time), SMDateTime::Year($time2));
							if ($newtime===false || $newtime==-1)
								{
									$newtime=SMDateTime::DayStart(SMDateTime::MonthEnd($time2));
								}
							return $newtime;
						}
					
					public static function WeekStart($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							$time+=3600*24*(1-SMDateTime::DayOfWeek($time));
							return SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time), SMDateTime::Date($time), SMDateTime::Year($time));
						}
					
					public static function MonthStart($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time), 1, SMDateTime::Year($time));
						}
					
					public static function MonthEnd($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							if (SMDateTime::Date($time)<28)
								$time=$time+3600*24*31;
							else
								$time=$time+3600*24*10;
							return SMDateTime::MonthStart($time)-1;
						}

					public static function DayStart($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time), SMDateTime::Date($time), SMDateTime::Year($time));
						}

					public static function DayEnd($time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							$time+=3600*24+5;
							return SMDateTime::DayStart($time)-1;
						}

					public static function Add($time=NULL, $days=0, $hours=0, $minutes=0, $seconds=0)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							$time+=3600*24*$days+3600*$hours+60*$minutes+$seconds;
							return SMDateTime::MakeTime(0, 0, 0, SMDateTime::Month($time), SMDateTime::Date($time), SMDateTime::Year($time));
						}
					
					public static function Format($format, $time=NULL)
						{
							if ($time===NULL)
								$time=SMDateTime::Now();
							return strftime($format, $time);
						}
					
					public static function MYSQLDate($time=NULL)
						{
							return SMDateTime::Format('%Y-%m-%d', $time);
						}
					
					public static function MYSQLDateToUnixtime($mysqldate)
						{
							$t=explode('-', $mysqldate);
							return SMDateTime::MakeTime(0, 0, 0, intval($t[1]), intval($t[2]), intval($t[0]));
						}
					
					public static function USDateTimeFormat($time=NULL)
						{
							return SMDateTime::Format('%m/%d/%Y, %I:%M %p', $time);
						}
					
					public static function USDateFormat($time=NULL)
						{
							return SMDateTime::Format('%m/%d/%Y', $time);
						}
					
					public static function USTimeFormat($time=NULL)
						{
							return SMDateTime::Format('%I:%M %p', $time);
						}
					
					public static function HasTheSameDate($timestamp1, $timestamp2)
						{
							return strcmp(SMDateTime::MYSQLDate($timestamp1), SMDateTime::MYSQLDate($timestamp2))==0;
						}
					
					public static function FromMYSQLDate($yyyy_mm_dd)
						{
							$tmp=explode('-', $yyyy_mm_dd);
							return SMDateTime::MakeTime(0, 0, 0, $tmp[1], $tmp[2], $tmp[0]);
						}
					
					public static function TimestampFromUSDateAndTime($date, $time='')
						{
							if (empty($time))
								$time='12:00 am';
							$time=strtolower(trim($time));
							$t=explode(' ', $time);
							$t1=explode(':', $t[0]);
							$h=intval($t1[0]);
							$m=intval($t1[1]);
							if ($h==12 && strtolower($t[1])=='am')
								$h=0;
							elseif ($h!=12 && strtolower($t[1])=='pm')
								$h+=12;
							$tmp=explode('/', trim($date));
							return SMDateTime::MakeTime($h, $m, 0, $tmp[0], $tmp[1], $tmp[2]);
						}
					
				}
			define("SMDateTime_lib_DEFINED", 1);
		}

?>