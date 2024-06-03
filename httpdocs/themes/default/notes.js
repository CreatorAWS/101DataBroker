
var last_id_customer=0;
var dash_no_focus_conversation=false;

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
