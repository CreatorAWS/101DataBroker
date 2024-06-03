<?php

	namespace GS\Contacts;

	use TCompany;
    use TCompanyStage;
    use TCompanyStageList;

    class ContactStage
		{

			public const RECEIVED = 'received';
			public const CONTACT = 'contact';
			public const APPOINTMENT = 'appointment';
			public const SOLD = 'sold';
			public const LOST = 'lost';

			public static function ListAvailable(): array
				{
					return [
						self::RECEIVED,
						self::CONTACT,
						self::APPOINTMENT,
						self::SOLD,
						self::LOST,
					];
				}

            public static function ListAvailableForStatus($status = '', $company = ''): array
                {
                    $data = [];

                    if (empty($company))
                        $company = TCompany::CurrentCompany();

                    if (empty($status) || in_array($status, ContactStatus::ListAvailable()))
                        {
                            return self::ListAvailable();
                        }
                    else
                        {
                            $list = new TCompanyStageList();
                            $list->SetFilterCompany($company);
                            $list->SetFilterStatusTag($status, $company);
                            $list->Load();

                            foreach ($list->EachItemOfLoaded() as $stage)
                                {
                                    /** @var TCompanyStage $stage */
                                    $data[] = $stage->Value();
                                }

                            return $data;
                        }
                }

            public static function CompanyListAvailableForStatus($status = '', $company = ''): array
                {
                    $data = [];

                    $list = new TCompanyStageList();
                    $list->SetFilterCompany($company);
                    $list->SetFilterStatusTag($status, $company);
                    $list->Load();

                    foreach ($list->EachItemOfLoaded() as $stage)
                        {
                            /** @var TCompanyStage $stage */
                            $data[] = $stage->Stage();
                        }

                    return $data;
                }


        public static function FullValuesListAvailable($company = ''): array
                {
                    /** @var TCompany $company */
                    if ( empty($company) )
                        $company = TCompany::CurrentCompany();

                    foreach (ContactStage::ListAvailable() as $stage)
                        {
                            $data[] = $stage;
                        }

                    $list = new TCompanyStageList();
                    $list->SetFilterCompany($company);
                    $list->Load();

                    foreach ($list->EachItemOfLoaded() as $stage)
                        {
                            /** @var TCompanyStage $stage */
                            $data[] = $stage->Value();
                        }
                    return $data;
                }

            public static function FullListAvailable($status, $company = ''): array
                {
                    /** @var TCompany $company */
                    if ( empty($company) )
                        $company = TCompany::CurrentCompany();

                    foreach (ContactStage::ListAvailable() as $stage)
                        {
                            $data[] = [
                                'title' => $company->PipelineTitleForStatusStage($status, $stage),
                                'value' =>  $stage,
                                'id' =>  '',
                                'editable' =>  false
                            ];
                        }

                    $list = new TCompanyStageList();
                    $list->SetFilterStatus($status);
                    $list->SetFilterCompany($company);
                    $list->Load();

                    foreach ($list->EachItemOfLoaded() as $stage)
                    {
                        /** @var TCompanyStage $stage */
                        $data[] = [
                            'title' => $stage->Stage(),
                            'value' =>  $stage->Value(),
                            'id' =>  $stage->ID(),
                            'editable' =>  true
                        ];
                    }

                    return $data;
                }

		}
