{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader">
    <ul class="nav nav-tabs">
        <li {if $sm.g.m eq "templates" AND ($sm.g.d eq "messagetemplates" OR $sm.g.d eq "")} class="active"{/if}><a href="index.php?m=templates">Text</a></li>
        <li {if $sm.g.d eq "emailtemplates"} class="active"{/if}><a href="index.php?m=templates&d=emailtemplates">Email</a></li>
        <li {if $data.currmode eq "voice"} class="active"{/if}><a href="index.php?m=companyassets&mode=voice">Voice</a></li>
    </ul>
</div>
