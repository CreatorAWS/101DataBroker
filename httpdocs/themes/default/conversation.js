var nextsidebaroffsetajax=0;
var dashboardsendmessageurl='';
var last_id_customer=0;
var dash_select_first=true;
var dash_no_focus_conversation=false;

function rd_dash_load_sidebar()
	{
		$('.rd-dash-sidebar-load').before('<div class="rd-dash-sidebar-loading">Loading...</div>');
		$('.rd-dash-sidebar-load').hide();
		if (dash_url!='')
			{
				var url=dash_url;
				dash_url='';
			}
		else
			var url="index.php?m=conversation&d=sidebar&from="+nextsidebaroffsetajax;
		$('.rd-dash-sidebar-loading').load(url, function() {
			var nextsidebaroffsetajax1=$('#nextsidebaroffsetajax').html();
			if (parseInt(nextsidebaroffsetajax)<parseInt(nextsidebaroffsetajax1))
				nextsidebaroffsetajax=parseInt(nextsidebaroffsetajax1);
			$('#nextsidebaroffsetajax').remove();
			$('.rd-dash-sidebar-loading').removeClass('rd-dash-sidebar-loading');
			$('.rd-dash-sidebar-load').show();
			if (dash_select_first)
				{
					$('.rd-dash-sidebar-item').first().click();
				}
			dash_select_first=false;
		});
	}

function dash_show_conversation(id_customer)
	{
		last_id_customer=id_customer;
		$('.rd-dash-sidebar-active').removeClass('rd-dash-sidebar-active');
		$('#dashsideitem-'+id_customer).addClass('rd-dash-sidebar-active');
		$('#dashsideitem-'+id_customer).removeClass('dash-sidebar-unread');
		$('.rd-dash-conversation-view').html('<div class="rd-dash-conversation-loading">Loading...</div>');
		$('.rd-dash-conversation-view').load("index.php?m=conversation&d=conversation&customer="+id_customer, function() {
			$('.rd-dash-conversation-answer').show();
			if (!dash_no_focus_conversation)
				$('#dashboard-conversation-text').focus();
		});
		$('.rd-dash-customer-info').load("index.php?m=conversation&d=userinfo&customer="+id_customer, function() {
			$('.rd-dash-conversation-answer').show();
			if (!dash_no_focus_conversation)
				$('#dashboard-conversation-text').focus();
		});
	}

function dash_send_message()
	{
		$('.rd-dash-conversation-answer').hide();
		$('.rd-dash-conversation-answer-loading').show();
		var msg=$('#dashboard-conversation-text').val();
		console.log(msg);
		$('#dashboard-conversation-text').val('');
		if (msg!='')
			{
				$.post('index.php?m=conversation&d=sendmessage&customer='+last_id_customer, { text: msg })
					.done(function( data ) {
						$('.rd-dash-conversation-answer-loading').hide();
						$('.rd-dash-conversation-answer').show();
						dash_show_conversation(last_id_customer);
					});
			}
		else
			{
				$('.rd-dash-conversation-answer-loading').hide();
				$('.rd-dash-conversation-answer').show();
				dash_show_conversation(last_id_customer);
			}

	}

function dash_show_note(id_customer)
	{
		last_id_customer=id_customer;
		$('.rd-dash-conversation-view').html('<div class="rd-dash-conversation-loading">Loading...</div>');
		$('.rd-dash-conversation-view').load("index.php?m=notes&d=note&customer="+id_customer, function() {
			$('.rd-dash-conversation-answer').show();
			if (!dash_no_focus_conversation)
				$('#dashboard-conversation-text').focus();
		});
	}

function dash_send_note()
	{
		$('.rd-dash-conversation-answer').hide();
		$('.rd-dash-conversation-answer-loading').show();
		var msg=$('#dashboard-conversation-text').val();
		console.log(msg);
		$('#dashboard-conversation-text').val('');
		if (msg!='')
			{
				$.post('index.php?m=notes&d=addnote&customer='+last_id_customer, { text: msg })
					.done(function( data ) {
						$('.rd-dash-conversation-answer-loading').hide();
						$('.rd-dash-conversation-answer').show();
						dash_show_note(last_id_customer);
					});
			}
		else
			{
				$('.rd-dash-conversation-answer-loading').hide();
				$('.rd-dash-conversation-answer').show();
				dash_show_note(last_id_customer);
			}
	}

$(document).ready(function(){
	rd_dash_load_sidebar();
});