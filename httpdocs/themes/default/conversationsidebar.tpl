{section name=i loop=$sm.items}
	<div class="rd-dash-sidebar-item {if $sm.items[i].unread} dash-sidebar-unread{/if}" onclick="dash_show_conversation('{$sm.items[i].id_customer}');" id="dashsideitem-{$sm.items[i].id_customer}">
		<div class="wrp">
			<div class="prospect-icon">
				<div class="icon"><span>{$sm.items[i].initials}</span></div>
				<div class="profile-link"><a href="{$sm.items[i].url}"></a></div>
			</div>
			<div class="prospect-details">
				<div class="rd-dash-sidebar-item-details"><span>{$sm.items[i].primary_title}</span> {$sm.items[i].primary_label}</div>
				{if $sm.items[i].secondary_label neq ""}<div class="rd-dash-sidebar-item-details rd-dash-sidebar-item-details-secondary"><span>{$sm.items[i].secondary_title}:</span> {$sm.items[i].secondary_label}</div>{/if}
			</div>
		</div>
	</div>
{/section}
<div style="display:none;" id="nextsidebaroffsetajax">{$sm.nextoffset}</div>
{if $sm.inbox eq "email" AND $sm.activetab eq "incoming"}
	<div style="display:none;" id="activetab">incoming</div>
{/if}
