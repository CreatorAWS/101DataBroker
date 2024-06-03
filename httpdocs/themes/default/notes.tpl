<div class="notes_section">
    {section name=i loop=$sm.items}
        <div class="clearfix timeline">
            <div class="dash-conversation-divider"></div>
            {if not $sm.items[i].hide_time}
                <div class="dash-conversation-timeline {$sm.items[i].time_class} rd-dash-conversation-timeline-customer">
                    <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
                    <div class="rd-dash-conversation-item-details"><span>{if $sm.items[i].employee_label neq ""}{$sm.items[i].employee_label}{else}Employee:{/if}</span> {$sm.items[i].employee}</div>
                </div>
            {/if}
            <div class="rd-dash-conversation-item rd-dash-conversation-customer">
                {$sm.items[i].text}
                <div class="clearfix"></div>
            </div>
        </div>
    {/section}
</div>