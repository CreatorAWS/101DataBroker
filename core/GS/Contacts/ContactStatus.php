<?php

	namespace GS\Contacts;

	use TCompany;
    use TCompanyStatus;
    use TCompanyStatusList;

    class ContactStatus
		{

			public const CUSTOMER = 'customers';
			public const OPPORTUNITY = 'opportunities';
			public const LEAD = 'leads';

			public static function ListAvailable(): array
				{
					return [
						self::CUSTOMER,
						self::OPPORTUNITY,
						self::LEAD,
					];
				}

            public static function FullValuesListAvailable($company = ''): array
				{
                    /** @var TCompany $company */
                    if ( empty($company) )
                        $company = TCompany::CurrentCompany();

                    foreach (ContactStatus::ListAvailable() as $status)
                        {
                            $data[] = $status;
                        }

                    $list = new TCompanyStatusList();
                    $list->SetFilterCompany($company);
                    $list->Load();

                    foreach ($list->EachItemOfLoaded() as $status)
                        {
                            /** @var TCompanyStatus $status */
                            $data[] = $status->Value();
                        }
                    return $data;
				}

            public static function FullListAvailable($company = ''): array
                {
                    /** @var TCompany $company */
                    if ( empty($company) )
                        $company = TCompany::CurrentCompany();

                    foreach (ContactStatus::ListAvailable() as $status)
                        {
                            $data[] = [
                                'title' => $company->PipelineTitleForStatus($status),
                                'value' =>  $status,
                                'id' =>  '',
                                'editable' =>  false
                            ];
                        }

                    $list = new TCompanyStatusList();
                    $list->SetFilterCompany($company);
                    $list->Load();

                    foreach ($list->EachItemOfLoaded() as $status)
                        {
                            /** @var TCompanyStatus $status */
                            $data[] = [
                                'title' => $status->Status(),
                                'value' =>  $status->Value(),
                                'id' =>  $status->ID(),
                                'editable' =>  true
                            ];
                        }
                    return $data;
                }
		}
