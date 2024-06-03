{section name=i loop=$sm.items}
    <div class="clearfix timeline">
        <div class="dash-conversation-divider"></div>
        {if not $sm.items[i].hide_time}
            <div class="rd-dash-conversation-item-footer allmessage-convo rd-dash-item-customer">
            <div class="dash-conversation-timeline {$sm.items[i].time_class}">
                <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
            </div>
            </div>
        {/if}
        <div class="rd-dash-conversation-item rd-dash-conversation-customer">
            <div class="rd-dash-conversation-item-footer">
                <div class="rd-dash-conversation-item-details"><span>{$sm.items[i].type_title}</span></div>
            </div>
            <div class="clearfix"></div>
            {$sm.items[i].text}
        </div>
    </div>
{/section}