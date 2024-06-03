<?php

	namespace GS\Campaigns;

	class CampaignStatus
		{

			public const NOTFINISHED = 'notfinished';
			public const STARTED = 'started';
			public const SCHEDULED = 'scheduled';
			public const STOPPED = 'stopped';
			public const FINISHED = 'finished';


			public static function StatusTitle(string $status_tag): string
				{
					//TODO: add correct titles
					return $status_tag;
				}

		}
