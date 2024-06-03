function dash_show_conversation_all(id_customer)
	{
		$('.rd-dash-conversation-view').html('<div class="rd-dash-conversation-loading">Loading...</div>');
		$('.rd-dash-conversation-view').load("index.php?m=conversation&d=allmessages&customer="+id_customer, function() {});
	}