{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader">
    <ul class="nav nav-tabs">
        <li {if $data.currmode eq "inbox"} class="active"{/if}><a href="index.php?m=messages&d=inbox">Incoming</a></li>
        <li {if $data.currmode eq "list"} class="active"{/if}><a href="index.php?m=messages&d=list">Outgoing</a></li>
        <li {if $data.currmode eq "customers"} class="active"{/if}><a href="index.php?m=customers&d=listview&status=noresponse">No Response</a></li>
    </ul>
</div>
