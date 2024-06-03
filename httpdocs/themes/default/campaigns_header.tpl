{assign var=m value=$modules[$index]}

<div class="profilesidebarright campaignsheader compaign-tabs-count">
    <ul class="nav nav-tabs child-li-tab">
        <li {if $data.currmode eq "list"} class="active"{/if}><a href="index.php?m=campaigns&d=list">All Campaigns</a></li>
        <li {if $data.currmode eq "sent"} class="active"{/if}><a href="index.php?m=campaigns&d=list&currmode=sent">Sent</a></li>
        <li {if $data.currmode eq "draft"} class="active"{/if}><a href="index.php?m=campaigns&d=list&currmode=draft">Drafts</a></li>
        <li {if $data.currmode eq "scheduled"} class="active"{/if}><a href="index.php?m=campaigns&d=list&currmode=scheduled">Scheduled</a></li>
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