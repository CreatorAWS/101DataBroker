<?php

	namespace GS\Storage\Universal;

	use GS\ORM\EntityList;

	/**
	 * @method StoredFile Item($index)
	 * @method StoredFile|bool Fetch()
	 * @method StoredFile[] EachItem()
	 */
	Class StoredFileList extends EntityList
		{

			function EntityName(): string
				{
					return StoredFile::class;
				}

			public static function ForObject(string $type, int $id): self
				{
					$list=new StoredFileList();
					$list->FilterObjectType($type);
					$list->FilterObjectID($id);
					return $list;
				}

			function FilterObjectType(string $object_type): self
				{
					$this->SetFilterFieldStringValue('object_type', $object_type);
					return $this;
				}

			function FilterObjectID(int $object_id): self
				{
					$this->SetFilterFieldIntValue('object_id', $object_id);
					return $this;
				}

			function OrderByUploadedTime($asc = true)
				{
					$this->OrderByField('uploadedtime', $asc);
				}
		}
