<?php

	namespace GS\ORM;

	/* Для вказання типів методів вставити наступні значення в DocBlock нащадків
	 * @method SimpleObject Item($index)
	 * @method SimpleObject[] EachItem()
	 * @method SimpleObject|NULL FirstItem()
	 * @method SimpleObject|NULL LastItem()
	 */

	use Cleaner;
	use GS\Common\Legacy\FirstLastItemTrait;

	class SimpleList
		{
			protected $items=[];

			use FirstLastItemTrait;

			function AddObjectToList($object)
				{
					$this->items[]=$object;
					return $this;
				}
			function RemoveObjectWithIndex($index)
				{
					if ($index<$this->Count())
						{
							array_splice($this->items, $index, 1);
						}
					return $this;
				}
			function Item($index)
				{
					if (array_key_exists($index, $this->items))
						return $this->items[$index];
					else
						return false;
				}
			function Count()
				{
					return count($this->items ?? []);
				}
			function EachItem()
				{
					for ($i=0; $i<$this->Count(); $i++)
						{
							yield $this->Item($i);
						}
				}
			function ExtractListWithCallback($callback_function)
				{
					$list=new static();
					foreach ($this->EachItem() as $item)
						{
							if ($callback_function($item))
								$list->AddObjectToList($item);
						}
					return $list;
				}
			function RotateElements($index1, $index2)
				{
					$p=$this->Item($index1);
					$this->items[$index1]=$this->Item($index2);
					$this->items[$index2]=$p;
				}

			function ExtractIDsArray(): array
				{
					$r=[];
					foreach ($this->EachItem() as $item)
						$r[]=$item->ID();
					return $r;
				}

			function ExtractTitlesArray(): array
				{
					$r=[];
					foreach ($this->EachItem() as $item)
						$r[]=$item->Title();
					return $r;
				}

			function GetItemWithID($id)
				{
					for ($i = 0; $i < $this->Count(); $i++)
						if ($this->Item($i)->ID()==$id)
							return $this->Item($i);
					return false;
				}

			public function GetIndexForItemWithID($object_or_id): ?int
				{
					$id=Cleaner::IntOrObjectID($object_or_id);
					for ($i = 0; $i < $this->Count(); $i++)
						if ($this->Item($i)->ID()==$id)
							return $i;
					return NULL;
				}

			function SortBy(callable $cmp_function)
				{
					usort($this->items, $cmp_function);
				}
		}
