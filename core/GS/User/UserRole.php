<?php

	namespace GS\User;

	class UserRole
		{

			public const ADMIN = 'admin';
			public const MANAGER = 'manager';
			public const USER = 'user';


			public static function ListAvailable(): array
				{
					return [
						self::ADMIN,
						self::MANAGER,
						self::USER,
					];
				}
		}
