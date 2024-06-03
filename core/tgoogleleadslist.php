<?php

	if (!defined("TGoogleLeadsList_DEFINED"))
		{
			Class TGoogleLeadsList extends TGenericList
				{
					/** @var TGoogleLeads[] $items */
					public $items;
					protected $tablename='google_leads';
					protected $idfield='id';

					function SetFilterSearch($search_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_search='.Cleaner::IntObjectID($search_id);
						}

					function SetFilterCheckEmail()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' email_checked = 0';
						}

					function SetFilterHasWebsite()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' website <> ""';
						}

					function SetFilterSearchesIDs($array)
						{
							use_api('cleaner');
							if (!empty($this->sql))
								$this->sql.=' AND ';
							if (!is_array($array) || count($array)==0)
								$this->sql.=" 1=2";
							else
								$this->sql.=" id_search IN (".implode(', ', Cleaner::ArrayIntval($array)).")";
						}

					function SetFilterCustomerIDs($array)
						{
							use_api('cleaner');
							if (!empty($this->sql))
								$this->sql.=' AND ';
							if (!is_array($array) || count($array)==0)
								$this->sql.=" 1=2";
							else
								$this->sql.=" id_customer IN (".implode(', ', Cleaner::ArrayIntval($array)).")";
						}

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterValidPhones()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND phone_number_type<>'notverified'";
						}

					function SetFilterValidForSMSPhones()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND (phone_number_type = 'mobile' OR phone_number_type = 'voip')";
						}

					function SetFilterNeedPhoneValidation()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND phone_number_type='notverified'";
						}

					function SetFilterName($name)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " title LIKE '%".dbescape($name)."%'";
						}

					function SetFilterPhone($cellphone)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone LIKE '%".Cleaner::Phone($cellphone)."%'";
						}

					function SetFilterEmail($email)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " email LIKE '%".dbescape($email)."%'";
						}

					function SetFilterAddress($address)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " address LIKE '%".dbescape($address)."%'";
						}

					protected function InitItem($index)
						{
							$item = new TGoogleLeads($this->itemsinfo[$index]);
							return $item;
						}

				}

			define("TGoogleLeadsList_DEFINED", 1);
		}
