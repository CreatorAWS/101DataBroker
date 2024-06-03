{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader compaign-tabs-count" style="margin-bottom: 20px;">
    <ul class="nav nav-tabs child-li-tab">
        {if $sm.google_search_available}
            <li {if $sm.g.m eq "searchleads"} class="active"{/if}><a href="index.php?m=searchleads">Google</a></li>
        {/if}
        {if $sm.build_with_installed}
            <li {if $sm.g.m eq "searchtechleads"} class="active"{/if}><a href="index.php?m=searchtechleads&d=searchleads">By Technology</a></li>
        {/if}
        {if $sm.sic_code_search_available}
            <li {if $sm.g.m eq "searchsiccode" && $sm.g.d neq "states"} class="active"{/if}><a href="index.php?m=searchsiccode">By SIC-Code</a></li>
        {/if}
        {if $sm.states_search_available}
            <li {if $sm.g.m eq "searchsiccode" && $sm.g.d eq "states"} class="active"{/if}><a href="index.php?m=searchsiccode&d=states">By State</a></li>
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