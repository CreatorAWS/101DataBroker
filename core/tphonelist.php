<?php

use GS\ORM\EntityList;
	/** 
	 * @method TPhone Item($index)
	 * @method TPhone[] EachItem()
	 */
	class TPhoneList extends EntityList
		{
			function EntityName(): string
				{
					return TPhone::class;
				}

			protected function InitItem($index)
				{
					$item = new TPhone($this->itemsinfo[$index]);

					return $item;
				}

			function SetFilterCompany($company_or_id): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " company_id = '" . Cleaner::IntObjectID($company_or_id)."'";

					return $this;
				}

			function SetFilterPhone($phone): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';
					$this->sql .= " phone LIKE '%".Cleaner::Phone($phone)."%'";

					return $this;
				}

			function SetFilterCustomer($customer_or_id): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " customer_id = '" . Cleaner::IntObjectID($customer_or_id)."'";

					return $this;
				}

			public function SetFilterPhoneType(string $phone_type = TPhone::MAIN_PHONE): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " phone_type = '" . dbescape($phone_type)."'";

					return $this;
				}

			public function SetFilterAdditionalPhones(array $phone_types = []): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " phone_type in (" .implode(',', Cleaner::ArrayQuotedAndDBEscaped($phone_types)).")";

					return $this;
				}

			public function SetFilterExcludeMain(): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " phone_type <> '" . dbescape(TPhone::MAIN_PHONE) . "'";

					return $this;
				}

			function ExtractPhonesArray($show_ids = false)
				{
					$r=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							if (!empty($this->items[$i]->Phone()))
								{
									if ($show_ids)
										$r[] = [
											'id' => $this->items[$i]->ID(),
											'phone' => $this->items[$i]->Phone(),
										];
									else
										$r[] = $this->items[$i]->Phone();
								}
						}
					return $r;
				}

			function ExtractCustomerIDsArray($show_ids = false)
				{
					$r=Array();
					for ($i = 0; $i < $this->Count(); $i++)
						{
							if (!empty($this->items[$i]->Phone()))
								{
									if ($show_ids)
										$r[] = [
											'id' => $this->items[$i]->ID(),
											'id_customer' => $this->items[$i]->CustomerID(),
										];
									else
										$r[] = $this->items[$i]->CustomerID();
								}
						}
					return $r;
				}

			public function SetFilterExcludeCustomer($customer_or_id): self
				{
					if (!empty($this->sql))
						$this->sql .= ' AND ';

					$this->sql .= " customer_id <> '" . Cleaner::IntObjectID($customer_or_id) . "'";

					return $this;
				}

		}
