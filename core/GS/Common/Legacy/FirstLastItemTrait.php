<?php

	namespace GS\Common\Legacy;

	trait FirstLastItemTrait
		{

			public function FirstItem()
				{
					if ($this->Count()==0)
						return NULL;
					else
						return $this->Item(0);
				}

			public function LastItem()
				{
					if ($this->Count()==0)
						return NULL;
					else
						return $this->Item($this->LastItemIndex());
				}

			public function LastItemIndex(): int
				{
					return $this->Count()-1;
				}

		}
