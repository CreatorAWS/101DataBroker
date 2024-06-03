{assign var=m value=$modules[$index]}

<div class="col-md-12 profilesidebarright" >


    <ul class="nav nav-tabs">
        <li {if $data.currmode eq "messagetemplates"} class="active"{/if}><a href="index.php?m=settings&d=messagetemplates">Text Templates</a></li>
        <li {if $data.currmode eq "emailtemplates"} class="active"{/if}><a href="index.php?m=settings&d=emailtemplates">Email Templates</a></li>
        <li {if $data.currmode eq "sequenceslist"} class="active"{/if}><a href="index.php?m=settings&d=sequenceslist">Default Sequences</a></li>
    </ul>

</div>




<div class="clearfix"></div>
