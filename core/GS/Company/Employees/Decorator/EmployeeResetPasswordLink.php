<?php

	namespace GS\Company\Employees\Decorator;
	use GS\Common\UniqueID;
	use TEmployeeList;

	/**
	 * @method static self Init($employee)
	 */

	class EmployeeResetPasswordLink extends EmployeeDecoratorPrototype
		{

			public function GetLinkIDForPassword(string $password): string
				{
					$id=UniqueID::Generate('rstl');
					$this->SetMetaData($id, $password);
					return $id;
				}

			public function ResetPasswordRemoveToken(string $token): void
				{
					if (substr($token, 0, 4)!=='rstl')
						return;
					$password=$this->GetMetaData($token);
					$this->Employee()->SetPassword($password);
					$this->SetMetaData($token, NULL);
				}

			public static function InitWithToken(int $company_id, string $token): ?self
				{
					if (substr($token, 0, 4)!=='rstl')
						return NULL;
					$list=new TEmployeeList($company_id);
					$list->SetFilterHasMetaData($token);
					$list->Limit(2);
					$list->Load();
					if ($list->Count()!==1)
						return NULL;
					return new self($list->Item(0));
				}

		}
