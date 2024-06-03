<?php

	namespace GS\ORM;

	abstract class EntityObject extends GenericObject
		{
			abstract public static function TableName(): string;

			abstract public static function IdFieldName(): string;

			abstract public static function TitleFieldName(): ?string;

			public function __construct($id_or_cachedinfo)
				{
					parent::__construct(
						$id_or_cachedinfo,
						static::TableName(),
						static::IdFieldName(),
						static::TitleFieldName()
					);
				}

			public static function CreateEntityWithParams(array $params_array)
				{
					return parent::CreateObjectWithParams(static::TableName(), $params_array);
				}

		/** @noinspection PhpMissingReturnTypeInspection */
			public static function initEntityWithParams(array $params_array, array $order_fieldname_boolsort_array=[])
				{
					return parent::initWithParams(static::TableName(), $params_array, $order_fieldname_boolsort_array);
				}
		}
