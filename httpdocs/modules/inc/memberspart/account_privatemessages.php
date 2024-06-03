<?php

	//------------------------------------------------------------------------------
	//|            Content Management System SiMan CMS                             |
	//|                http://www.simancms.org                                     |
	//------------------------------------------------------------------------------

	//==============================================================================
	//#ver 1.6.5
	//#revision 2013-10-27
	//==============================================================================

	if (!defined("SIMAN_DEFINED"))
		{
			print('Hacking attempt!');
			exit();
		}

	if (!defined("ACCOUNT_MEMBERS_PRIVMSG_FUNCTIONS_DEFINED"))
		{
			function siman_get_userid_by_name($name)
				{
					global $tableusersprefix, $_settings;
					$sql = "SELECT * FROM ".$tableusersprefix."users WHERE lower(login)=lower('".dbescape($name)."') LIMIT 1";
					$result = execsql($sql);
					$id = 0;
					while ($row = database_fetch_object($result))
						{
							$id = $row->id_user;
						}
					return $id;
				}

			function siman_get_privmsgcount($user)
				{
					global $nameDB, $lnkDB, $tableusersprefix, $tableprefix;
					$result['inbox']['all'] = 0;
					$result['inbox']['unread'] = 0;
					$result['outbox']['all'] = 0;
					$sql = "SELECT count(*), sum(unread_privmsg), sum(folder_privmsg) FROM ".$tableusersprefix."privmsg WHERE folder_privmsg=1 AND id_sender_privmsg=$user OR folder_privmsg=0 AND id_recipient_privmsg=$user";
					$rezult = database_db_query($nameDB, $sql, $lnkDB);
					while ($row = database_fetch_row($rezult))
						{
							$result['inbox']['all'] = $row[0];
							$result['inbox']['unread'] = $row[1];
							$result['outbox']['all'] = $row[2];
						}
					return $result;
				}

			define("ACCOUNT_MEMBERS_PRIVMSG_FUNCTIONS_DEFINED", 1);
		}

	if ($_settings['allow_private_messages'] == 1 && !empty($userinfo['id']))
		{
			if (sm_action('viewprivmsg'))
				{
					sm_page_viewid('account-viewprivmsg');
					$m["module"] = 'account';
					if (empty($_getvars['folder']))
						$_getvars['folder'] = 'inbox';
					$m["privmsg_folder"] = $_getvars['folder'];
					$tmp_folder = $_getvars['folder'];
					if (strcmp($tmp_folder, 'outbox') == 0)
						{
							$m["title"] = $lang['module_account']['outbox'];
							$tmp_filter = ' folder_privmsg=1 AND id_sender_privmsg='.intval($userinfo['id']);
						}
					if (empty($tmp_filter))
						{
							$m["title"] = $lang['module_account']['inbox'];
							$tmp_filter = ' folder_privmsg=0 AND id_recipient_privmsg='.intval($userinfo['id']);
							$tmp_folder = 'inbox';
						}
					$sql = "SELECT * FROM ".$tableusersprefix."privmsg, ".$tableusersprefix."users ";
					if (strcmp($tmp_folder, 'outbox') == 0)
						$sql .= " WHERE ".$tableusersprefix."privmsg.id_sender_privmsg=".$tableusersprefix."users.id_user";
					else
						$sql .= " WHERE ".$tableusersprefix."privmsg.id_recipient_privmsg=".$tableusersprefix."users.id_user";
					$sql .= ' AND '.$tmp_filter;
					$sql .= ' ORDER BY time_privmsg DESC ';
					sm_use('admintable');
					$t = new TGrid('edit');
					$t->AddCol('ico', '', '16');
					$t->AddCol('title', $lang['common']['title'], '80%');
					$t->AddCol('time', $lang['module_account']['sended'], '10%');
					$t->AddCol('user', $lang['user'], '10%');
					$t->AddDelete();
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							if ($row->folder_privmsg == 0 && $row->unread_privmsg == 1)
								$t->Image('ico', 'newmessage.gif');
							else
								$t->Image('ico', 'message.gif');
							$t->Label('title', $row->theme_privmsg);
							$t->URL('title', 'index.php?m=account&d=readprivmsg&id='.$row->id_privmsg.'&folder='.$tmp_folder);
							$t->Label('user', $row->login);
							$t->Label('time', strftime($lang["datetimemask"], $row->time_privmsg));
							$t->URL('delete', 'index.php?m=account&d=postdeleteprivmsg&id='.$row->id_privmsg.'&folder='.$tmp_folder);
							$t->NewRow();
							$i++;
						}
					$m['table'] = $t->Output();
				}
			if (sm_action('postsendprivmsg'))
				{
					$m["module"] = 'account';
					$m["title"] = $lang['module_account']['send_message'];
					$m['data']['recipient'] = htmlescape($_postvars['p_recipient']);
					$m['data']['theme'] = htmlescape($_postvars['p_theme']);
					$m['data']['text'] = htmlescape($_postvars['p_text']);
					if (empty($_postvars['p_recipient']))
						{
							$m['mode'] = 'sendprivmsg';
							$m['error_message'] = $lang['module_account']['error_message_recipient'];
						}
					elseif (empty($_postvars['p_theme']) || empty($_postvars['p_text']))
						{
							$m['mode'] = 'sendprivmsg';
							$m['error_message'] = $lang['module_account']['error_message_theme_text'];
						}
					elseif (siman_get_userid_by_name(dbescape($_postvars['p_recipient'])) < 1)
						{
							$m['mode'] = 'sendprivmsg';
							$m['error_message'] = $lang['module_account']['error_message_recipient'];
						}
					else
						{
							$id_sender_privmsg = $userinfo['id'];
							$id_recipient_privmsg = siman_get_userid_by_name(dbescape($_postvars['p_recipient']));
							$folder_privmsg = 0;
							$unread_privmsg = 1;
							$theme_privmsg = dbescape($_postvars['p_theme']);
							$body_privmsg = dbescape($_postvars['p_text']);
							$time_privmsg = time();
							$sql = "INSERT INTO ".$tableusersprefix."privmsg (`id_sender_privmsg`, `id_recipient_privmsg`, `folder_privmsg`, `unread_privmsg`, `theme_privmsg`, `body_privmsg`, `time_privmsg`) VALUES('$id_sender_privmsg', '$id_recipient_privmsg', '$folder_privmsg', '$unread_privmsg', '$theme_privmsg', '$body_privmsg', '$time_privmsg')";
							$result = execsql($sql);
							$folder_privmsg = 1;
							$unread_privmsg = 0;
							$sql = "INSERT INTO ".$tableusersprefix."privmsg (`id_sender_privmsg`, `id_recipient_privmsg`, `folder_privmsg`, `unread_privmsg`, `theme_privmsg`, `body_privmsg`, `time_privmsg`) VALUES('$id_sender_privmsg', '$id_recipient_privmsg', '$folder_privmsg', '$unread_privmsg', '$theme_privmsg', '$body_privmsg', '$time_privmsg')";
							$result = execsql($sql);
							log_write(LOG_USEREVENT, $lang['module_account']['log']['user_send_privmsg']);
							sm_redirect('index.php?m=account&d=viewprivmsg&folder=inbox');
						}
				}
			if (sm_action('sendprivmsg'))
				{
					sm_page_viewid('account-sendprivmsg');
					$m["module"] = 'account';
					$m["title"] = $lang['module_account']['send_message'];
					$m['data']['recipient'] = htmlescape($_postvars['p_recipient']);
					$m['data']['theme'] = htmlescape($_postvars['p_theme']);
					$m['data']['text'] = htmlescape($_postvars['p_body']);
				}
			if (sm_action('readprivmsg'))
				{
					sm_page_viewid('account-readprivmsg');
					$m["module"] = 'account';
					$m["title"] = $lang['module_account']['read_message'];
					if (empty($_getvars['folder']))
						$_getvars['folder'] = 'inbox';
					$tmp_folder = $_getvars['folder'];
					$m["folder_privmsg"] = $_getvars['folder'];
					$sql = "SELECT * FROM ".$tableusersprefix."privmsg, ".$tableusersprefix."users ";
					if (strcmp($tmp_folder, 'outbox') == 0)
						$sql .= " WHERE ".$tableusersprefix."privmsg.id_recipient_privmsg=".$tableusersprefix."users.id_user AND id_sender_privmsg=".intval($userinfo['id']);
					else
						$sql .= " WHERE ".$tableusersprefix."privmsg.id_sender_privmsg=".$tableusersprefix."users.id_user AND id_recipient_privmsg=".intval($userinfo['id']);
					$tmp_filter = ' id_privmsg='.intval($_getvars['id']);
					$sql .= ' AND '.$tmp_filter;
					$result = execsql($sql);
					$i = 0;
					while ($row = database_fetch_object($result))
						{
							$m['message']['id'] = $row->id_privmsg;
							$m['message']['theme'] = $row->theme_privmsg;
							$m['message']['body'] = nl2br($row->body_privmsg);
							$m['message']['time'] = strftime($lang["datetimemask"], $row->time_privmsg);
							$m['message']['user'] = $row->login;
							$m['message']['rebody'] = "-------------------\n&gt; ".str_replace("\n", "\n&gt; ", $row->body_privmsg);
							$i++;
						}
					if ($i == 1)
						{
							$sql = " UPDATE ".$tableusersprefix."privmsg SET unread_privmsg=0 WHERE id_privmsg=".intval($_getvars['id']);
							$result = execsql($sql);
						}
				}
			if (sm_action('postdeleteprivmsg'))
				{
					execsql("DELETE FROM ".$tableusersprefix."privmsg WHERE id_privmsg=".intval($_getvars['id'])." and (folder_privmsg=1 and id_sender_privmsg=".intval($userinfo['id'])." or folder_privmsg=0 and id_recipient_privmsg=".intval($userinfo['id']).")");
					sm_redirect('index.php?m=account&d=viewprivmsg&folder='.$_getvars['folder']);
				}
		}


?>