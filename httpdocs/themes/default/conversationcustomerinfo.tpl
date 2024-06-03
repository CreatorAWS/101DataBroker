{if $sm.currentuser.exist neq ""}
    <div class="wrapper">
        <div id="rd-dash-customer-info">
            <div class="icon"><span>{$sm.currentuser.initials}</span></div>
            <div class="name">{$sm.currentuser.name}</div>
            <div class="button"><a href="{$sm.currentuser.url}">View Profile</a></div>
        </div>
    </div>
{/if}