function checkfordelete()
	{
		if ($('.admintable-control-checkbox:checked').length>0)
			$('.bulkdelete').show();
		else
			$('.bulkdelete').hide();
	}

function checkforexport()
	{
		if ($('.admintable-control-checkbox:checked').length>0)
			$('.bulkexport').show();
		else
			$('.bulkexport').hide();
	}