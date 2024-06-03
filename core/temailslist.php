<?php

	if (!defined("TEmailsList_DEFINED"))
		{
			Class TEmailsList extends TGenericList
				{
					/** @var TEmails[] $items */
					public $items;
					protected $tablename='emails';
					protected $idfield='id';

					function SetFilterCustomer($customer_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' customer_id='.Cleaner::IntObjectID($customer_or_id);
						}

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' company_id='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterEmail($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' email = '.dbescape($email);
						}

					function SetFilterEmailLike($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' email LIKE "%'.dbescape($email).'%"';
						}

					function ExtractEmailsArray($exclude_email = '', $show_ids = false)
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									if (empty($exclude_email) || (!empty($exclude_email) && $exclude_email != $this->items[$i]->Email()))
										{
											if ($show_ids)
												$r[] = [
													'id' => $this->items[$i]->ID(),
													'email' => $this->items[$i]->Email(),
												];
											else
												$r[] = $this->items[$i]->Email();
										}

								}
							return $r;
						}

					function ExtractCustomerIDsArray($show_ids = false)
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									if (!empty($this->items[$i]->Email()))
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

					protected function InitItem($index)
						{
							$item = new TEmails($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TEmailsList_DEFINED", 1);
		}
