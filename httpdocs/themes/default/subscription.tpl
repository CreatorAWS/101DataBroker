{*check subscription.css*}

<div class="row subscription-block">
	<div class="{if $data.sidebartext neq ""}col-md-8{/if} subscription-main-bar">
		<div class="subscription-price-container">
            <div class="subscription-price"><span class="subscription-price-label">Price</span> <span class="subscription-price-val">{$data.formatted.price}</span> <span class="subscription-price-interval-separator">{$data.price_interval_separator}</span> <span class="subscription-price-interval">{$data.interval_label}</span></div>
            {if $data.setup_fee neq 0}<div class="subscription-setup-fee"><span class="subscription-setup-fee-label">{$data.setup_fee_label}</span> <span class="subscription-setup-fee-val">{$data.formatted.setup_fee}</span></div>{/if}
            {if $data.multiple_qty_allowed}
                <div class="subscription-qty-container">Quantity:
                    <select id="subscription-qty-selector" onclick="subscription_qty_update()" onchange="subscription_qty_update()" onkeydown="subscription_qty_update()" onkeyup="subscription_qty_update()">
                        {section name=i loop=$data.qty_values}
                            <option value="{$data.qty_values[i]}"{if $data.qty_values[i] eq $data.form.subscription_quantity} selected{/if}>{$data.qty_labels[i]}</option>
                        {/section}
                    </select>
                </div>
            {/if}
            <div class="subscription-total"><span class="subscription-total-label">{$data.total_label}</span> <span class="subscription-total-val"></span></div>
            {if $data.trial_period}
                <div class="subscription-trial">{$data.trial_period_text}</div>
            {/if}
			<div class="subscription-price-separator"></div>
		</div>
		{if $data.text neq ""}
			<div class="subscription-text">
				{$data.text}
			</div>
		{/if}
        <hr>
        {if $data.coupon_form_visible}
            <form id="coupon-form" class="product-payment-form" method="POST" action="{$data.apply_coupon_url}">
                <div class="pyments-header">
                    <h4>Apply Coupon</h4>
                </div>
                <div class="payments_content">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" data-stripe="name" id="coupon_code" name="coupon_code" class="form-control" placeholder="Coupon Code">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="product-text-payment-button">Apply</button>
                        </div>
                    </div>
                    {if $data.coupon_discount_text neq ""}
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$data.coupon_discount_text}</h3>
                            </div>
                        </div>
                    {/if}
                </div>
            </form>
        {/if}
		<div class="head_info">
            <h4>Contact Information</h4>
        </div>
		<form id="payment-form" class="subscription-payment-form" method="POST" action="{$data.charge_url}">
            <input type="hidden" name="subscription_interval_label" id="subscription-interval-label" value="{$data.interval_label}" />
            <input type="hidden" name="subscription_trial_type" id="subscription-trial-type" value="{$data.trial_type}" />
            <input type="hidden" name="subscription_price" id="subscription-price" value="{$data.price}" />
            <input type="hidden" name="subscription_quantity" id="subscription-quantity" value="{$data.form.subscription_quantity}" />
            <input type="hidden" name="setup_fee" id="subscription-fee" value="{$data.setup_fee}" />
            {if $data.coupon_id neq ""}
                <input type="hidden" name="coupon_id" id="coupon_id" value="{$data.coupon_id}" />
            {/if}
            <input type="hidden" name="product_discount" id="product-discount" value="{$data.subscriptiondiscount}" />

			<div class="subscription-payment-errors alert alert-danger" style="{if $data.error_message eq ""}display:none;{/if}">{$data.error_message}</div>
            <div class="body_info">
                <div class="row"><div class="col-md-12">
                    <input type="text" id="subscription_email" name="subscription_email" value="{$data.form.subscription_email}" class="form-control" placeholder="Email Address">
                </div></div>
                <div class="row"><div class="col-md-12">
                    <input type="text" id="subscription_phone" name="subscription_phone" value="{$data.form.subscription_phone}" class="form-control" placeholder="Phone">
                </div></div>
                <div class="row"><div class="col-md-12">
                    <input type="text" id="subscription_address_line1" name="subscription_address_line1" value="{$data.form.subscription_address_line1}" class="form-control" placeholder="Billing Address Street">
                </div></div>
                <div class="row"><div class="col-md-12">
                    <input type="text" id="subscription_address_line2" name="subscription_address_line2" value="{$data.form.subscription_address_line2}" class="form-control" placeholder="Billing Address Line 2">
                </div></div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" id="subscription_address_city" name="subscription_city" value="{$data.form.subscription_city}" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" id="subscription_address_state" value="{$data.form.subscription_state}" name="subscription_state" class="form-control" placeholder="State">
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="subscription_address_zip" value="{$data.form.subscription_zip}" name="subscription_zip" class="form-control" placeholder="Zip">
                    </div>
                </div>
            </div>
            {if $data.account_section}
                <div class="head_info">
                    <h4>Account Setup</h4>
                </div>
                    <div class="body_info">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" id="account_email" name="account_email"
                                       value="{$data.form.account_email}" class="form-control"
                                       placeholder="Email Address" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="password" id="password" value="{$data.form.subscription_state}"
                                       name="password" class="form-control" placeholder="Password">
                            </div>
                            <div class="col-md-6">
                                <input type="password" id="password2" value="{$data.form.subscription_zip}"
                                       name="password2" class="form-control" placeholder="Confirm Password">
                            </div>
                        </div>
                    </div>
                {/if}
			<div class="pyments-header">
                <h4>Payment Information</h4>
                <div style="padding-bottom: 5px;" class="col-md-5 pull-right">
                    <img alt="Credit Cards" src="themes/{$special.theme}/images/types.png" style="padding-top:20px;" />
                </div>
            </div>
            <div class="subscription-payment-errors alert alert-danger" style="{if $data.error_message eq ""}display:none;{/if}">{$data.error_message}</div>
            <div class="payments_content">
                <div class="row">
                    <div class="col-md-6">
                            <input type="text" data-stripe="name" id="subscription_name" name="subscription_name" value="{$data.form.subscription_name}" class="form-control" placeholder="Card Holder's Name">
                        </div>
                        <div class="col-md-6">
                            <input type="text" data-stripe="number" size="16" autocomplete="off" class="form-control" placeholder="Card Number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <input type="password" style="width: 150px;" data-stripe="cvc" size="4" autocomplete="off" class="form-control" placeholder="Card CVV">
                    </div>
                    <div class="col-md-3">
                        <select data-stripe="exp-month" class="form-control">
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select data-stripe="exp-year" class="form-control">
							{section name=i loop=$data.years}<option value="{$data.years[i]}">{$data.years[i]}</option>{/section}
                        </select>
                    </div>
                </div>
                <div class="row">
                    {if $data.trial_box_enabled}
                        <div class="col-md-12 subscription-text-payment-button-container subscription-trial-box-container">
                            <div>{$data.trial_box.first_line_title} {$data.trial_box.first_line_amount}</div>
                            <div>Payments Starting In 60 Days <span id="trial-box-period-total">{$data.price}</span>{$data.price_interval_separator}{$data.interval_label}</div>
                        </div>
                    {/if}
                    <div class="col-md-12 subscription-text-payment-button-container">
                        <button type="submit" class="subscription-text-payment-button">{if $data.custom_payment_button_title neq ""}{$data.custom_payment_button_title}{else}Make a Payment <span id="btn-total-txt">{$data.total_text}</span>{/if}</button>
                        <div id="showLoading" style="display: none;">Loading...</div>
                    </div>
                </div>
            </div>
		</form>
	</div>
    {if $data.sidebartext neq ""}
        <div class="col-md-4 subscription-sidebar">
            {$data.sidebartext}
        </div>
    {/if}
</div>