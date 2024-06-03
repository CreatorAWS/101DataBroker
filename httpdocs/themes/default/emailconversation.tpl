{section name=i loop=$sm.items}
    <div class="clearfix timeline" data-number="{$smarty.section.i.index}">
        <div class="dash-conversation-divider"></div>

        <div class="rd-dash-conversation-item-footer allmessage-convo {if $sm.items[i].isincoming}rd-dash-item-customer{else}rd-dash-item-employee{/if}">
            {if $sm.items[i].employee neq ""}
                <div class="rd-dash-conversation-item-details"><span>{if $sm.items[i].employee_label neq ""}{$sm.items[i].employee_label}{else}Employee:{/if}</span> {$sm.items[i].employee}

                    {if $sm.items[i].replies_count neq ""}
                        <span class="reply_count">{$sm.items[i].replies_count}</span>
                    {/if}
                </div>
            {else}
                <div class="rd-dash-conversation-item-details"><span>{$sm.label.customer}:</span> {$sm.items[i].customer}

                    {if $sm.items[i].replies_count neq ""}
                        <span class="reply_count">{$sm.items[i].replies_count}</span>
                    {/if}</div>
            {/if}
            {if not $sm.items[i].hide_time}
                <div class="dash-conversation-timeline {$sm.items[i].time_class} {if $sm.items[i].isincoming} rd-dash-conversation-timeline-customer{else} rd-dash-conversation-timeline-employee{/if}">
                    <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
                </div>
            {/if}
        </div>

        <div class="rd-dash-conversation-item{if $sm.items[i].unread} dash-conversation-unread{/if}{if $sm.items[i].employee neq ""} rd-dash-conversation-employee{else} rd-dash-conversation-customer{/if}">
            <div class="clearfix"></div>
            <div class="email-message-preview">
                <span class="email-subject-preview">Subject:</span> {$sm.items[i].subject}
                <a href="javascript:;" class="show-full-message" id="show-full-message-{$smarty.section.i.index}" onclick="show_hide({$smarty.section.i.index}, {$sm.items[i].id})">Open</a>
                <div class="full-message" id="full-message-{$smarty.section.i.index}" style="display: none">
                    <span class="email-subject-preview">Message:</span>  {$sm.items[i].text}
                    {if $sm.items[i].hasattachments}
                        <div class="attachments">
                            <span>Attachments:</span>
                            {section name=j loop=$sm.items[i].files}
                                <a href="{$sm.items[i].files[j].url}" title="{$sm.items[i].files[j].title}" target="_blank"><i class="fa fa-paperclip"></i> {$sm.items[i].files[j].title}</a>
                            {/section}
                        </div>
                    {/if}
                    {if $sm.items[i].replies_count gt 0}
                    <h4 style="margin-top: 30px;">Replies</h4>
                    {/if}
                    {section name=k loop=$sm.items[i].replies}
                        <div class="view-reply-message full-message mt-15 {if $sm.items[i].replies[k].unread} dash-conversation-unread{/if}">
                            <div class="message-header flex">
                                <a href="{$sm.items[i].replies[k].customer_url}" title="{$sm.items[i].replies[k].customer}" class="sender-info flex-1" style="margin-right: 10px;">
                                <span class="sender">
                                    <div>
                                        <span>
                                            {$sm.items[i].replies[k].customer}
                                        </span>
                                        <span>
                                            &lt;{$sm.items[i].replies[k].customer_email}&gt;
                                        </span>
                                    </div>
                                </span>
                                </a>
                                <div class="email-subject m-r-lg flex-1">{$sm.items[i].replies[k].subject}</div>
                                {if not $sm.items[i].replies[k].hide_time}
                                    <div class="dash-conversation-timeline {$sm.items[i].replies[k].time_class} {if $sm.items[i].replies[k].employee neq ""} rd-dash-conversation-timeline-employee{else} rd-dash-conversation-timeline-customer{/if}">
                                        <div class="rd-dash-conversation-time">{$sm.items[i].replies[k].time}</div>
                                    </div>
                                {/if}

                            </div>
                            <div class="message-body">
                                <span class="email-subject-preview">Message:</span>
                                {$sm.items[i].replies[k].text}
                            </div>

                            {if $sm.items[i].replies[k].hasattachments}
                                <div class="message-attachments">
                                    <div class="attachments">
                                        <span>Attachments:</span>
                                        {section name=j loop=$sm.items[i].replies[k].files}
                                            <a href="{$sm.items[i].replies[k].files[j].url}" title="{$sm.items[i].replies[k].files[j].title}" target="_blank"><i class="fa fa-paperclip"></i> {$sm.items[i].replies[k].files[j].title}</a>
                                        {/section}
                                    </div>
                                </div>
                            {/if}
                        </div>
                    {/section}


                    <div id="reply-{$sm.items[i].id}" class="reply-container m-t-sm"></div>
                    <div class="close-message w-50">
                        <a href="javascript:;" class="reply-to-message flex" id="reply-to-message-{$sm.items[i].id}" onclick="load_editor({$sm.items[i].id}, {$sm.items[i].id_customer})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
                            <span>Reply</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
{/section}
<div id="loadmore">
    <input type="hidden" id="id" value="{$sm.lastitems_id}" />
    <script>{literal}
        function show_hide(val, id_message){
            $.get('index.php?m=conversation&d=setmessageread&id='+id_message, {});
            $('#full-message-' + val).toggle();
            if ($('#full-message-' + val).is(':visible'))
                {
                    $('#show-full-message-' + val ).text('Close');
                    $('#show-full-message-' + val ).parent().parent().addClass('fullwidth');
                }
            else
                {
                    $('#show-full-message-' + val ).text('Open');
                    $('#show-full-message-' + val ).parent().parent().removeClass('fullwidth');
                }
        };
    {/literal}</script>

    <script>
        function load_editor(id_message, id_customer)
        {ldelim}
            $('#reply-' + id_message).load('index.php?m=conversation&d=loademaileditor&theonepage=1&id_message=' + id_message + '&id=' + id_customer);
            {rdelim}
    </script>
</div>

