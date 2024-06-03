<?php

	namespace GS\ORM;

	/* Для вказання типів методів вставити наступні значення в DocBlock нащадків
	 * @method TGenericObject Item($index)
	 * @method TGenericObject|bool Fetch()
	 * @method TGenericObject[] EachItem()
	 */
	abstract class EntityList extends GenericList
		{

			abstract function EntityName(): string;

			function __construct()
				{
					/** @var EntityObject $items_classname */
					$items_classname=$this->EntityName();
					parent::__construct($items_classname, $items_classname::TableName(), $items_classname::IdFieldName(), $items_classname::TitleFieldName());
				}

		}
