<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-09-16
	//==============================================================================

	function is_email($string)
		{
			$s = trim(strtolower($string));
			return preg_match('/\b[A-Za-z0-9._%+-]+@(?!example)[A-Za-z0-9.-]+\.[A-Za-z]{2,}(?<!jpg|jpeg|png|gif)\b/i', $s);
		}

	//Deprecated
	function addslashesJ($string)
		{
			return dbescape($string);
		}

	function siman_upload_image($id, $prefix, $postfix = '', $extention = '.jpg')
		{
			global $_uplfilevars;
			$fs = $_uplfilevars["userfile".$postfix]['tmp_name'];
			if (!empty($fs))
				{
					$fd = 'files/img/'.$prefix.$id.$extention;
					if (file_exists($fd))
						unlink($fd);
					$res = move_uploaded_file($fs, $fd);
					if ($res !== FALSE)
						sm_event('afteruploadedimagesave', array($fd));
					return $res;
				}
			else
				return false;
		}

	function siman_generate_protect_code()
		{
			global $_sessionvars, $session_prefix;
			$code = rand(0, 9999);
			while (strlen($code)<4)
				$code = '0'.$code;
			$_sessionvars['protect_code'] = $code;
		}

	function send_mail($from, $to, $subject, $message, $attachment = '', $filename = '')
		{
			global $lang;
			$eol = "\r\n";
			$boundary = '----=_Part_'.md5(uniqid(time()));
			if ($from and $a = strpos($from, '<') and strpos($from, '>', $a))
				$from = "=?".$lang["charset"]."?B?".base64_encode(trim(substr($from, 0, $a)))."?= ".trim(substr($from, $a));
			$headers =
				($from ? "From: $from$eol" : '').
					"Content-Type: multipart/mixed; boundary=\"$boundary\"$eol".
					"Content-Transfer-Encoding: 8bit$eol".
					"Content-Disposition: inline$eol".
					"MIME-Version: 1.0$eol";
			$body =
				"$eol--$boundary$eol".
					"Content-Type: text/html; charset=\"".$lang["charset"]."\"; format=\"flowed\"$eol".
					"Content-Disposition: inline$eol".
					"Content-Transfer-Encoding: 8bit$eol$eol".
					$message.$eol;
			if ($attachment and is_readable($attachment) and $data = @file_get_contents($attachment))
				$body .=
					"--$boundary$eol".
						"Content-Type: application/octet-stream; name=\"$filename\"$eol".
						"Content-Disposition: attachment; filename=\"$filename\"$eol".
						"Content-Transfer-Encoding: base64$eol$eol".
						chunk_split(base64_encode($data)).$eol;
			$body .= "--$boundary--$eol";
			return mail($to, "=?".$lang["charset"]."?B?".base64_encode($subject)."?=", $body, $headers);
		}

	// load_file_list('./files/img/', 'jpg|gif|bmp')
	function load_file_list($path, $ext = '')
		{
			$extall = explode('|', $ext);
			$dir = dir($path);
			while ($entry = $dir->read())
				{
					if (empty($ext))
						$u = 1;
					else
						{
							$u = 0;
							for ($j = 0; $j<count($extall); $j++)
								{
									if (strcmp(strtolower(pathinfo($entry, PATHINFO_EXTENSION)), strtolower($extall[$j]))==0)
										{
											$u = 1;
											break;
										}
								}
						}
					if (strcmp($entry, '.') != 0 && strcmp($entry, '..') != 0 && $u == 1)
						$result[] = $entry;
				}
			$dir->close();
			if (is_array($result))
				sort($result);
			return $result;
		}

	function register_filesystem($url, $filename, $comment)
		{
			global $tableprefix;
			return insertsql("INSERT INTO ".$tableprefix."filesystem (`filename_fs`, `url_fs`, `comment_fs`) VALUES ('".dbescape($filename)."', '".dbescape($url)."', '".dbescape($comment)."')");
		}

	function update_filesystem($id, $url, $filename, $comment)
		{
			global $tableprefix;
			execsql("UPDATE ".$tableprefix."filesystem SET filename_fs='".dbescape($filename)."', url_fs='".dbescape($url)."', comment_fs='".dbescape($comment)."' WHERE id_fs=".intval($id)." ");
		}

	function delete_filesystem($id)
		{
			global $tableprefix;
			execsql("DELETE FROM ".$tableprefix."filesystem WHERE id_fs=".intval($id));
		}

	function get_filesystem($id)
		{
			global $tableprefix;
			$result = execsql("SELECT * FROM ".$tableprefix."filesystem WHERE id_fs=".intval($id));
			while ($row = database_fetch_object($result))
				{
					$res['id'] = $row->id_fs;
					$res['url'] = $row->url_fs;
					$res['filename'] = $row->filename_fs;
					$res['comment'] = $row->comment_fs;
				}
			return $res;
		}

	function get_filename($id)
		{
			$r = get_filesystem($id);
			return $r['filename'];
		}

	function cut_str_by_word($str, $count, $end_str)
		{
			$str = strip_tags($str);
			if (strlen($str)>$count)
				{
					$res = explode('<br />', wordwrap($str, $count, '<br />'));
					return $res[0].$end_str;
				}
			else
				return $str;
		}

	function get_groups_list()
		{
			global $sm;
			$result = execsql("SELECT * FROM ".$sm['t']."groups ORDER BY title_group ASC");
			$i = 0;
			$res=Array();
			while ($row = database_fetch_assoc($result))
				{
					$res[$i]['id'] = $row['id_group'];
					$res[$i]['title'] = $row['title_group'];
					$res[$i]['description'] = $row['description_group'];
					$res[$i]['auto'] = $row['autoaddtousers_group'];
					$i++;
				}
			return $res;
		}

	//str ;X;Y;Z; to array {X,Y,Z}
	function get_array_groups($gr)
		{
			$res = explode(';', $gr);
			$res2=Array();
			for ($i = 0; $i<count($res); $i++)
				{
					if (!empty($res[$i]))
						$res2[] = $res[$i];
				}
			return $res2;
		}

	//array {X,Y,Z} to str ;X;Y;Z;
	function create_groups_str($array)
		{
			$str = ';';
			for ($i = 0; $i<count($array); $i++)
				{
					if (!empty($array[$i]))
						$str .= $array[$i].';';
				}
			return $str;
		}

	//return 1 if both groups ;X;Y;Z; ;X;R;T; has the same group in list 
	function compare_groups($gr1, $gr2)
		{
			if (!is_array($gr1))
				$gr1 = get_array_groups($gr1);
			if (!is_array($gr2))
				$gr2 = get_array_groups($gr2);
			for ($i = 0; $i<count($gr1); $i++)
				{
					for ($j = 0; $j<count($gr2); $j++)
						{
							if ($gr1[$i] == $gr2[$j])
								return true;
						}
				}
			return false;
		}

	//Convert group string ;X;Y;Z; or array to SQL
	function convert_groups_to_sql($gr, $fieldname)
		{
			$sql = '';
			if (!is_array($gr))
				$gr = get_array_groups($str);
			for ($i = 0; $i<count($gr); $i++)
				{
					if (!empty($sql))
						$sql .= ' OR ';
					$sql .= ' '.$fieldname.' LIKE \'%;'.$gr[$i].';%\'';
				}
			return $sql;
		}

	define("LOG_NOLOG", 0);
	define("LOG_DANGER", 1);
	define("LOG_LOGIN", 10);
	define("LOG_UPLOAD", 20);
	define("LOG_MODIFY", 30);
	define("LOG_USEREVENT", 100);
	define("LOG_ALL", 120);
	function log_write($type, $description)
		{
			global $sm, $_servervars, $_settings, $userinfo;
			if ($_settings['log_type']>=$type)
				{
					if (function_exists('sm_ip_address'))
						$ip = sm_ip_address();
					else
						$ip = $_servervars['REMOTE_ADDR'];
					$sql = "INSERT INTO ".$sm['t']."log (type, description, ip, time, user) VALUES (".intval($type).", '".dbescape($description)."', '".inet_pton($ip)."', ".time().", '".dbescape($userinfo['login'])."')";
					execsql($sql);
				}
		}

	function delete_file_dir($_target)
		{
			//file?
			if (is_file($_target))
				{
					if (is_writable($_target))
						{
							if (@unlink($_target))
								{
									return true;
								}
						}
					return false;
				}
			//dir?
			if (is_dir($_target))
				{
					if (is_writeable($_target))
						{
							foreach (new DirectoryIterator($_target) as $_res)
								{
									if ($_res->isDot())
										{
											unset($_res);
											continue;
										}
									if ($_res->isFile())
										{
											removeRessource($_res->getPathName());
										}
									elseif ($_res->isDir())
										{
											removeRessource($_res->getRealPath());
										}
									unset($_res);
								}
							if (@rmdir($_target))
								{
									return true;
								}
						}
					return false;
				}
		}

	function add_path($title, $url, $tag='')
		{
			global $special;
			$i = count($special['path']);
			$special['path'][$i]['title'] = $title;
			$special['path'][$i]['url'] = $url;
			$special['path'][$i]['tag'] = $tag;
		}

	function push_path($title, $url)
		{
			global $special;
			$max = count($special['path']);
			if ($max>0)
				for ($i = $max-1; $i>=0; $i++)
					{
						$special['path'][$i]['title'] = $special['path'][$i-1]['title'];
						$special['path'][$i]['url'] = $special['path'][$i-1]['url'];
					}
			$special['path'][0]['title'] = $title;
			$special['path'][0]['url'] = $url;
		}

	function add_path_home()
		{
			global $lang, $_settings;
			add_path($lang['common']['home'], 'https://'.$_settings['resource_url']);
		}

	function add_path_control()
		{
			global $lang;
			add_path($lang['control_panel'], 'index.php?m=admin');
		}

	function add_path_modules()
		{
			global $lang;
			add_path($lang['control_panel'], 'index.php?m=admin');
			add_path($lang['modules_mamagement'], 'index.php?m=admin&d=modules');
		}

	function add_path_current($title=NULL)
		{
			global $sm;
			if ($title===NULL)
				add_path($sm['modules'][0]['title'], sm_this_url(), 'currentpage');
			else
				add_path($title, sm_this_url());
		}

	//nllist - sting with items separated by new line character (s)
	function nllistToArray($nllist, $clean_empty_values = false)
		{
			$list = explode("\n", str_replace("\r", "", $nllist));
			if ($clean_empty_values)
				{
					$r=Array();
					for ($i = 0; $i<count($list); $i++)
						{
							if (strlen($list[$i])>0)
								$r[]=$list[$i];
						}
					return $r;
				}
			else
				return $list;
		}

	function arrayToNllist($array)
		{
			return implode("\r\n", $array);
		}

	function addto_nllist($nllist, $item)
		{
			$nllist = nllistToArray($nllist, false);
			$nllist[] = $item;
			return arrayToNllist($nllist);
		}

	function removefrom_nllist($nllist, $item)
		{
			$a = nllistToArray($nllist, false);
			$b = Array();
			for ($i = 0; $i<count($a); $i++)
				{
					if ($a[$i] != $item)
						$b[] = $a[$i];
				}
			return arrayToNllist($b);
		}

	function removefrom_nllist_index($nllist, $index)
		{
			$a = nllistToArray($nllist, false);
			$b = Array();
			for ($i = 0; $i<count($a); $i++)
				{
					if ($i != $index)
						$b[] = $a[$i];
				}
			return arrayToNllist($b);
		}

	function present_nllist($nllist, $item)
		{
			$a = nllistToArray($nllist, false);
			return in_array($item, $a);
		}

	function out($txt)
		{
			global $special;
			$special['textout'] .= $txt;
		}

	function htmlescape($html)
		{
			global $lang;
			$charset=sm_settings('htmlescapecharset');
			if (empty($charset))
				$charset=$lang['charset'];
			return htmlspecialchars($html, ENT_COMPAT | ENT_HTML401, $charset);
		}

	//Escape for using in javascripts assignment operator x='text'
	function jsescape($text)
		{
			return addslashes(str_replace("\n", ' ', str_replace("\r", ' ', $text)));
		}

?>