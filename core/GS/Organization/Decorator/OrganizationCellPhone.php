<?php

namespace GS\Organization\Decorator;

use TCompany;
use TOrganization;
use TPhone;
use TPhoneList;
use Cleaner;
use Formatter;

/** @method static self Init($organization) */

class OrganizationCellPhone extends OrganizationDecoratorPrototype
{

	public function GetOrganizationCellPhonesList(): TPhoneList
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->Load();
		return $cellphones;
	}
	public function GetAdditionalPhonesArray(): array
	{
		$phones=[];
		foreach ($this->GetAdditionalPhones()->EachItem() as $phone) {
			$phones[] = ['id'=>$phone->ID(), 'type'=>$phone->PhoneType(), 'phone' => Formatter::PhoneByCountry($phone->FullPhone(), $phone->Country()), 'active'=>$phone->isActivePhone()];
		}
		return $phones;
	}
	public function GetOrganizationCellPhones(): array
	{
		$phones = [];
		/** @var TPhone $organizationphone */
		foreach ($this->GetOrganizationCellPhonesList()->EachItem() as $organizationphone) {
			$phones[] = [
				'prefix' => $organizationphone->CountryPrefix(),
				'phone' => $organizationphone->ShortPhone()
			];
		}
		return $phones;
	}
	public function isDublicateWithAnotherOrganization(string $prefix, string $phone): bool
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->SetFilterExcludeOrganization($this->Organization())
			->SetFilterCompanyPhone()
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
			->SetFilterCompanyPhone()
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->Limit(1)
			->Load();
		return $cellphones->Count() > 0;
	}
	public function isOrganizationsPhone(string $prefix, string $phone): bool
	{
		$cellphones = new TPhoneList();
		$cellphones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->SetFilterPhoneWithPrefix($prefix, $phone)
			->SetFilterExcludeOrganization($this->Organization())
			->Limit(1)
			->Load();
		return $cellphones->Count() > 0;
	}
	/**
	 * To add phones to existing organization
	 *
	 * @param string $prefix
	 * @param string $short_phone
	 * @param string $country
	 * @param [type] $phone_type
	 * @return TPhone
	 */
	public function AddOrganizationPhone(string $phone_prefix, string $short_phone, string $country, bool $inactive_phone, string $phone_type = TPhone::OTHER_PHONE, string $extension=''): void
	{
		$phone = TPhone::Create(
			$phone_prefix,
			Cleaner::Phone($short_phone),
			$country,
			0,
			$this->Organization(),
			$this->Organization()->CompanyID(),
			$inactive_phone,
			$phone_type,
			$extension
		);
		if ($phone->PhoneType() == TPhone::MAIN_PHONE){
			$this->Organization()->SetCellPhone($phone->CountryPrefix().$phone->ShortPhone());
		}
	}
	public function RemoveOrganizationPhones(): void
	{
		$this->Organization()->SetCellphone('');
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->Load();
		foreach ($phones->EachItem() as $phone) {
			$phone->Remove();
		}
	}
	public function HasAdditionalPhones(): bool
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->SetFilterExcludeMain()
			->Limit(1)
			->Load();
		return $phones->Count() > 0;
	}
	function MainCellphone(): TPhone
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
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
		$phones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->SetFilterPhoneType()
			->Limit(1)
			->Load();
		return $phones->Count() > 0;
	}
	public function GetAdditionalPhones(): TPhoneList
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($this->Organization()->CompanyID())
			->SetFilterOrganization($this->Organization())
			->SetFilterExcludeMain()
			->Load();
		return $phones;
	}
	public static function initOrganizationByPhone(string $phone, int $company_id, string $country): TOrganization
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany($company_id)
			->SetFilterShortPhone($phone)
			->SetFilterCountry($country)
			->Limit(1)
			->Load();
		$organization_id = $phones->Item(0) ? $phones->Item(0)->OrganizationID() : false;
		return new TOrganization($organization_id);
	}
	public static function GetOrganizationsIdsByFullPhone(string $phone): array
	{
		$phones = new TPhoneList();
		$phones->SetFilterCompany(TCompany::CurrentCompany())
			->SetFilterCompanyPhone()
			->SetFilterFullPhoneLike($phone)
			->Load();
		$organizations_ids=[];
		foreach ($phones->EachItem() as $phone) {
			$organizations_ids[]=$phone->OrganizationID();
		}
		return $organizations_ids;
	}
	public function GetAllPhonesArrayWithFlag(): array
	{
		$phones=[];
		/**@var TPhone $phone */
		foreach ($this->GetOrganizationCellPhonesList()->EachItem() as $phone) {
			$phones[] = ['type'=>$phone->PhoneType(),
						'phone_formatted' => $phone->FullPhoneFormatted(),
						'prefix' => $phone->CountryPrefix(),
						'short_phone' => $phone->ShortPhone(),
						'country' => $phone->Country(),
						'extension' => $phone->Extension()];
		}
		return $phones;
	}
}
