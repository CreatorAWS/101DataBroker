var nextsidebaroffsetajax=0;
var dashboardsendmessageurl='';
var activetab='';
var last_id_customer=0;
var dash_select_first=true;
var dash_no_focus_conversation=false;
var timer = null;

function rd_dash_load_sidebar()
	{
		var activetab = $('#activetab').html();
		$('.rd-dash-sidebar-load').before('<div class="rd-dash-sidebar-loading">Loading...</div>');
		$('.rd-dash-sidebar-load').hide();
		if (dash_url!='')
			{
				var url=dash_url;
				dash_url='';
			}
		else if (activetab === 'incoming')
			{
				var url="index.php?m=conversation&d=emailsidebar&type=incoming&from="+nextsidebaroffsetajax;
			}
		else
			var url="index.php?m=conversation&d=emailsidebar&from="+nextsidebaroffsetajax;


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

function check_for_new_messages(id_customer)
	{
		var lastid = $('#id').val();
		var lastcount = parseInt($('.timeline').last().data('number'), 10);
		$('#loadmore').after('<div id="preloader"></div>');
		$('#loadmore').remove();
		$('#id').remove();
		$('#lastcount').remove();
		$('#preloader').after().load("index.php?m=conversation&d=emailconversation&lastid="+lastid+"&customer="+id_customer, function(){
			$('#preloader .timeline').attr('data-number', lastcount+1);
			$('#preloader #show-full-message-0').attr('onclick', 'show_hide('+ (lastcount+1) +')');
			$('#preloader #show-full-message-0').attr('id', 'show-full-message-' + (lastcount+1));
			$('#preloader #full-message-0').attr('id', 'full-message-' + (lastcount+1));
			$('#preloader').removeAttr('id');
			$('#preloader').removeAttr('count');
			var newlastid = $('#id').val();
			if (newlastid != lastid)
				{
					$(document).scrollTop($(document).height());
				}
		});

	}

function dash_show_conversation(id_customer)
	{
		if (timer != null)
			clearInterval(timer);

		last_id_customer=id_customer;
		$('.rd-dash-sidebar-active').removeClass('rd-dash-sidebar-active');
		$('#dashsideitem-'+id_customer).addClass('rd-dash-sidebar-active');
		$('#dashsideitem-'+id_customer).removeClass('dash-sidebar-unread');
		$('.rd-dash-conversation-view').html('<div class="rd-dash-conversation-loading">Loading...</div>');

		$('.rd-dash-conversation-view').load("index.php?m=conversation&d=emailconversation&customer="+id_customer, function()
			{
				$('.rd-dash-conversation-answer').show();
				$(document).scrollTop(0);
			});
	}


$(document).ready(function(){
	rd_dash_load_sidebar();
});


