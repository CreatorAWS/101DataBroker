<?php

	include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/core/api.php');
	include_once(sm_cms_rootdir().'modules/preload/data_structure_update.php');
	include_once(sm_cms_rootdir().'ext/stripe/Stripe.php');

	use_api('sms');
	use \Mailjet\Resources;
	use Stripe\Stripe;

	function MailJetSend($from, $to, $subject, $message, $attachment='', $company_name='', $id_email = '')
		{
			if (empty($company_name))
				$company_name = resource_name();

			if (strpos($from, '@'.mail_domain())!==false || true)
				{
					if($from == 'noreply@'.mail_domain())
						{
							$apikey = sm_settings('mailjet_api_key');
							$apisecret = sm_settings('mailjet_api_secret');
						}
					else
						{
							$company = TCompany::initWithEmail($from);
							if ($company->Exists() && $company->EmailFrom() && $company->HasMailjetApiSecret())
								{
									$apikey = $company->MailjetApiKey();
									$apisecret = $company->MailjetApiSecret();
									$company_name = $company->Name();
								}
							else
								{
									$apikey = sm_settings('mailjet_api_key');
									$apisecret = sm_settings('mailjet_api_secret');
								}
						}

					$mj = new \Mailjet\Client($apikey, $apisecret,true,['version' => 'v3.1']);

					$data['From'] = [
						'Email' => $from,
						'Name' => $company_name
					];
					$data['To'] = [
						[
							'Email' => $to
						]
					];

					$data['Subject'] = $subject;
					$data['HTMLPart'] = $message;

					if (!empty($id_email))
						{
							$data['Headers'] = [
								'Reply-To' => '<'.$id_email.'@'.mail_domain().'>'
							];
						}

					$body = ['Messages' => [$data]];
					$response = $mj->post(Resources::$Email, ['body' => $body]);
					return $response->getBody();
				}
		}

	if (intval(sm_settings('disable_mailjet'))!=1)
		{
			require sm_cms_rootdir().'ext/vendor/autoload.php';

			$apikey=sm_settings('mailjet_api_key');
			$apisecret=sm_settings('mailjet_api_secret');

			$mj=new \Mailjet\Client($apikey, $apisecret);


			if (intval(sm_settings('mailjet_tracking_open'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"open",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=open",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_open', 1);
					unset ($body);

					$body=[
						'EventType'=>"sent",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=sent",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
				}
			if (intval(sm_settings('mailjet_tracking_click'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"Click",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=click",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_click', 1);
				}
			if (intval(sm_settings('mailjet_tracking_bounce'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"Bounce",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=bounce",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_bounce', 1);
				}
			if (intval(sm_settings('mailjet_tracking_spam'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"Spam",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=spam",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_spam', 1);
				}
			if (intval(sm_settings('mailjet_tracking_blocked'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"Blocked",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=blocked",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_blocked', 1);
				}

			if (intval(sm_settings('mailjet_tracking_unsub'))!=1 && !empty(sm_settings('mailjet_api_key')))
				{
					$body=[
						'EventType'=>"Unsub",
						'Url'=>sm_homepage()."index.php?m=stats&d=getstatus&type=unsub",
						'Version'=>"2"
					];
					$response=$mj->post(Resources::$Eventcallbackurl, ['body'=>$body]);
					sm_update_settings('mailjet_tracking_unsub', 1);
				}
		}

	$sm['s']['current_logo']='themes/'.sm_current_theme().'/images/logo.png';

	if ($userinfo['level']==0)
		{
			if (TCompany::SystemCompany()->HasStripeSecretKey())
				Stripe::setApiKey(TCompany::SystemCompany()->StripeSecretKey());

			if (TCompany::SystemCompany()->HasSystemLogoImageURL())
				$sm['s']['current_logo']=TCompany::SystemCompany()->SystemLogoImageURL();

		}

	include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/core/api.php');
	include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/core/ttowerdata.php');
	use_api('sms');
	use_api('voice');
	if ($userinfo['level']>0)
		{
			use_api('temployee');
			$myaccount=new TEmployee($userinfo['id']);
			$sm['assets_section_allowed']=$myaccount->isAllowedToManageAssets() || $myaccount->isAllowedToDownloadAssets();
		}

