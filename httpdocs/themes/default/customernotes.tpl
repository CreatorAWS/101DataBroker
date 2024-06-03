{section name=i loop=$sm.items}
    <div class="clearfix timeline">
        <div class="dash-conversation-divider"></div>
        <div class="rd-dash-conversation-item{if $sm.items[i].unread} dash-conversation-unread{/if}{if $sm.items[i].employee neq ""} rd-dash-conversation-employee{else} rd-dash-conversation-customer{/if}">
            <div class="rd-dash-conversation-item-footer">
                {if $sm.items[i].employee neq ""}
                    <div class="rd-dash-conversation-item-details"><span>{if $sm.items[i].employee_label neq ""}{$sm.items[i].employee_label}{else}Employee:{/if}</span> {$sm.items[i].employee}</div>
                {else}
                    <div class="rd-dash-conversation-item-details"><span>{$sm.label.customer}:</span> {$sm.items[i].customer}</div>
                {/if}
            </div>
            <div class="clearfix"></div>
            {$sm.items[i].text}
        </div>
        {if not $sm.items[i].hide_time}
            <div class="dash-conversation-timeline {$sm.items[i].time_class} {if $sm.items[i].employee neq ""} rd-dash-conversation-timeline-employee{else} rd-dash-conversation-timeline-customer{/if}">
                <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
            </div>
        {/if}
    </div>
{/section}
