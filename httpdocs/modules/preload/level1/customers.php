<?php

	use Stripe\Stripe;

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	use_api('cleaner');
	use_api('validator');
	use_api('formatter');
	use_api('tcompany');

	sm_use('twilio');

	sm_add_cssfile('incomingcalls.css');
	sm_add_cssfile('conversation.css');

	$currentcompany = new TCompany(System::MyAccount()->CompanyID());
	if (!$currentcompany->Exists())
		exit('Error E-345-1566');

	if (System::MyAccount()->isSuperAdmin() && TCompany::CurrentCompany()->ID()==1 && (TCompany::SystemCompany()->HasStripeSecretKey() || TCompany::CurrentCompany()->HasStripeSecretKey()))
		Stripe::setApiKey(TCompany::SystemCompany()->StripeSecretKey());
	elseif (TCompany::CurrentCompany()->HasStripeSecretKey())
		Stripe::setApiKey(TCompany::CurrentCompany()->StripeSecretKey());

	if (TCompany::CurrentCompany()->HasSystemLogoImageURL())
		$sm['s']['current_logo'] = TCompany::CurrentCompany()->SystemLogoImageURL();
	$sm['label']['customer'] = TCompany::CurrentCompany()->LabelForCustomer();
	$sm['label']['customers'] = TCompany::CurrentCompany()->LabelForCustomers();

	//Main Menu Start

	$sm['mainmenu'][]=Array(
		'url'=>'index.php',
		'icon_class'=>'',
		'title'=> '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none"><path d="M14.25 14.7499H3.75C3.55109 14.7499 3.36032 14.6709 3.21967 14.5303C3.07902 14.3896 3 14.1988 3 13.9999V7.24994H0.75L8.49525 0.20894C8.63333 0.0832983 8.81331 0.0136719 9 0.0136719C9.18669 0.0136719 9.36667 0.0832983 9.50475 0.20894L17.25 7.24994H15V13.9999C15 14.1988 14.921 14.3896 14.7803 14.5303C14.6397 14.6709 14.4489 14.7499 14.25 14.7499ZM4.5 13.2499H13.5V5.86769L9 1.77719L4.5 5.86769V13.2499Z" fill="white"/></svg><span>Home</span>',
		'selected'=>sm_is_index_page()
	);

	if ( SearchSectionAvailableForCompanies() && (System::HasGoogleSearchInstalled() || System::HasBuiltWithInstalled() || System::HasSicCodesSearchInstalled() || System::HasStatesSearchInstalled()) )
		{
			$search_selected = false;
			$history_selected = false;

			if (($sm['g']['m']=='searchleads' && $sm['g']['d'] != 'search_history') || ($sm['g']['m']=='searchleads' && $sm['g']['d'] != 'search_history') || $sm['g']['m']=='searchsiccode')
				$search_selected = true;

			if ( $sm['g']['m']== 'customerdetails' &&  !empty($_getvars['search']) )
				$search_selected = true;

			if ($sm['g']['m']=='searchleads' && $sm['g']['d'] == 'search_history')
				$history_selected = true;

			if ($sm['g']['m']=='searchtechleads' &&  ($sm['g']['d'] == 'view' || $sm['g']['d'] == ''))
				$history_selected = true;
			elseif($sm['g']['m']=='searchtechleads')
				$search_selected = true;

			if (System::HasGoogleSearchInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchleads',
					'icon_class'=>'',
					'title'=>'<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Symbols" stroke="none" stroke-width="1" fill="CurrentColor" fill-rule="evenodd"><path d="M14.2506122,15.0064715 C14.4498309,15.0064715 14.6256122,14.9302997 14.7779559,14.7779559 C14.9302997,14.6256122 15.0064715,14.4498309 15.0064715,14.2506122 C15.0064715,14.0513934 14.9302997,13.8756122 14.7779559,13.7232684 L14.7779559,13.7232684 L11.3853778,10.3306903 C12.4283465,8.9478778 12.8765887,7.44201842 12.7301044,5.81311217 C12.58362,4.18420592 11.8775653,2.7838153 10.6119403,1.6119403 C9.28772155,0.486940299 7.81115905,-0.049192514 6.1822528,0.00354186101 C4.55334655,0.056276236 3.11779967,0.680299674 1.87561217,1.87561217 C0.680299674,3.11779967 0.056276236,4.55334655 0.00354186101,6.1822528 C-0.049192514,7.81115905 0.486940299,9.28772155 1.6119403,10.6119403 C2.7838153,11.8775653 4.18420592,12.58362 5.81311217,12.7301044 C7.44201842,12.8765887 8.9478778,12.4283465 10.3306903,11.3853778 L10.3306903,11.3853778 L13.7232684,14.7779559 C13.8756122,14.9302997 14.0513934,15.0064715 14.2506122,15.0064715 Z M6.37561217,11.2447528 C4.99279967,11.2095965 3.84436217,10.7349872 2.93029967,9.82092467 C2.01623717,8.90686217 1.5416278,7.75842467 1.50647155,6.37561217 C1.5416278,4.99279967 2.01623717,3.84436217 2.93029967,2.93029967 C3.84436217,2.01623717 4.99279967,1.5416278 6.37561217,1.50647155 C7.75842467,1.5416278 8.90686217,2.01623717 9.82092467,2.93029967 C10.7349872,3.84436217 11.2095965,4.99279967 11.2447528,6.37561217 C11.2095965,7.75842467 10.7349872,8.90686217 9.82092467,9.82092467 C8.90686217,10.7349872 7.75842467,11.2095965 6.37561217,11.2447528 Z" id="path-1"></path></g></svg> <span>Search Leads</span>',
					'selected' => $search_selected
				];
			elseif (System::HasBuiltWithInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchtechleads&d=searchleads',
					'icon_class'=>'',
					'title'=>'<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Symbols" stroke="none" stroke-width="1" fill="CurrentColor" fill-rule="evenodd"><path d="M14.2506122,15.0064715 C14.4498309,15.0064715 14.6256122,14.9302997 14.7779559,14.7779559 C14.9302997,14.6256122 15.0064715,14.4498309 15.0064715,14.2506122 C15.0064715,14.0513934 14.9302997,13.8756122 14.7779559,13.7232684 L14.7779559,13.7232684 L11.3853778,10.3306903 C12.4283465,8.9478778 12.8765887,7.44201842 12.7301044,5.81311217 C12.58362,4.18420592 11.8775653,2.7838153 10.6119403,1.6119403 C9.28772155,0.486940299 7.81115905,-0.049192514 6.1822528,0.00354186101 C4.55334655,0.056276236 3.11779967,0.680299674 1.87561217,1.87561217 C0.680299674,3.11779967 0.056276236,4.55334655 0.00354186101,6.1822528 C-0.049192514,7.81115905 0.486940299,9.28772155 1.6119403,10.6119403 C2.7838153,11.8775653 4.18420592,12.58362 5.81311217,12.7301044 C7.44201842,12.8765887 8.9478778,12.4283465 10.3306903,11.3853778 L10.3306903,11.3853778 L13.7232684,14.7779559 C13.8756122,14.9302997 14.0513934,15.0064715 14.2506122,15.0064715 Z M6.37561217,11.2447528 C4.99279967,11.2095965 3.84436217,10.7349872 2.93029967,9.82092467 C2.01623717,8.90686217 1.5416278,7.75842467 1.50647155,6.37561217 C1.5416278,4.99279967 2.01623717,3.84436217 2.93029967,2.93029967 C3.84436217,2.01623717 4.99279967,1.5416278 6.37561217,1.50647155 C7.75842467,1.5416278 8.90686217,2.01623717 9.82092467,2.93029967 C10.7349872,3.84436217 11.2095965,4.99279967 11.2447528,6.37561217 C11.2095965,7.75842467 10.7349872,8.90686217 9.82092467,9.82092467 C8.90686217,10.7349872 7.75842467,11.2095965 6.37561217,11.2447528 Z" id="path-1"></path></g></svg> <span>Search Leads</span>',
					'selected' => $search_selected
				];
			elseif (System::HasSicCodesSearchInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchsiccode',
					'icon_class'=>'',
					'title'=>'<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Symbols" stroke="none" stroke-width="1" fill="CurrentColor" fill-rule="evenodd"><path d="M14.2506122,15.0064715 C14.4498309,15.0064715 14.6256122,14.9302997 14.7779559,14.7779559 C14.9302997,14.6256122 15.0064715,14.4498309 15.0064715,14.2506122 C15.0064715,14.0513934 14.9302997,13.8756122 14.7779559,13.7232684 L14.7779559,13.7232684 L11.3853778,10.3306903 C12.4283465,8.9478778 12.8765887,7.44201842 12.7301044,5.81311217 C12.58362,4.18420592 11.8775653,2.7838153 10.6119403,1.6119403 C9.28772155,0.486940299 7.81115905,-0.049192514 6.1822528,0.00354186101 C4.55334655,0.056276236 3.11779967,0.680299674 1.87561217,1.87561217 C0.680299674,3.11779967 0.056276236,4.55334655 0.00354186101,6.1822528 C-0.049192514,7.81115905 0.486940299,9.28772155 1.6119403,10.6119403 C2.7838153,11.8775653 4.18420592,12.58362 5.81311217,12.7301044 C7.44201842,12.8765887 8.9478778,12.4283465 10.3306903,11.3853778 L10.3306903,11.3853778 L13.7232684,14.7779559 C13.8756122,14.9302997 14.0513934,15.0064715 14.2506122,15.0064715 Z M6.37561217,11.2447528 C4.99279967,11.2095965 3.84436217,10.7349872 2.93029967,9.82092467 C2.01623717,8.90686217 1.5416278,7.75842467 1.50647155,6.37561217 C1.5416278,4.99279967 2.01623717,3.84436217 2.93029967,2.93029967 C3.84436217,2.01623717 4.99279967,1.5416278 6.37561217,1.50647155 C7.75842467,1.5416278 8.90686217,2.01623717 9.82092467,2.93029967 C10.7349872,3.84436217 11.2095965,4.99279967 11.2447528,6.37561217 C11.2095965,7.75842467 10.7349872,8.90686217 9.82092467,9.82092467 C8.90686217,10.7349872 7.75842467,11.2095965 6.37561217,11.2447528 Z" id="path-1"></path></g></svg> <span>Search Leads</span>',
					'selected' => $search_selected
				];
			elseif (System::HasStatesSearchInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchsiccode&d=states',
					'icon_class'=>'',
					'title'=>'<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Symbols" stroke="none" stroke-width="1" fill="CurrentColor" fill-rule="evenodd"><path d="M14.2506122,15.0064715 C14.4498309,15.0064715 14.6256122,14.9302997 14.7779559,14.7779559 C14.9302997,14.6256122 15.0064715,14.4498309 15.0064715,14.2506122 C15.0064715,14.0513934 14.9302997,13.8756122 14.7779559,13.7232684 L14.7779559,13.7232684 L11.3853778,10.3306903 C12.4283465,8.9478778 12.8765887,7.44201842 12.7301044,5.81311217 C12.58362,4.18420592 11.8775653,2.7838153 10.6119403,1.6119403 C9.28772155,0.486940299 7.81115905,-0.049192514 6.1822528,0.00354186101 C4.55334655,0.056276236 3.11779967,0.680299674 1.87561217,1.87561217 C0.680299674,3.11779967 0.056276236,4.55334655 0.00354186101,6.1822528 C-0.049192514,7.81115905 0.486940299,9.28772155 1.6119403,10.6119403 C2.7838153,11.8775653 4.18420592,12.58362 5.81311217,12.7301044 C7.44201842,12.8765887 8.9478778,12.4283465 10.3306903,11.3853778 L10.3306903,11.3853778 L13.7232684,14.7779559 C13.8756122,14.9302997 14.0513934,15.0064715 14.2506122,15.0064715 Z M6.37561217,11.2447528 C4.99279967,11.2095965 3.84436217,10.7349872 2.93029967,9.82092467 C2.01623717,8.90686217 1.5416278,7.75842467 1.50647155,6.37561217 C1.5416278,4.99279967 2.01623717,3.84436217 2.93029967,2.93029967 C3.84436217,2.01623717 4.99279967,1.5416278 6.37561217,1.50647155 C7.75842467,1.5416278 8.90686217,2.01623717 9.82092467,2.93029967 C10.7349872,3.84436217 11.2095965,4.99279967 11.2447528,6.37561217 C11.2095965,7.75842467 10.7349872,8.90686217 9.82092467,9.82092467 C8.90686217,10.7349872 7.75842467,11.2095965 6.37561217,11.2447528 Z" id="path-1"></path></g></svg> <span>Search Leads</span>',
					'selected' => $search_selected
				];

			if (System::HasGoogleSearchInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchleads&d=search_history',
					'icon_class'=>'',
					'title'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none"><path d="M14.75 0.25C15.1642 0.25 15.5 0.58579 15.5 1V13C15.5 13.4142 15.1642 13.75 14.75 13.75H1.25C0.83579 13.75 0.5 13.4142 0.5 13V1C0.5 0.58579 0.83579 0.25 1.25 0.25H14.75ZM4.56203 8.5H2V12.25H14V8.5H11.438C10.8593 9.82442 9.53772 10.75 8 10.75C6.46226 10.75 5.1407 9.82442 4.56203 8.5ZM14 1.75H2V7H5.75C5.75 8.24267 6.75733 9.25 8 9.25C9.24267 9.25 10.25 8.24267 10.25 7H14V1.75Z" fill="white"/></svg> <span>Search History</span>',
					'selected' => $history_selected
				];
			elseif (System::HasBuiltWithInstalled())
				$sm['mainmenu'][] = [
					'url'=>'index.php?m=searchtechleads',
					'icon_class'=>'',
					'title'=>'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none"><path d="M14.75 0.25C15.1642 0.25 15.5 0.58579 15.5 1V13C15.5 13.4142 15.1642 13.75 14.75 13.75H1.25C0.83579 13.75 0.5 13.4142 0.5 13V1C0.5 0.58579 0.83579 0.25 1.25 0.25H14.75ZM4.56203 8.5H2V12.25H14V8.5H11.438C10.8593 9.82442 9.53772 10.75 8 10.75C6.46226 10.75 5.1407 9.82442 4.56203 8.5ZM14 1.75H2V7H5.75C5.75 8.24267 6.75733 9.25 8 9.25C9.24267 9.25 10.25 8.24267 10.25 7H14V1.75Z" fill="white"/></svg> <span>Search History</span>',
					'selected' => $history_selected
				];
		}


	if ( System::LoggedIn() )
		{
			if (!SearchSectionAvailableForCompanies() && System::MyAccount()->isSuperAdmin() && TCompany::isSystemCompany() && (System::HasGoogleSearchInstalled() || System::HasBuiltWithInstalled()) )
				{
					if (System::HasGoogleSearchInstalled())
						$sm['settingsmenu'][]=Array(
							'url'=>'index.php?m=searchleads',
							'icon_class'=>'',
							'title'=>'Search Leads',
							'selected' => $sm['g']['m']=='searchleads' || ( $sm['g']['m']== 'customerdetails' &&  !empty($_getvars['search']) )
						);
					else
						$sm['settingsmenu'][]=Array(
							'url'=>'index.php?m=searchtechleads&d=searchleads',
							'icon_class'=>'',
							'title'=>'Search Leads',
							'selected' => $sm['g']['m']=='searchtechleads' || ( $sm['g']['m']== 'customerdetails' &&  !empty($_getvars['search']) )
						);
				}

			$sm['settingsmenu'][]=Array(
				'title'=>'User Management',
				'url'=>'index.php?m=usersmgmt&d=list&id='.TCompany::CurrentCompany()->ID(),
				'icon'=>'<span aria-hidden="true" class="icon icon-bag"></span>',
				'selected' => $sm['g']['m']=='usersmgmt'
			);
		}

	if (System::MyAccount()->isSuperAdmin())
		{
			if (!SearchSectionAvailableForCompanies())
				{
					$sm['accountmenuactions'][]=Array(
						'url'=>'index.php?m=companiesmgmt&d=switchcompany&id=1',
						'title'=>'Super Admin',
						'image'=>'themes/current/images/setting-icon.svg',
						'selected'=>$sm['g']['m']=='licenseemgmt'
					);
				}

			$sm['accountmenuactions'][] = Array(
				'title' => 'Client Management',
				'url' => 'index.php?m=clientsmanagement',
				'selected' => $sm['g']['m'] == 'clientsmanagement'
			);

			$sm['accountmenuactions'][] = Array(
				'title' => 'System Settings',
				'url' => 'index.php?m=globalsettings',
				'selected' => $sm['g']['m'] == 'globalsettings' && $sm['g']['d'] != 'templatectg' && $sm['g']['d'] != 'packagesmgmt'
			);

			$sm['accountmenuactions'][] = Array(
				'url' => 'index.php?m=companydomain',
				'title' => 'Email Domain',
				'selected' => $sm['g']['m'] == 'companydomain'
			);

			$sm['accountmenuactions'][] = Array(
				'url' => 'index.php?m=globalsettings&d=packagesmgmt',
				'title' => 'Packages Management',
				'selected' => $sm['g']['d'] == 'packagesmgmt'
			);
		}
	//Main Menu Finish


	if ( $userinfo['level'] > 0 && $currentcompany->BusinessWizardAvailable() && $sm['g']['m']!='businesswizard')
		sm_redirect('index.php?m=businesswizard');