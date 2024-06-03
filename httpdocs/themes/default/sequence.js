function loader_sequence(url)
	{
		$('.sequence-loader').addClass('sequence-loading');
		$('.sequence-loading').removeClass('sequence-loader');
		$('.sequence-loading').html('Loading...');
		$('.sequence-loading').load(url, function() {
			$('.sequence-loading').removeClass('sequence-loading');
		});
		$('.sequence-loading').removeClass('sequence-loading');
	}

function remove_sequence(url, id, count)
	{
        $('.email_row#' + id).load(url, function() {
            $('.email_row#' + id).remove();
            $('.add_sequence_item.sequence-loader > div').css('display', 'table');
            $('.add_sequence_item.sequence-loader > div').css('margin', 'auto');
			$('.add_sequence_item.sequence-loader').css('display', 'table');

        });
	}