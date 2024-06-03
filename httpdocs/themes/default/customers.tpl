{if $modules[$index].mode eq "log"}
{include file="block_begin.tpl"}

    {section name=i loop=$sm.items}
        <div class="clearfix dash-conversation-divider"></div>
        <div class="rd-dash-conversation-item{if $sm.items[i].unread} dash-conversation-unread{/if}{if $sm.items[i].employee neq ""} rd-dash-conversation-employee{else} rd-dash-conversation-customer{/if}">
            {$sm.items[i].text}
            <div class="rd-dash-conversation-item-footer">
                {if $sm.items[i].employee neq ""}
                    <div class="rd-dash-conversation-item-details"><span>Employee:</span> {$sm.items[i].employee}</div>
                {else}
                    <div class="rd-dash-conversation-item-details"><span>Customer:</span> {$sm.items[i].customer}</div>
                {/if}
                <div class="rd-dash-conversation-time">{$sm.items[i].time}</div>
                <div class="clearfix"></div>
            </div>
        </div>
    {/section}
    <div class="clearfix"></div>

    <div class="row" id="replyform">

        <div style="" class="col-md-12 rd-dash-conversation-answer">
            <form enctype="multipart/form-data" class="adminform_form" method="post" action="{$modules[$index].replyform}">
                <textarea id="dashboard-conversation-text" rows="5" cols="30" class="form-control" name="text"></textarea>

                <div class="adminform_savebutton" style="float:right"><button>Send Message</button></div>
            </form>
        </div>
    </div>
{include file="block_end.tpl"}
{/if}