{section name=i loop=$sm.items}
    <div class="clearfix timeline" data-number="{$smarty.section.i.index}">
        <div class="dash-conversation-divider"></div>
        <div class="rd-dash-conversation-item-footer allmessage-convo {if $sm.items[i].isincoming}rd-dash-item-customer{else}rd-dash-item-employee{/if}">
            <div class="rd-dash-conversation-item-details"><span>{$sm.items[i].type_title},</span></div>
            {if not $sm.items[i].hide_time}
            <div class="dash-conversation-timeline {$sm.items[i].time_class} {if $sm.items[i].isincoming} rd-dash-conversation-timeline-customer{else} rd-dash-conversation-timeline-employee{/if}">
                <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
            </div>
            {/if}
        </div>
        <div class="rd-dash-conversation-item {if $sm.items[i].isincoming}rd-dash-conversation-customer{else}rd-dash-conversation-employee{/if}">
            {* <div class="rd-dash-conversation-item-footer">
                <div class="rd-dash-conversation-item-details"><span>{$sm.items[i].type_title}</span></div>
            </div> *}
            <div class="clearfix"></div>
            {if $sm.items[i].preview}
                <div class="email-message-preview">
                    <span class="email-subject-preview"></span> {$sm.items[i].subject}
                    {* <a href="javascript:;" class="show-full-message" id="show-full-message-{$smarty.section.i.index}" onclick="show_hide({$smarty.section.i.index})">Open</a> *}
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
                    </div>
                </div>
            {else}
                {$sm.items[i].text}
                {if $sm.items[i].hasattachments}
                    {section name=j loop=$sm.items[i].files}
                        <a href="">{$sm.items[i].files[j].title}</a>
                    {/section}
                {/if}

            {/if}
        </div>
        {* {if not $sm.items[i].hide_time}
            <div class="dash-conversation-timeline {$sm.items[i].time_class} {if $sm.items[i].isincoming} rd-dash-conversation-timeline-customer{else} rd-dash-conversation-timeline-employee{/if}">
                <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
            </div>
        {/if} *}
    </div>
{/section}
<div id="loadmore">
    <input type="hidden" id="id" value="{$sm.lastitems_id}" />

    <script>{literal}
            function show_hide(val)
                {
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
</div>