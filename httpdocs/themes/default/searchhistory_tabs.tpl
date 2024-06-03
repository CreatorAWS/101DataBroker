{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader compaign-tabs-count" style="margin-bottom: 20px;">
    <ul class="nav nav-tabs child-li-tab">
        {if $sm.google_search_available}
            <li {if $sm.g.m eq "searchleads" AND $sm.g.d eq "search_history" OR ($sm.g.m eq "searchleads" AND ($sm.g.d eq "view" OR $sm.g.d eq ""))} class="active"{/if}><a href="index.php?m=searchleads&d=search_history">Google</a></li>
        {/if}
        {if $sm.build_with_installed}
            <li {if $sm.g.m eq "searchtechleads"} class="active"{/if}><a href="index.php?m=searchtechleads">By Technology</a></li>
        {/if}
    </ul>
</div>

<script>{literal}
    function  admintable_msgbox(question, url)
    {
        if (confirm(question+(question.indexOf('?', 0)>=0?'':'?')))
        {
            setTimeout(function() { document.location.href = url; }, 30);
        }
    }
    {/literal}
</script>