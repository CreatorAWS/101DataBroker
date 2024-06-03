<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!sm_has_settings('cutomers_data_structure'))
		sm_add_settings('cutomers_data_structure', '');

	if (intval(sm_settings('cutomers_data_structure'))<2023032601)
		{
			execsql("ALTER TABLE `organizations_searches` ADD `is_imported` INT(11) NOT NULL DEFAULT 0;");
			sm_update_settings('cutomers_data_structure', '2023032601');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023032602)
		{
			execsql("ALTER TABLE `organizations_searches` ADD `id_list` INT(11) NOT NULL DEFAULT 0;");
			sm_update_settings('cutomers_data_structure', '2023032602');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033001)
		{
			execsql("ALTER TABLE `customers` ADD `website` VARCHAR(255) NULL DEFAULT NULL;");
			sm_update_settings('cutomers_data_structure', '2023033001');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033002)
		{
			execsql("ALTER TABLE `campaigns_sequences` ADD `letter_template` VARCHAR(255) NULL DEFAULT NULL;");
			sm_update_settings('cutomers_data_structure', '2023033002');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033003)
		{
			execsql("ALTER TABLE `letter_templates` ADD `id_ctg` INT(11) NOT NULL DEFAULT 1;");
			sm_update_settings('cutomers_data_structure', '2023033003');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033101)
		{
			execsql("ALTER TABLE `google_leads` ADD `phone_number_type` VARCHAR (255) NOT NULL DEFAULT 'notverified';");

			sm_update_settings('cutomers_data_structure', '2023033101');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033102)
		{
			execsql("ALTER TABLE `google_leads` ADD `phone_number_type` VARCHAR (255) NOT NULL DEFAULT 'notverified';");

			sm_update_settings('cutomers_data_structure', '2023033102');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023033103)
		{
			execsql("ALTER TABLE `customers` ADD `country` VARCHAR (255) NOT NULL DEFAULT 'USA';");

			sm_update_settings('cutomers_data_structure', '2023033103');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023040601)
		{
			execsql("ALTER TABLE `companies` ADD `proxycurl_api_key` VARCHAR (255) NULL DEFAULT NULL;");

			sm_update_settings('cutomers_data_structure', '2023040601');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023040602)
		{
			execsql("ALTER TABLE `organizations_search_leads` ADD `proxycurl_checked` TINYINT (2) NOT NULL DEFAULT 0;");
			execsql("ALTER TABLE `google_leads` ADD `proxycurl_checked` TINYINT (2) NOT NULL DEFAULT 0;");

			sm_update_settings('cutomers_data_structure', '2023040602');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023041201)
		{
			execsql("ALTER TABLE `messagelog` ADD `id_employee` INT (10) NOT NULL DEFAULT 0;");
			execsql("ALTER TABLE `messagelog` ADD `id_reply` INT (10) NOT NULL DEFAULT 0;");
			execsql("ALTER TABLE `smslog` ADD `id_campaign_schedule` VARCHAR (255) NULL DEFAULT NULL;");

			sm_update_settings('cutomers_data_structure', '2023041201');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023041501)
		{
			execsql("ALTER TABLE `companies` CHANGE `customer_label` `customer_label` VARCHAR(255) NOT NULL  DEFAULT 'Leads';");
			execsql("ALTER TABLE `companies` CHANGE `customers_label` `customers_label` VARCHAR(255) NOT NULL  DEFAULT 'Leads';");

			sm_update_settings('cutomers_data_structure', '2023041501');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023060601)
		{
			execsql("ALTER TABLE `google_reviews` CHARACTER SET = utf8mb4;");
			execsql("ALTER TABLE `google_reviews` COLLATE = utf8mb4_unicode_ci;");
			sm_update_settings('cutomers_data_structure', '2023060601');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023061601)
		{
			execsql("CREATE TABLE `plans` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_company` int(11) unsigned NOT NULL DEFAULT '0',
						  `stripe_id` varchar(255) NOT NULL DEFAULT '',
						  `stripe_product_id` varchar(255) NOT NULL DEFAULT '',
						  `title` varchar(255) NOT NULL DEFAULT '',
						  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `setup_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `setup_fee_title` varchar(255) NOT NULL DEFAULT '',
						  `setup_fee_trial_start` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `interval` varchar(255) NOT NULL DEFAULT 'month',
						  `interval_count` int(11) unsigned NOT NULL DEFAULT '1',
						  `text` longtext,
						  `sidebartext` longtext,
						  `redirect_after_checkout` varchar(255) DEFAULT NULL,
						  `css` longtext,
						  `thank_you_text` longtext,
						  `trial_period_days` int(11) unsigned NOT NULL DEFAULT '0',
						  `qty_min_available` int(11) unsigned NOT NULL DEFAULT '1',
						  `qty_max_available` int(11) unsigned NOT NULL DEFAULT '1',
						  `qty_step` int(11) unsigned NOT NULL DEFAULT '1',
						  `qty_title` varchar(255) NOT NULL DEFAULT '',
						  `webhook_url` text,
						  `ask_for_passwd` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `deleted` int(11) unsigned NOT NULL DEFAULT '0',
						  `type` varchar(255) NOT NULL DEFAULT 'plan',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			execsql("CREATE TABLE `products_metadata` (
							  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							  `object_id` int(11) unsigned NOT NULL,
							  `key_name` varchar(100) NOT NULL DEFAULT '',
							  `val` text NOT NULL,
							  PRIMARY KEY (`id`),
							  KEY `object_name` (`object_id`,`key_name`,`val`(50))
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			execsql("ALTER TABLE `companies` ADD `stripe_secret_key` varchar(255) DEFAULT NULL;");
			execsql("ALTER TABLE `companies` ADD `stripe_public_key` varchar(255) DEFAULT NULL;");
			execsql("ALTER TABLE `companies` ADD `stripe_endpoint_secret` varchar(255) DEFAULT NULL;");
			execsql("ALTER TABLE `companies` ADD `test_mode_add_to_purchases` tinyint(4) NOT NULL DEFAULT '0';");
			execsql("ALTER TABLE `companies` ADD `id_pricing_plan` INT(11) unsigned NOT NULL DEFAULT '0';");

			execsql("CREATE TABLE `subscriptions` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_company` int(11) unsigned NOT NULL DEFAULT '0',
						  `id_plan` int(11) unsigned NOT NULL DEFAULT '0',
						  `plan_quantity` int(11) unsigned NOT NULL DEFAULT '1',
						  `first_name` varchar(255) NOT NULL DEFAULT '',
						  `last_name` varchar(255) NOT NULL DEFAULT '',
						  `address1` text,
						  `address2` text,
						  `city` varchar(255) NOT NULL DEFAULT '',
						  `state` varchar(255) NOT NULL DEFAULT '',
						  `zip` varchar(255) NOT NULL DEFAULT '',
						  `country` varchar(255) NOT NULL DEFAULT '',
						  `email` varchar(255) NOT NULL DEFAULT '',
						  `cellphone` varchar(255) NOT NULL DEFAULT '',
						  `added_time` int(11) unsigned NOT NULL DEFAULT '0',
						  `stripe_id` varchar(255) NOT NULL DEFAULT '',
						  `card_stripe_id` varchar(255) NOT NULL DEFAULT '',
						  `subscription_stripe_id` varchar(255) NOT NULL DEFAULT '',
						  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `coupon_stripe_id` varchar(255) NOT NULL DEFAULT '',
						  `trial_period_days` int(11) unsigned NOT NULL DEFAULT '0',
						  `initial_setup_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `initial_subscription_price` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `billing_cycles_paid` int(11) unsigned NOT NULL DEFAULT '0',
						  `last_successful_payment_time` int(11) unsigned NOT NULL DEFAULT '0',
						  `unsuccessful_payments_count` int(11) unsigned DEFAULT '0',
						  `expiration_time` int(11) unsigned NOT NULL DEFAULT '0',
						  `cancelled` int(11) unsigned NOT NULL DEFAULT '0',
						  `coupon_code` varchar(255) DEFAULT NULL,
						  `id_employee` int(11) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			execsql("CREATE TABLE `coupons` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_u` int(11) unsigned NOT NULL DEFAULT '0',
						  `code` varchar(255) NOT NULL DEFAULT '',
						  `title` varchar(255) DEFAULT NULL,
						  `discount_percent` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `discount_fixed` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `max_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `used_times` int(11) unsigned NOT NULL DEFAULT '0',
						  `assigned_to_product` int(11) unsigned NOT NULL DEFAULT '0',
						  `assigned_to_plan` int(11) unsigned NOT NULL DEFAULT '0',
						  `stripe_coupon_id` varchar(255) DEFAULT NULL,
						  `duration` int(11) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			execsql("CREATE TABLE `webhooks_queue` (
							  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							  `added_time` int(11) unsigned NOT NULL DEFAULT '0',
							  `webhook_url` text,
							  `post_request` text,
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			sm_update_settings('cutomers_data_structure', '2023061601');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023061801)
		{
			execsql("ALTER TABLE `google_searches` ADD `addedtime` INT(11) unsigned NOT NULL DEFAULT '0';");
			sm_update_settings('cutomers_data_structure', '2023061801');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023061901)
		{
			execsql("CREATE TABLE `builtwith_tech_grouos` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_group` int(11) unsigned NOT NULL DEFAULT '0',
						  `title` varchar(255) DEFAULT NULL,
						  `url` varchar(255) DEFAULT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

			execsql("CREATE TABLE `builtwith_tech` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `title` varchar(255) DEFAULT NULL,
						  `id_category` int(11) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB AUTO_INCREMENT=6327 DEFAULT CHARSET=utf8mb4;");
			sm_update_settings('cutomers_data_structure', '2023061901');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023082601)
		{
			execsql("ALTER TABLE `companies` ADD `sic_code_search` INT(11) unsigned NOT NULL DEFAULT '1';");
			sm_update_settings('cutomers_data_structure', '2023082601');
		}


	if (intval(sm_settings('cutomers_data_structure'))<2023091601)
		{
			execsql("ALTER TABLE `companies` ADD `google_search` INT(11) unsigned NOT NULL DEFAULT '1';");
			execsql("ALTER TABLE `companies` ADD `builtwith_search` INT(11) unsigned NOT NULL DEFAULT '1';");
			sm_update_settings('cutomers_data_structure', '2023091601');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023091603)
		{
			execsql("CREATE TABLE `products` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_u` int(11) unsigned NOT NULL DEFAULT '0',
						  `title` varchar(255) DEFAULT NULL,
						  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `text` longtext,
						  `sidebartext` longtext,
						  `redirect_after_checkout` varchar(255) DEFAULT NULL,
						  `purchases_count_total` int(11) unsigned NOT NULL DEFAULT '0',
						  `css` longtext,
						  `thank_you_text` longtext,
						  `multiple_qty_allowed` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `shippable` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `downloadable` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `download_path` text,
						  `download_original_filename` varchar(255) DEFAULT '',
						  `webhook_url` text,
						  `ask_for_passwd` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `id_company` int(11) unsigned NOT NULL DEFAULT '0',
						  `order` int(11) unsigned NOT NULL DEFAULT '0',
						  `type` varchar(255) NOT NULL DEFAULT 'product',
						  `sic_search_enabled` int(11) unsigned NOT NULL DEFAULT '0',
						  `google_search_enabled` int(11) unsigned NOT NULL DEFAULT '0',
						  `builtwith_search_enabled` int(11) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			sm_update_settings('cutomers_data_structure', '2023091603');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023091605)
		{
			execsql("ALTER TABLE `companies` ADD `state_search` INT(11) unsigned NOT NULL DEFAULT '1';");
			execsql("ALTER TABLE `products` ADD `state_search_enabled` INT(11) unsigned NOT NULL DEFAULT '0';");
			execsql("ALTER TABLE `products` ADD `interval` VARCHAR(255) NULL DEFAULT NULL;");
			sm_update_settings('cutomers_data_structure', '2023091605');
		}

	if (intval(sm_settings('cutomers_data_structure'))<2023091606)
		{
			execsql("CREATE TABLE `purchases` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `id_u` int(11) unsigned NOT NULL DEFAULT '0',
						  `id_p` int(11) unsigned NOT NULL DEFAULT '0',
						  `title` varchar(255) DEFAULT NULL,
						  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `qty` int(15) unsigned NOT NULL DEFAULT '1',
						  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `shipping` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `shipping_id` int(11) unsigned NOT NULL DEFAULT '0',
						  `shipping_name` varchar(255) NOT NULL DEFAULT '',
						  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
						  `coupon_code` varchar(255) DEFAULT NULL,
						  `timebought` int(11) unsigned NOT NULL DEFAULT '0',
						  `customeremail` varchar(255) NOT NULL DEFAULT '',
						  `stripetoken` varchar(255) NOT NULL DEFAULT '',
						  `name` varchar(255) NOT NULL DEFAULT '',
						  `email` varchar(255) NOT NULL DEFAULT '',
						  `phone` varchar(255) NOT NULL DEFAULT '',
						  `address_line1` varchar(255) NOT NULL DEFAULT '',
						  `address_line2` varchar(255) NOT NULL DEFAULT '',
						  `city` varchar(255) NOT NULL DEFAULT '',
						  `state` varchar(255) NOT NULL DEFAULT '',
						  `zip` varchar(255) NOT NULL DEFAULT '',
						  `downloadable` tinyint(4) unsigned NOT NULL DEFAULT '0',
						  `downloaded_times` int(11) unsigned NOT NULL DEFAULT '0',
						  `max_downloads` int(11) unsigned NOT NULL DEFAULT '3',
						  `download_link_sent` int(11) unsigned NOT NULL DEFAULT '0',
						  `download_url_hash` varchar(255) NOT NULL DEFAULT '',
						  `id_company` int(11) unsigned NOT NULL DEFAULT '0',
						  `id_employee` int(11) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			sm_update_settings('cutomers_data_structure', '2023091606');
		}

