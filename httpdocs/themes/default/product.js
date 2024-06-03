function product_qty_update()
	{
		var qty=parseInt($('#product-qty-selector').val());
		console.log(qty);
		if (qty && qty>=1)
			$('#product-quantity').val(qty);
		else
			$('#product-quantity').val(1);
		recalc_total();
	}

function product_shipping_update(id)
	{
		$('#product-shipping_id').val(id);
		recalc_total();
	}

function recalc_total()
	{
		var price=$('#product-price').val();
		var discount=$('#product-discount').val();
		var qty=$('#product-quantity').val();
		var shipping=0;
		var shipping_id=$('#product-shipping_id').val();
		if (shipping_id>0)
			shipping=parseFloat($('#shipping-id-selector-'+shipping_id).attr('data-price'));
		var total=(price-discount)*qty+shipping;
		$('#btn-total-txt').html(total.toLocaleString('en-US', { style: 'currency', currency: 'USD' }));
	}

$( document ).ready(function() {
	recalc_total();
	$("#product_email").on('propertychange input', function (e) {
		$("#account_email").val($("#product_email").val());
	});
});
