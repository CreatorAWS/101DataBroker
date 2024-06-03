<?php

	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.7
	//#revision 2014-07-22
	//==============================================================================

	function sm_add_user($login, $password, $email, $question = '', $answer = '', $user_status = '1')
		{
			global $tableusersprefix;
			$password = md5($password);
			$q = new TQuery($tableusersprefix.'users');
			$q->Add('login', dbescape($login));
			$q->Add('password', dbescape($password));
			$q->Add('email', dbescape($email));
			$q->Add('question', dbescape($question));
			$q->Add('answer', dbescape($answer));
			$q->Add('user_status', intval($user_status));
			$q->Add('random_code', md5(time().rand()));
			$groups = get_groups_list();
			for ($i = 0; $i < count($groups); $i++)
				{
					if ($groups[$i]['auto'] == 1)
						{
							$u[] = $groups[$i]['id'];
						}
				}
			if (count($u) > 0)
				{
					$groups_user = create_groups_str($u);
					$q->Add('groups_user', $groups_user);
				}
			$id = $q->Insert();
			return $id;
		}

	/*
	$showas values:
		text
		password
		textarea
		checkbox
		radio
	*/
	function sm_add_userfield($fieldname, $show_as = 'text', $allowed_values = '', $replaceforallvalue = '')
		{
			global $tableusersprefix;
			$sql = "ALTER TABLE `".$tableusersprefix."users` ADD `".$fieldname."` TEXT NULL ;";
			execsql($sql);
			$allowed[] = 'text';
			$allowed[] = 'password';
			$allowed[] = 'textarea';
			$allowed[] = 'checkbox';
			$allowed[] = 'radio';
			if (!in_array($show_as, $allowed))
				$show_as = 'text';
			sm_add_settings($fieldname.'_show_as', $show_as, 'custom_user_fields');
			sm_add_settings($fieldname.'_allowed_values', $allowed_values, 'custom_user_fields');
			if (!empty($replaceforallvalue))
				execsql("UPDATE ".$tableusersprefix."users SET `".$fieldname."`='".$replaceforallvalue."'");
		}

	function sm_delete_userfield($fieldname)
		{
			global $tableusersprefix;
			$sql = "ALTER TABLE `".$tableusersprefix."users` DROP `".$fieldname."`;";
			execsql($sql);
			sm_delete_settings($fieldname.'_show_as', 'custom_user_fields');
			sm_delete_settings($fieldname.'_allowed_values', 'custom_user_fields');
			if (!empty($replaceforallvalue))
				execsql("UPDATE ".$tableusersprefix."users SET `".$fieldname."`='".$replaceforallvalue."'");
		}

	function sm_get_offsetforpage($pagenumber, $limitcount)
		{
			if (intval($pagenumber) < 1)
				$pagenumber = 1;
			return abs((intval($pagenumber) - 1) * intval($limitcount));
		}

	function sm_get_pagescount($totalcount, $itemsperpage)
		{
			if ($totalcount == 0) return 1;
			return floor(($totalcount - 1) / $itemsperpage) + 1;
		}

	function sm_resizeimage($inputfile, $outputfile, $neededwidth, $neededheight, $skipifimageless = 1, $quality = 100, $needcrop = 0)
		{
			include_once('ext/resizer/resizer.php');
			$result = @resized_image($inputfile, $outputfile, $neededwidth, $neededheight, $skipifimageless, $quality, $needcrop);
			sm_event('afterresizedimagesave', array($outputfile));
			return $result;
		}

	//This function is deprecated - see sm_url instead
	function sm_change_url_param($url, $param_name, $param_value)
		{
			return sm_url($url, $param_name, $param_value);
		}

	function sm_add_group($title_group, $description_group, $autoaddtousers_group = 0)
		{
			global $sm;
			$q = new TQuery($sm['t'].'groups');
			$q->Add('title_group', addslashes($title_group));
			$q->Add('description_group', addslashes($description_group));
			$q->Add('autoaddtousers_group', intval($autoaddtousers_group));
			return $q->Insert();
		}

	function sm_set_group($id_group, $user_ids = Array())
		{
			for ($i = 0; $i < count($user_ids); $i++)
				{
					sm_set_taxonomy('usergroups', $user_ids[$i], $id_group);
				}
		}

	function sm_unset_group($id_group, $user_ids = Array())
		{
			for ($i = 0; $i < count($user_ids); $i++)
				{
					sm_unset_taxonomy('usergroups', $user_ids[$i], $id_group);
				}
		}

	function sm_delete_group($id_group)
		{
			global $sm;
			$q = new TQuery($sm['t'].'groups');
			$q->Add('id_group', intval($id_group));
			$q->Remove();
			sm_unset_group($id_group, sm_get_taxonomy('usergroups', $id_group, true));
		}

	function sm_tempdata_addtext($type, $identifier, $data, $timetolive = 3600)
		{
			global $tableprefix;
			$q = new TQuery($tableprefix."tempdata");
			$q->Add('type_td', dbescape($type));
			$q->Add('identifier_td', dbescape($identifier));
			$q->Add('data_td_text', dbescape($data));
			$q->Add('deleteafter_td', time() + intval($timetolive));
			$q->Insert();
		}

	function sm_tempdata_addint($type, $identifier, $data, $timetolive = 3600)
		{
			global $tableprefix;
			$q = new TQuery($tableprefix."tempdata");
			$q->Add('type_td', dbescape($type));
			$q->Add('identifier_td', dbescape($identifier));
			$q->Add('data_td_int', intval($data));
			$q->Add('deleteafter_td', time() + intval($timetolive));
			$q->Insert();
		}

	function sm_tempdata_gettext($type, $identifier, $data = NULL)
		{
			global $tableprefix;
			$sql = "SELECT data_td_text FROM ".$tableprefix."tempdata WHERE type_td='".dbescape($type)."' AND identifier_td='".dbescape($identifier)."'";
			if ($data !== NULL)
				$sql .= " AND data_td_text='".dbescape($data)."'";
			return getsqlfield($sql);
		}

	function sm_tempdata_getint($type, $identifier, $data = NULL)
		{
			global $tableprefix;
			$sql = "SELECT data_td_int FROM ".$tableprefix."tempdata WHERE type_td='".dbescape($type)."' AND identifier_td='".dbescape($identifier)."'";
			if ($data !== NULL)
				$sql .= " AND data_td_int='".dbescape($data)."'";
			return getsqlfield($sql);
		}

	function sm_tempdata_remove($type, $identifier, $data = NULL)
		{
			global $tableprefix;
			$sql = "DELETE FROM ".$tableprefix."tempdata WHERE type_td='".dbescape($type)."' AND identifier_td='".dbescape($identifier)."'";
			if ($data !== NULL)
				$sql .= " AND (data_td_int='".intval($data)."' OR data_td_text='".dbescape($data)."')";
			execsql($sql);
		}

	function sm_tempdata_clean($type = NULL, $identifier = NULL, $data = NULL)
		{
			global $tableprefix;
			$sql = "DELETE FROM ".$tableprefix."tempdata WHERE deleteafter_td<=".time();
			if ($type !== NULL)
				$sql .= " AND type_td='".dbescape($type)."'";
			if ($identifier !== NULL)
				$sql .= " AND identifier_td='".dbescape($identifier)."'";
			if ($data !== NULL)
				$sql .= " AND (data_td_int='".intval($data)."' OR data_td_text='".dbescape($data)."')";
			execsql($sql);
		}

	define("SM_AGGREGATE_SUM", 'sum');
	define("SM_AGGREGATE_COUNT", 'count');
	define("SM_AGGREGATE_MAX", 'max');
	define("SM_AGGREGATE_MIN", 'min');
	define("SM_AGGREGATE_AVG", 'avg');

	function sm_tempdata_aggregate($type, $identifier, $resulttype = SM_AGGREGATE_COUNT, $data = NULL)
		{
			global $tableprefix;
			if ($resulttype == SM_AGGREGATE_COUNT)
				$returntype = 'count(*)';
			else
				$returntype = $resulttype.'(data_td_int)';
			$sql = "SELECT ".$returntype." FROM ".$tableprefix."tempdata WHERE type_td='".dbescape($type)."' AND identifier_td='".dbescape($identifier)."'";
			if ($data !== NULL)
				$sql .= " AND data_td_int='".dbescape($data)."'";
			return getsqlfield($sql);
		}

	function sm_error_page($title, $message, $header_error_code = '')
		{
			global $special, $modules, $lang;
			$modules[0]['error_message'] = $message;
			$modules[0]['module'] = '';
			$modules[0]['mode'] = md5('error');
			if (empty($title))
				$modules[0]['title'] = $lang["error"];
			else
				$modules[0]['title'] = $title;
			$modules[0]['error_type'] = 'custom';
			if (!empty($header_error_code))
				$special['header_error_code'] = $header_error_code;
		}

	function sm_access_denied($message = NULL)
		{
			global $lang;
			if ($message === NULL)
				$message = $lang['access_denied'];
			sm_error_page($lang["error"], $message, '423 Locked');
		}

	function sm_autobannedip_cleanup()
		{
			global $_settings;
			if (!empty($_settings['autoban_ips']))
				{
					$newbanip = $_settings['autoban_ips'];
					$banip = nllistToArray($_settings['autoban_ips']);
					for ($i = 0; $i < count($banip); $i++)
						{
							if (intval(sm_tempdata_aggregate('bannedip', $banip[$i], SM_AGGREGATE_COUNT)) == 0)
								{
									$newbanip = removefrom_nllist($newbanip, $banip[$i]);
								}
						}
					if ($newbanip != $_settings['autoban_ips'])
						sm_update_settings('autoban_ips', $newbanip);
				}
		}

	function sm_logout()
		{
			global $_sessionvars, $userinfo, $lang, $tableusersprefix;
			$sql = "UPDATE ".$tableusersprefix."users SET id_session=NULL WHERE id_user='".intval($userinfo['id'])."'";
			execsql($sql);
			sm_event('userlogout', array($userinfo['id']));
			log_write(LOG_LOGIN, $lang['module_account']['log']['user_logout']);
			$_sessionvars['userinfo_id'] = '';
			$_sessionvars['userinfo_login'] = '';
			$_sessionvars['userinfo_email'] = '';
			$_sessionvars['userinfo_level'] = '0';
			$_sessionvars['userinfo_groups'] = '';
			$_sessionvars['userinfo_allinfo'] = '';
		}
	
	function sm_process_login($user_id)
		{
			global $userinfo, $_sessionvars;
			if (sm_login($user_id))
				{
					include('includes/userinfo.php');
					sm_event('successlogin', array($userinfo['id']));
				}
		}

	function sm_url_content($url, $postvars=Array(), $timeout=5)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			if (sm_settings('curl_default_useragent'))
				curl_setopt($ch, CURLOPT_USERAGENT, sm_settings('curl_default_useragent'));
			if (!empty($postvars))
				{
					$postvars=http_build_query($postvars);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
				}
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (!($out = curl_exec($ch)))
				$out=false;
			curl_close($ch);
			return $out;
		}

	function sm_download_file($url, $filename, $postvars=Array(), $timeout=5)
		{
			$ch = curl_init($url);
			if (file_exists($filename))
				unlink($filename);
			$fp = fopen($filename, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			if (sm_settings('curl_default_useragent'))
				curl_setopt($ch, CURLOPT_USERAGENT, sm_settings('curl_default_useragent'));
			if (!empty($postvars))
				{
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
				}
			curl_exec($ch);
			$tmperr = curl_error($ch);
			curl_close($ch);
			fclose($fp);
			if (!empty($tmperr))
				unlink($filename);
			return file_exists($filename);
		}
	
	function sm_check_user($login, $password)
		{
			global $sm;
			$usr_name = dbescape(strtolower($login));
			$usr_passwd = md5($password);
			if (sm_settings('signinwithloginandemail')==1)
				$id = getsqlfield("SELECT id_user FROM ".$sm['tu']."users WHERE (lower(login)='$usr_name' OR lower(email)='$usr_name') AND password='$usr_passwd' AND user_status>0 LIMIT 1");
			else
				$id = getsqlfield("SELECT id_user FROM ".$sm['tu']."users WHERE lower(login)='$usr_name' AND password='$usr_passwd' AND user_status>0 LIMIT 1");
			if (intval($id)!=0)
				return intval($id);
			else
				return false;
		}
	
	function sm_tomenuurl($title, $url, $returnto='')
		{
			return 'index.php?m=menu&d=addouter&p_caption='.urlencode($title).'&p_url='.urlencode($url).'&returnto='.urlencode($returnto);
		}
	
	function sm_saferemove($url)
		{
			if (empty($url))
				return;
			global $sm;
			$items=Array();
			$q=new TQuery($sm['t'].'menu_lines');
			$q->Add('url', dbescape($url));
			$q->Remove();
			$q=new TQuery($sm['t'].'filesystem');
			$q->Add('url_fs', dbescape($url));
			$q->Select();
			for ($i = 0; $i < $q->Count(); $i++)
				{
					$items[]=$q->items[$i]['filename_fs'];
				}
			$q=new TQuery($sm['t'].'filesystem');
			$q->Add('url_fs', dbescape($url));
			$q->Remove();
			sm_event('saferemove', Array($url));
			for ($i = 0; $i < count($items); $i++)
				{
					sm_saferemove($items[$i]);
				}
		}

	function sm_fs_exists($fs_url)
		{
			global $sm;
			$q = new TQuery($sm['t'].'filesystem');
			$q->Add('filename_fs', dbescape($fs_url));
			return intval($q->GetField('id_fs'))>0;
		}

	function sm_fs_autogenerate($name, $extension='.html', $prefix_path='')
		{
			$name=$prefix_path.sm_getnicename($name);
			if (!sm_fs_exists($name.$extension))
				return $name.$extension;
			$i=2;
			while (true)
				{
					$tmp=$name.'-'.$i.$extension;
					if (!sm_fs_exists($tmp))
						return $tmp;
					$i++;
				}
		}

	function sm_is_allowed_to_upload($filename)
		{
			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			$disallowed=explode('|', sm_settings('disallowed_upload_extensions'));
			if (count($disallowed)==0)
				return false;
			return !in_array($ext, $disallowed);
		}
	
	function sm_ban_ip($bantime, $ip=NULL)
		{
			global $sm;
			if ($ip==NULL)
				$ip=sm_ip_address();
			sm_update_settings('autoban_ips', addto_nllist(sm_get_settings('autoban_ips'), $ip));
			sm_tempdata_addint('bannedip', $ip, time(), intval($bantime));
		}
	
	//Return array of ids or empty array
	function sm_metadata_objectids_for($object_name, $key_name, $val=NULL)
		{
			global $sm;
			$q=new TQuery($sm['t'].'metadata');
			$q->Add('object_name', dbescape($object_name));
			$q->Add('key_name', dbescape($key_name));
			if ($val!==NULL)
				$q->Add('val', dbescape($val));
			$q->SelectFields('DISTINCT object_id as oid');
			$q->Select();
			return $q->ColumnValues('oid');
		}

	function sm_unset_taxonomy($object_name, $object_id, $rel_id)
		{
			global $sm;
			if (is_array($rel_id))
				{
					for ($i = 0; $i<count($rel_id); $i++)
						{
							sm_unset_taxonomy($object_name, $object_id, $rel_id[$i]);
							return;
						}
				}
			$q=new TQuery($sm['t'].'taxonomy');
			$q->Add('object_name', dbescape($object_name));
			$q->Add('object_id', intval($object_id));
			$q->Add('rel_id', intval($rel_id));
			$q->Remove();
		}

	function sm_set_taxonomy($object_name, $object_id, $rel_id)
		{
			global $sm;
			if (is_array($rel_id))
				{
					for ($i = 0; $i<count($rel_id); $i++)
						{
							sm_set_taxonomy($object_name, $object_id, $rel_id[$i]);
							return;
						}
				}
			if (in_array($rel_id, sm_get_taxonomy($object_name, $object_id)))
				return;
			$q=new TQuery($sm['t'].'taxonomy');
			$q->Add('object_name', dbescape($object_name));
			$q->Add('object_id', intval($object_id));
			$q->Add('rel_id', intval($rel_id));
			$q->Insert();
		}
	
	function sm_get_log($object_name, $object_id, $sort_desc=true, $limit=0, $offset=0)
		{
			global $sm;
			$q=new TQuery($sm['t'].'log');
			$q->Add('object_name', dbescape($object_name));
			$q->Add('object_id', dbescape($object_id));
			if ($sort_desc)
				$q->OrderBy('id_log DESC');
			else
				$q->OrderBy('id_log ASC');
			if ($limit!==0)
				{
					$q->Limit($limit);
					if ($offset!==0)
						$q->Offset($offset);
				}
			$q->Select();
			for ($i = 0; $i < $q->Count(); $i++)
				{
					$q->items[$i]['ip']=inet_ntop($q->items[$i]['ip']);
				}
			return $q->items;
		}

	function sm_search_query_sql($query, $fields)
		{
			if (!is_array($fields))
				$fields=explode(' ', $fields);
			$keywords = explode(' ', preg_replace('/\s+/', ' ', $query));
			if (count($fields)==0 || count($keywords)==0)
				return '(1=2)';
			$r='';
			for ($i = 0; $i<count($keywords); $i++)
				{
					if ($i>0)
						$r.=' AND ';
					$sql='(';
					for ($j = 0; $j<count($fields); $j++)
						{
							if ($j>0)
								$sql.=' OR ';
							$sql.="`".$fields[$j]."` LIKE '%".dbescape($keywords[$i])."%'";
						}
					$r.=$sql.')';
				}
			return '('.$r.')';
		}

?>
