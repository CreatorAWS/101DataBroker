<?php
	
	function sendnextvoiceinqueue()
		{
			$info = getsql("SELECT * FROM voicequeue WHERE sendafter<=".time()." ORDER BY id LIMIT 1");
			if (!empty($info['id']))
				{
					//print($info['id'].' - ');
					execsql("DELETE FROM voicequeue WHERE id=".intval($info['id'])." LIMIT 1");
					if (strcmp(substr($info['to'], 0, 4), '1111')!=0 && strcmp(substr($info['to'], 0, 5), '+1111')!=0)
						{
							send_voice($info['to'], $info['from'], $info['id_asset']);
						}
					return true;
				}
			else
				return false;
		}

	$timeend = time() + 50;
	while (time() < $timeend)
		{
			if (!sendnextvoiceinqueue())
				sleep(1);
		}
