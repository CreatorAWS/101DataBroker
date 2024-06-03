<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	$company = new TCompany(intval($_getvars['company']));
	if ($company->Exists())
		{
			\Stripe\Stripe::setApiKey($company->StripeSecretKey());

			// You can find your endpoint's secret in your webhook settings
			$endpoint_secret=$company->StripeWebhooksEndpointSecret();

			$payload=@file_get_contents("php://input");
			$sig_header=$_SERVER["HTTP_STRIPE_SIGNATURE"];
			$event=NULL;

			try
				{
					$event=\Stripe\Webhook::constructEvent(
						$payload, $sig_header, $endpoint_secret
					);
				}
			catch (\UnexpectedValueException $e)
				{
					// Invalid payload
					http_response_code(400); // PHP 5.4 or greater
					exit();
				}
			catch (\Stripe\Error\SignatureVerification $e)
				{
					// Invalid signature
					http_response_code(400); // PHP 5.4 or greater
					exit();
				}

			// Do something with $event
			sm_log('stripewebhook', $company->ID(), $event->id.' '.$event->type.' *** '.print_r($event, true));

			try
				{
					if ($event->type=='invoice.payment_succeeded')
						{
							for ($i=0; $i<count($event->data->object->lines->data); $i++)
								{
									if ($event->data->object->lines->data[0]->type=='subscription')
										{
											$subscription_id=$event->data->object->lines->data[0]->subscription;
											if (empty($subscription_id) || substr($subscription_id, 0, 4)!='sub_')
												$subscription_id=$event->data->object->lines->data[0]->id;
											sm_set_action('subscription-renew');
											break;
										}
								}
						}
					elseif ($event->type=='invoice.payment_failed')
						{
							for ($i=0; $i<count($event->data->object->lines->data); $i++)
								{
									if ($event->data->object->lines->data[0]->type=='subscription')
										{
											$subscription_id=$event->data->object->lines->data[0]->subscription;
											if (empty($subscription_id) || substr($subscription_id, 0, 4)!='sub_')
												$subscription_id=$event->data->object->lines->data[0]->id;
											sm_set_action('subscription-failed-renew');
											break;
										}
								}
						}
					elseif ($event->type=='customer.subscription.deleted')
						{
							for ($i=0; $i<count($event->data->object->items->data); $i++)
								{
									if (!empty($event->data->object->items->data[0]->subscription))
										{
											$subscription_id=$event->data->object->items->data[0]->subscription;
											sm_set_action('subscription-deleted');
											break;
										}
								}
						}
				}
			catch (Exception $e)
				{
					http_response_code(200); // PHP 5.4 or greater
					exit;
				}

			if (sm_action('subscription-renew'))
				{
					$subscription=TSubscription::InitWithStripeSubscriptionID($company, $subscription_id);
					if ($subscription->Exists())
						{
							$subscription->SetLastSuccessfulPaymentTimestamp(time());
							$subscription->SetBillingCyclesPaid($subscription->BillingCyclesPaid()+1);
							$subscription->SetUnsuccessfulPaymentsCount(0);
							$subscription->RenewExpirationTime();
							$subscription->CreateRenewSubscriptionWebhook();
						}
				}
			if (sm_action('subscription-failed-renew'))
				{
					$subscription=TSubscription::InitWithStripeSubscriptionID($company, $subscription_id);
					if ($subscription->Exists())
						{
							$subscription->SetUnsuccessfulPaymentsCount($subscription->UnsuccessfulPaymentsCount()+1);
						}
				}
			if (sm_action('subscription-deleted'))
				{
					$subscription=TSubscription::InitWithStripeSubscriptionID($company, $subscription_id);
					if ($subscription->Exists() && !$subscription->isCancelled())
						{
							$subscription->Cancel(false);
						}
				}

			http_response_code(200); // PHP 5.4 or greater
			exit;
		}
