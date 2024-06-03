<?php

	class Path
		{
			public static function DealersRoot()
				{
					return dirname(dirname(__FILE__)).'/httpdocs/';
				}
			public static function iOSPushNotificationCert()
				{
					return dirname(dirname(__FILE__)).'/secureitems/cert.pem';
				}
		}

?>