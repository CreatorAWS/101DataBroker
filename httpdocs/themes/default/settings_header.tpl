{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader">
    <ul class="nav nav-tabs">
        <li {if $data.currmode eq "tags"} class="active"{/if}><a href="index.php?m=tags">Tags</a></li>
{*        <li {if $data.currmode eq "library"} class="active"{/if}><a href="index.php?m=companyassets">Library</a></li>*}
        <li {if $data.currmode eq "compliancemessage"} class="active"{/if}><a href="index.php?m=settings&d=compliancemessage">Compliance Message</a></li>
        <li {if $data.currmode eq "customerfields"} class="active"{/if}><a href="index.php?m=companiesmgmt&d=customerfields">Form Settings</a></li>

    </ul>
</div>
