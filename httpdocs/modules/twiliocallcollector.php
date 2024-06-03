<?php

	$customer = new TCustomer(intval($_getvars['id']));
	if ($customer->Exists())
		{
			sm_log('customer', $customer->ID(), print_r($_postvars, true));
			$customercall=new TCustomerCall(intval($_getvars['call']));
			if ($customercall->Exists())
				{
					$customercall->SetDurationSec($_postvars['CallDuration']);
					$customercall->SetRecordingUrl($_postvars['RecordingUrl']);
					$customercall->SetStatus($_postvars['CallStatus']);
					if ($_getvars['incoming'] == 1)
						{
							if ($_postvars['CallStatus'] == 'no-answer' || $_postvars['CallStatus'] == 'failed' || $_postvars['CallStatus'] == 'unknown')
								$customer->SetMissedCall(1);
						}
				}
		}
	exit;