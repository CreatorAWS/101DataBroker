<?php

	if (!defined("SIMAN_DEFINED"))
		exit('Hacking attempt!');

	if (!defined("subscription_FUNCTIONS_DEFINED"))
		{

			define("subscription_FUNCTIONS_DEFINED", 1);
		}

	sm_default_action('view');
	sm_add_body_class('product_page');
	sm_add_body_class('onecolumb-cart-page');
	if (sm_action('update'))
		{
			sm_use('ui.interface');
			sm_use('ui.form');
			$ui = new TInterface();
			$subscription=new TSubscription($_getvars['id']);
			if ($subscription->Exists() && strcmp($_getvars['hash'], $subscription->PublicHash())==0)
				{
					if (empty($_postvars['stripeToken']))
						$error_message = 'Wrong payment information';
					if (empty($error_message))
						{
							$stripe_source = $_postvars['stripeToken'];
							$company=new TCompany($subscription->CompanyID());
							\Stripe\Stripe::setApiKey($company->StripeSecretKey());
							try
								{
									$subscription->SetStripeCardID($stripe_source, true);
								}
							catch (\Stripe\Error\Card $e)
								{
									$error = true;
								}
							if (!$error)
								{
									sm_redirect('index.php?m='.sm_current_module().'&d=thankyou');
								}
							else
								{
									sm_title('Error');
									sm_use('ui.interface');
									$ui = new TInterface();
									$ui->p('Unable to attach the card.');
									$ui->p('Please try again later.');
									$ui->Output(true);
								}
						}
					if (!empty($error_message))
						sm_set_action('view');
				}
		}
	if (sm_action('view'))
		{
			sm_use('ui.interface');
			sm_use('ui.form');
			$ui = new TInterface();
			$subscription=new TSubscription($_getvars['id']);
			if ($subscription->Exists() && strcmp($_getvars['hash'], $subscription->PublicHash())==0)
				{
					sm_add_cssfile('subscriptioncard.css');
					$company=new TCompany($subscription->CompanyID());
					$info=Array();
					sm_title('Update Card Info');
					$info['charge_url']='index.php?m=subscriptioncard&d=update&id='.$subscription->ID().'&hash='.urlencode($subscription->PublicHash());
					$info['years']=range(intval(date('Y')), intval(date('Y'))+35);
					if (count($sm['p'])>0)
						{
							foreach ($sm['p'] as $key=>$val)
								$info['form'][$key]=$val;
						}
					if (!empty($error_message))
						$info['error_message']=$error_message;
					//--------------------------------------------------------------------------
					$ui->AddTPL('subscriptioncard.tpl', 'view', $info);
					//--------------------------------------------------------------------------
					sm_html_headend('
							<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
					');
					sm_html_headend('
							<script type="text/javascript">
							// This identifies your website in the createToken call below
							Stripe.setPublishableKey(\''.$company->StripePublicKey().'\');
						
							var stripeResponseHandler = function(status, response) {
							  var $form = $(\'#payment-form\');
						
							  if (response.error) {
								// Show the errors on the form
								$form.find(\'.subscription-payment-errors\').text(response.error.message);
								$form.find(\'.subscription-payment-errors\').show();
								$form.find(\'button\').prop(\'disabled\', false);
							  } else {
								// token contains id, last4, and card type
								var token = response.id;
								// Insert the token into the form so it gets submitted to the server
								$form.append($(\'<input type="hidden" name="stripeToken" />\').val(token));
								// and re-submit
								$form.get(0).submit();
							  }
							};
						
							jQuery(function($) {
							  $(\'#payment-form\').submit(function(e) {
								var $form = $(this);
								$form.find(\'.subscription-payment-errors\').text("");
								$form.find(\'.subscription-payment-errors\').hide();
						
								// Disable the submit button to prevent repeated clicks
								$form.find(\'button\').prop(\'disabled\', true);
								
								if ($("#subscription_name").val()=="")
									{
										$form.find(\'.subscription-payment-errors\').text("Wrong name");
										$form.find(\'.subscription-payment-errors\').show();
										$form.find(\'button\').prop(\'disabled\', false);
									}
								else
									Stripe.card.createToken($form, stripeResponseHandler);
						
								// Prevent the form from submitting with the default action
								return false;
							  });
							});
						  </script>
					');
					//--------------------------------------------------------------------------
					if ($company->HasLogo())
						{
							$ui->style("
								.navbar-brand {
									margin: 0;
									background: url(".sm_homepage().$company->LogoURL().") no-repeat left center;
									min-width: 300px;
									text-indent: -9999px;
								}
							");
						}
					//--------------------------------------------------------------------------
					$ui->Output(true);
				}
		}
	if (sm_action('thankyou'))
		{
			sm_title('Thank you');
			sm_use('ui.interface');
			$ui = new TInterface();
			$ui->p('You have successfully updated your card.');
			$ui->Output(true);
		}
