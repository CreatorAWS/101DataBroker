<?php

	use GS\Company\Countries;
	use GS\ORM\EntityObject;

	class TPhone extends EntityObject
		{
			const WORK_PHONE = 'Work';
			const MOBILE_PHONE = 'Mobile';
			const HOME_PHONE = 'Home';
			const MAIN_PHONE = 'Main';
			const BUSINESS_PHONE = 'Business';
			const OTHER_PHONE = 'Other';
			const PHONE_TYPES = [TPhone::BUSINESS_PHONE, TPhone::HOME_PHONE, TPhone::MOBILE_PHONE, TPhone::WORK_PHONE, TPhone::OTHER_PHONE];

			protected $table_name;

			public static function TableName(): string
				{
					return 'phones';
				}

			public static function IdFieldName(): string
				{
					return 'id';
				}

			public static function phoneFullExists($phone, $id_company): bool
				{
					$phone = Cleaner::Phone($phone);

					$info = TQuery::ForTable('phones')->Add('company_id', Cleaner::IntObjectID($id_company))->Add('phone', dbescape($phone))->OrderBy('id')->Get();
					$phone = new TPhone($info);
					if ($phone->Exists())
						return true;
					else
						return false;
				}

			public static function TitleFieldName(): ?string
				{
					return '';
				}

			public static function Create( string $phone, $customer_or_id, $company_or_id, string $phone_type = self::MAIN_PHONE)
				{
					$q = new TQuery('phones');
					$q->Add('phone', dbescape(Cleaner::Phone($phone)));
					$q->Add('customer_id', Cleaner::IntObjectID($customer_or_id));
					$q->Add('company_id', Cleaner::IntObjectID($company_or_id));
					if ($phone_type=='')
						$phone_type=self::OTHER_PHONE;
					$q->Add('phone_type', dbescape($phone_type));
					$object = new TPhone($q->Insert());
					return $object;
				}

			function Phone(): string
				{
					return $this->FieldStringValue('phone');
				}

			function SetPhone(string $val): self
				{
					$this->UpdateValues(['phone' => dbescape($val)]);
					return $this;
				}

			function FullPhoneFormatted(): string
				{
					return Formatter::USPhone($this->Phone());
				}

			function HasCustomerID(): bool
				{
					return !empty($this->CustomerID());
				}

			function CustomerID(): int
				{
					return $this->FieldIntValue('customer_id');
				}

			function SetCustomerID($customer_or_id): self
				{
					$this->UpdateValues(['customer_id' => Cleaner::IntObjectID($customer_or_id)]);
					return $this;
				}

			function CompanyID(): int
				{
					return $this->FieldIntValue('company_id');
				}

			function SetCompanyID($company_or_id): self
				{
					$this->UpdateValues(['company_id' => Cleaner::IntObjectID($company_or_id)]);
					return $this;
				}

			function PhoneType(): string
				{
					return $this->FieldStringValue('phone_type');
				}

			function SetPhoneType(string $val): self
				{
					$this->UpdateValues(['phone_type' => dbescape($val)]);
					return $this;
				}

			function UpdateValues($params): void
				{
					if (!is_array($params))
						return;
					unset($params['id']);
					if (empty($params))
						return;
					$q = new TQuery('phones');
					foreach ($params as $key => $val) {
						$this->info[$key] = $val;
						$q->Add($key, dbescape($this->info[$key]));
					}
					$q->Update('id', $this->ID());
				}

			function Remove(): void
				{
					TQuery::ForTable('phones')->AddWhere('id', intval($this->ID()))->Remove();
				}

		}
