<?php

	namespace GS\Campaigns;

	class CampaignItemStatus
		{

			public const NONE = 'none';
			public const SENT = 'sent';
			public const REGISTERED = 'registered';
			public const PENDING_1 = 'pending1';
			public const PENDING_2 = 'pending2';
			public const SCHEDULED = 'scheduled';
			public const FINISHED = 'finished';
			public const VOICE_MESSAGE_SENT = 'Voice message sent';
			public const UNSUBSCRIBED = 'Unsubscribed';

			public static function StatusTitle(string $status_tag): string
				{
					//TODO: add correct titles
					return $status_tag;
				}

		}
