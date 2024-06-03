
$( document ).ready(function() {
	$('.smsblast').hide();
	$(".admintable-control-checkbox").change(function() {
		if ($('.admintable-control-checkbox:checked').length>0)
			$('.smsblast').show();
		else
			$('.smsblast').hide();
	});
});