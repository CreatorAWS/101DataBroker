function subscription_qty_update()
	{
		var qty=parseInt($('#subscription-qty-selector').val());
		console.log(qty);
		if (qty && qty>=1)
			$('#subscription-quantity').val(qty);
		else
			$('#subscription-quantity').val(1);
		recalc_subscription_total();
	}

function recalc_subscription_total()
	{
		var price=parseFloat($('#subscription-price').val());
		var setup_fee=parseFloat($('#subscription-fee').val());
		var qty=parseInt($('#subscription-quantity').val());
		var trial_type=parseInt($('#subscription-trial-type').val());
		var discount=$('#product-discount').val();
		var total=0;
		var period_total=(price-discount)*qty;
		if (trial_type==0)
			total=(price-discount)*qty+setup_fee;
		else
			{
				if (trial_type==1)
					total = 0;
				else
					total = setup_fee;
			}
		$('#btn-total-txt').html(total.toLocaleString('en-US', { style: 'currency', currency: 'USD' }));
		$('.subscription-total-val').html(total.toLocaleString('en-US', { style: 'currency', currency: 'USD' }));
		$('#trial-box-period-total').html(period_total.toLocaleString('en-US', { style: 'currency', currency: 'USD' }));
	}

$( document ).ready(function() {
	recalc_subscription_total();
	$("#subscription_email").on('propertychange input', function (e) {
		$("#account_email").val($("#subscription_email").val());
	});
});
