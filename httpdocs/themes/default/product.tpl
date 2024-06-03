{*check product.css*}

<div class="product-block ">
	<div class="{if $data.sidebartext neq ""}col-md-8{/if} product-main-bar">
		<div class="product-price-wrapper">
            <div class="product-price-container">
                <div class="product-price">${if $data.product_type eq "prepared"}{$data.price_to_show}{else}{$data.price}{/if}</div>
                {if $data.multiple_qty_allowed}
                    <div class="product-qty-container">Quantity: <input class="form-control" type="number" id="product-qty-selector" value="{$data.quantity}" onclick="product_qty_update()" onchange="product_qty_update()" onkeydown="product_qty_update()" onkeyup="product_qty_update()" /></div>
                {/if}
                <div class="product-price-separator"></div>
            </div>
            {if $data.text neq ""}
                <div class="product-text">
                    {$data.text}
                </div>
            {/if}
        </div>
        {if $data.shippable}
            <hr>
            <div class="product-shipping-methods">
                <h4>Shipping Methods</h4>
                {section name=i loop=$data.shipping_methods}
                    <input type="radio" name="shipping_id_selector" id="shipping-id-selector-{$data.shipping_methods[i].id}" value="{$data.shipping_methods[i].id}" {if $data.shipping_methods[i].selected} checked{/if} onclick="product_shipping_update({$data.shipping_methods[i].id})" data-price="{$data.shipping_methods[i].price}" /> {$data.shipping_methods[i].title}<br /> 
                {/section}
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
						<div class="col-md-10">
							<input type="text" data-stripe="name" id="coupon_code" name="coupon_code" class="form-control" placeholder="Coupon Code">
						</div>
						<div class="col-md-2">
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
		<form id="payment-form" class="product-payment-form" method="POST" action="{$data.charge_url}">
            {if $data.shippable}
            {/if}
            <input type="hidden" name="product_price" id="product-price" value="{$data.price}" />
            <input type="hidden" name="product_token" id="product-token" value="{$data.token}" />
            <input type="hidden" name="product_quantity" id="product-quantity" value="{$data.quantity}" />
            <input type="hidden" name="product_discount" id="product-discount" value="{$data.discount}" />
            <input type="hidden" name="shipping_id" id="product-shipping_id" value="{$data.shipping_id}" />
            <div class="product-payment-errors alert alert-danger" style="{if $data.error_message eq ""}display:none;{/if}">{$data.error_message}</div>
            <div class="body_info">
                <div class="row"><div class="col-md-12">
{*                    <label class="control-label product-form-label">Email Address</label> *}
                    <input type="text" id="product_email" name="product_email" class="form-control" placeholder="Email Address">
                </div></div>
                <div class="row"><div class="col-md-12">
{*                    <label class="control-label product-form-label">Phone</label> *}
                    <input type="text" id="product_phone" name="product_phone" class="form-control" placeholder="Phone">
                </div></div>
                <div class="row"><div class="col-md-12">
{*                    <label class="control-label product-form-label">Billing Address Street</label> *}
                    <input type="text" id="product_address_line1" name="product_address_line1" class="form-control" placeholder="Billing Address Street">
                </div></div>
                <div class="row"><div class="col-md-12">
{*                    <label class="control-label product-form-label">Billing Address Line 2</label> *}
                    <input type="text" id="product_address_line2" name="product_address_line2" class="form-control" placeholder="Billing Address Line 2">
                </div></div>
                <div class="row">
                    <div class="col-md-12">
{*                        <label class="control-label product-form-label">City</label> *}
                        <input type="text" id="product_address_city" name="product_city" class="form-control" placeholder="City">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
{*                        <label class="control-label product-form-label">State</label> *}
                        <input type="text" id="product_address_state" name="product_state" class="form-control" placeholder="State">
                    </div>
                    <div class="col-md-6">
{*                        <label class="control-label product-form-label">Zip</label> *}
                        <input type="text" id="product_address_zip" name="product_zip" class="form-control" placeholder="Zip">
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
                            <input type="password" id="password" value=""
                                   name="password" class="form-control" placeholder="Password">
                        </div>
                        <div class="col-md-6">
                            <input type="password" id="password2" value=""
                                   name="password2" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>
            {/if}
			<div class="pyments-header">
                <h4>Payment Information</h4>
                <div style="padding-bottom: 5px;" class="col-md-5 pull-right">
                    <img alt="Credit Cards" src="themes/{$special.theme}/images/types.png" style="padding-top:20px; max-width: 350px;" />
                </div>
            </div>
            <div class="product-payment-errors alert alert-danger" style="{if $data.error_message eq ""}display:none;{/if}">{$data.error_message}</div>
            <div class="payments_content">
                <div class="row">
                    <div class="col-md-6">
{*                        <label class="control-label product-form-label">Card Holder's Name</label>*}
                            <input type="text" data-stripe="name" id="product_name" name="product_name" class="form-control" placeholder="Card Holder's Name">
                        </div>
                        <div class="col-md-6">
{*                        <label class="control-label product-form-label">Card Number</label> *}
                            <input type="text" data-stripe="number" size="16" autocomplete="off" class="form-control" placeholder="Card Number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
{*                        <label class="control-label product-form-label">Card CVV</label> *}
                        <input type="password" style="width: 150px;" data-stripe="cvc" size="4" autocomplete="off" class="form-control" placeholder="Card CVV">
                    </div>
                    <div class="col-md-3">
{*                       <label class="control-label product-form-label">Month</label> *}
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
                {*        <label class="control-label product-form-label">Year</label> *}
                        <select data-stripe="exp-year" class="form-control">
                            {section name=i loop=$data.years}<option value="{$data.years[i]}">{$data.years[i]}</option>{/section}
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 product-text-payment-button-container">
                        <button type="submit" class="product-text-payment-button">Make a Payment <span id="btn-total-txt">{$data.total_text}</span></button>
                        <div id="showLoading" style="display: none;">Loading...</div>
                    </div>
                </div>
            </div>
		</form>
	</div>
    {if $data.sidebartext neq ""}
        <div class="col-md-4 product-sidebar">
            {$data.sidebartext}
        </div>
    {/if}
</div>