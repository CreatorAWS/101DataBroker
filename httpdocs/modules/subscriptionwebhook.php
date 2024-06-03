<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	$subscription = new TSubscription(intval($_getvars['subscription_id']));
	if ($subscription->Exists())
		{
			$data = $_POST;
			
			$company = new TCompany(intval($subscription->CompanyID()));
			if (!$company->Exists())
				{
					http_response_code(400); // PHP 5.4 or greater
					exit();
				}
			if(strcmp($data['webhook_secret'], $company->StripeWebhooksEndpointSecret())!=0)
				{
					http_response_code(400); // PHP 5.4 or greater
					exit();
				}

			\Stripe\Stripe::setApiKey($company->StripeSecretKey());

			try
				{
					if (strcmp($data['action_type'],'subscription-renew')==0)
						{
							sm_set_action('subscription-renew');
						}
					elseif (strcmp($data['action_type'],'subscription-cancel')==0)
						{
							sm_set_action('subscription-deleted');
						}
				}
			catch (Exception $e)
				{
					http_response_code(200); // PHP 5.4 or greater
					exit;
				}

			if (sm_action('subscription-renew'))
				{
					$subscription->SetLastSuccessfulPaymentTimestamp(time());
					$subscription->SetBillingCyclesPaid($subscription->BillingCyclesPaid()+1);
					$subscription->SetUnsuccessfulPaymentsCount(0);
					$subscription->RenewExpirationTime();
					$subscription->CreateRenewSubscriptionWebhook();
				}
			if (sm_action('subscription-deleted'))
				{
					$subscription->Cancel();
				}

			http_response_code(200); // PHP 5.4 or greater
			exit;

		}