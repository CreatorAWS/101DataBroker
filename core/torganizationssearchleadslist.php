<?php

	if (!defined("TOrganizationsSearchLeadsList_DEFINED"))
		{
			Class TOrganizationsSearchLeadsList extends TGenericList
				{
				/** @var TOrganizationsSearchLead[] $items */
					public $items;
					protected $tablename='organizations_search_leads';
					protected $idfield='id';
					protected $titlefield='title';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterSearch($search_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_search='.Cleaner::IntObjectID($search_id);
						}


					function SetFilterValidPhones()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND phone_number_type<>'notverified'";
						}

					function SetFilterNeedPhoneValidation()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND phone_number_type='notverified'";
						}

					function SetFilterValidForSMSPhones()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " phone <> '' AND (phone_number_type = 'mobile' OR phone_number_type = 'voip')";
						}

					function SetFilterVisible()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' hidden = 0';
						}

					function SetFilterHidden()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' hidden = 1';
						}

					function SetFilterName($name)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " title LIKE '%".dbescape($name)."%'";
						}

					function SetFilterPhone($cellphone)
						{
							$phones = new TPhoneList();
							$phones->SetFilterCompany(TCompany::CurrentCompany());
							$phones->SetFilterPhone($cellphone);
							$phones->Load();

							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql.=" (id_customer IN (".implode(',', $phones->ExtractCustomerIDsArray())."))";
						}

					function SetFilterEmail($email)
						{
							$emails = new TEmailsList();
							$emails->SetFilterCompany(TCompany::CurrentCompany());
							$emails->SetFilterEmailLike($email);
							$emails->Load();

							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql.=" (id_customer IN (".implode(',', $emails->ExtractCustomerIDsArray())."))";
						}

					function SetFilterAddress($address)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= " address LIKE '%".dbescape($address)."%' OR city LIKE '%".dbescape($address)."%' OR state LIKE '%".dbescape($address)."%' OR zip LIKE '%".dbescape($address)."%'";
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

					protected function InitItem($index)
						{
							$item = new TOrganizationsSearchLead($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TOrganizationsSearchLeadsList_DEFINED", 1);
		}
