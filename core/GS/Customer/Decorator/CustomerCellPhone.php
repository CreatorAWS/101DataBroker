<?php

namespace GS\Customer\Decorator;

use Cleaner;
use Formatter;
use GS\Company\Countries;
use TCompany;
use TCustomer;
use TPhone;
use TPhoneList;

/** 
 * @method static self Init($customer) 
 * 
*/

class CustomerCellPhone extends CustomerDecoratorPrototype
{
	public function GetAdditionalPhones(): TPhoneList
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany(TCompany::CurrentCompany())
			->SetFilterCustomer($this->Customer())
			->SetFilterExcludeMain()
			->Load();
		return $phones;
	}
	public function GetAdditionalPhonesArray(): array
	{
		$phones=[];
		/**@var TPhone $phone */
		foreach ($this->GetAdditionalPhones()->EachItem() as $phone) {
			$phones[] = ['id'=>$phone->ID(), 'type'=>$phone->PhoneType(), 'phone' => Formatter::PhoneByCountry($phone->FullPhone(), $phone->Country()), 'active' => $phone->isActivePhone()];
		}
		return $phones;
	}
	public function GetAllPhonesArrayWithFlag(): array
	{
		$phones=[];
		/**@var TPhone $phone */
		foreach ($this->GetCustomerCellPhonesList()->EachItem() as $phone) {
			$phones[] = ['type'=>$phone->PhoneType(), 
						'phone_formatted' => $phone->FullPhoneFormatted(),
						'prefix' => $phone->CountryPrefix(),
						'short_phone' => $phone->ShortPhone(),
						'country' => $phone->Country(),
						'extension' => $phone->Extension()];
		}
		return $phones;
	}
	public function GetCustomerCellPhonesList(): TPhoneList
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->Load();
		return $cellphones;
	}
	public function GetCustomerCellPhones(): array
	{
		$phones = [];
		/** @var TPhone $customerphone */
		foreach ($this->GetCustomerCellPhonesList()->EachItem() as $customerphone) {
			$phones[] = [
				'prefix' => $customerphone->CountryPrefix(),
				'phone' => $customerphone->ShortPhone()
			];
		}
		return $phones;
	}
	public function isDublicateWithAnotherCustomer(string $prefix, string $phone): bool
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->SetFilterExcludeCustomer($this->Customer())
			->SetFilterCustomerPhone()
			->Limit(1)
			->Load();
		return $cellphones->Count() > 0;
	}
	public static function isDublicate(string $prefix, string $phone, TCompany $company = null): bool
	{
		if (is_null($company))
			$company = TCompany::CurrentCompany();
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($company)
			->SetFilterCustomerPhone()
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->Limit(1)
			->Load();
		return $cellphones->Count() > 0;
	}

	public function isCustomersPhone(string $prefix, string $phone): bool
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->Limit(1)
			->Load();
		return $cellphones->Count() > 0;
	}
	/**
	 * To add phones to existing customer
	 *
	 * @param string $prefix
	 * @param string $short_phone
	 * @param string $country
	 * @param [type] $phone_type
	 * @return TPhone
	 */
	public function AddCustomerPhone(string $phone_prefix, string $short_phone, string $country, bool $inactive_phone = false, string $phone_type = TPhone::OTHER_PHONE, string $extension=''): void
	{
		$phone = TPhone::Create(
			$phone_prefix,
			Cleaner::Phone($short_phone),
			$country,
			$this->Customer(),
			0,
			$this->Customer()->CompanyID(),
			$inactive_phone,
			$phone_type,
			$extension
		);
		if ($phone->PhoneType() == TPhone::MAIN_PHONE){
			$this->Customer()->SetCellPhone($phone->CountryPrefix().$phone->ShortPhone());
			if ($inactive_phone)
				$this->Customer()->SetInactivePhone();
			else
				$this->Customer()->SetActivePhone();
		}
	}
	public function RemoveCustomerPhones(): void
	{
		$this->Customer()->SetCellPhone('');
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->Load();
		foreach ($phones->EachItem() as $phone) {
			$phone->Remove();
		}
	}
	public function HasAdditionalPhones(): bool
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->SetFilterExcludeMain()
			->Limit(1)
			->Load();
		return $phones->Count() > 0;
	}
	function MainCellphone(): TPhone
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->SetFilterPhoneType()
			->Limit(1)
			->Load();
		if ($phones->Count() > 0)
			return $phones->FirstItem();
		else
			return TPhone::initNotExistent();
	}
	function HasMainCellphone(): bool
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Customer()->CompanyID())
			->SetFilterCustomer($this->Customer())
			->SetFilterPhoneType()
			->Limit(1)
			->Load();
		return $phones->Count() > 0;
	}
	
	public static function initCustomerByPhone(string $phone, int $company_id, string $country): TCustomer
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($company_id)
			->SetFilterShortPhone($phone)
			->SetFilterCountry($country)
			->Limit(1)
			->Load();
		$customer_id = $phones->Item(0) ? $phones->Item(0)->CustomerID() : false;
		return new TCustomer($customer_id);
	}
	public static function GetCustomersIdsByFullPhone(string $phone): array
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany(TCompany::CurrentCompany())
			->SetFilterCustomerPhone()
			->SetFilterFullPhoneLike($phone)
			->Load();
		$customer_ids=[];
		foreach ($phones->EachItem() as $phone) {
			$customer_ids[]=$phone->CustomerID();
		}
		return $customer_ids;
	}
}
