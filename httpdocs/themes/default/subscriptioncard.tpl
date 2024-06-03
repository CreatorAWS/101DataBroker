{*check subscription.css*}

<div class="row subscription-block">
	<div class="col-md-12 subscription-main-bar">
		<form id="payment-form" class="subscription-payment-form" method="POST" action="{$data.charge_url}">
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
                    <div class="col-md-12 subscription-text-payment-button-container">
                        <button type="submit" class="subscription-text-payment-button">Update Credit Card</button>
                        <div id="showLoading" style="display: none;">Loading...</div>
                    </div>
                </div>
            </div>
		</form>
	</div>
</div>