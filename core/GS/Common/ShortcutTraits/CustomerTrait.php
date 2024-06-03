<?php

	namespace GS\Common\ShortcutTraits;

	use TCustomer;

	trait CustomerTrait
		{
			private $customer_cached_object=NULL;

			abstract public function CustomerID(): int;

			public function HasCustomer(): bool
				{
					return $this->CustomerID()>0;
				}

			public function Customer(): TCustomer
				{
					if ($this->customer_cached_object===NULL)
						{
							$this->customer_cached_object=new TCustomer($this->CustomerID());
						}
					return $this->customer_cached_object;
				}

		}
