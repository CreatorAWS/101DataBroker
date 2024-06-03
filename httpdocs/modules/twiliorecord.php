<?php

	if (intval(sm_settings('twiliorecord_version'))==0)
		{
			execsql("CREATE TABLE `".$sm['t']."twiliorecord` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `from` varchar(255) DEFAULT NULL,
			  `to` varchar(255) DEFAULT NULL,
			  `text` text,
			  `recording` text,
			  `recording_duration` int(11) unsigned NOT NULL DEFAULT '0',
			  `request` text,
			  `timeadded` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM;");
			sm_add_settings('twiliorecord_version', '2016061701');
		}
	if (intval(sm_settings('twiliorecord_version'))<2016061702)
		{
			sm_fs_update('Twilio List', 'index.php?m=twiliorecord&d=list', 'twiliolist/');
			sm_update_settings('twiliorecord_version', '2016061702');
		}

	if (sm_action('receivecall'))
		{
			print('<?'.'xml version="1.0" encoding="UTF-8"?>
				<Response>
					<Say>
						To receive more information about the app, leave your name, email address and business name after the beep. 
					</Say>
					<Record 
						action="'.sm_homepage().'index.php?m=twiliorecord&amp;d=record"
						maxLength="90"
						finishOnKey="*"
						/>
					<Say>I did not receive a recording</Say>
				</Response>
				');
			exit();
		}
	if (sm_action('record'))
		{
			$q=new TQuery($sm['t'].'twiliorecord');
			$q->Add('from', dbescape($_postvars['From']));
			$q->Add('to', dbescape($_postvars['To']));
			$q->Add('text', dbescape($_postvars['Body']));
			$q->Add('recording', dbescape($_postvars['RecordingUrl']));
			$q->Add('recording_duration', dbescape($_postvars['RecordingDuration']));
			$q->Add('request', dbescape(print_r($_REQUEST, true)));
			$q->Add('timeadded', time());
			$id=$q->Insert();
			print("<?xml version='1.0' encoding='utf-8' ?>\n<Response></Response>");
			$smstext='New voicemail '.sm_homepage().'index.php?m=twiliorecord&d=l&i='.$id.' | All: '.sm_homepage().'twiliolist/';
			$notification_phone='+17026591465';
			$from_phone='+17024252155';
			use_api('sms');
			queue_sms($notification_phone, $smstext, $from_phone);
			exit;
		}
	if (sm_action('l'))
		{
			$info=TQuery::ForTable($sm['t'].'twiliorecord')->AddWhere('id', intval($_getvars['i']))->Get();
			sm_redirect_now($info['recording']);
		}
	if ($userinfo['level']>0)
		{
			if (sm_action('list'))
				{
					sm_use('ui.interface');
					sm_use('ui.form');
					sm_use('ui.grid');
					include_once('includes/adminbuttons.php');
					add_path_modules();
					add_path('Recordings', 'index.php?m='.sm_current_module().'&d=list');
					sm_title('Recordings');
					$offset = abs(intval($_getvars['from']));
					$limit = 30;
					$ui = new TInterface();
					$b = new TButtons();
					$ui->AddButtons($b);
					$t = new TGrid();
					$t->AddCol('id', 'Id');
					$t->AddCol('from', 'From');
					$t->AddCol('text', 'Text');
					$t->AddCol('recording', 'Recording');
					$t->AddCol('recording_duration', 'Duration');
					$t->AddCol('timeadded', 'Time');
					$t->AddCol('info', 'Info');
					//$t->AddDelete();
					$q = new TQuery($sm['t'].'twiliorecord');
					$q->OrderBy('id DESC');
					$q->Limit($limit);
					$q->Offset($offset);
					$q->Select();
					for ($i = 0; $i < count($q->items); $i++)
						{
							$t->Label('id', $q->items[$i]['id']);
							$t->Label('from', $q->items[$i]['from']);
							$t->Label('to', $q->items[$i]['to']);
							$t->Label('text', $q->items[$i]['text']);
							$t->Label('recording', $q->items[$i]['recording']);
							$t->URL('recording', $q->items[$i]['recording']);
							$t->Label('recording_duration', $q->items[$i]['recording_duration']);
							$t->Label('timeadded', strftime($lang['datetimemask'], $q->items[$i]['timeadded']));
							$t->Label('info', 'Info');
							$t->Expand('info');
							$t->ExpanderHTML(nl2br($q->items[$i]['request']));
							//$t->URL('delete', 'index.php?m='.sm_current_module().'&d=postdelete&id='.$q->items[$i]['id'].'&returnto='.urlencode(sm_this_url()));
							$t->NewRow();
						}
					$ui->AddGrid($t);
					$ui->AddPagebarParams($q->Find(), $limit, $offset);
					$ui->AddButtons($b);
					$ui->Output(true);
				}
		}
	if ($userinfo['level']==0 && sm_action('list'))
		sm_redirect(sm_homepage().'index.php?m=account');

?>