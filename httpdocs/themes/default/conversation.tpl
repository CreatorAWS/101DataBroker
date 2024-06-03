{if $modules[$index].mode eq "view"}
    <style>
        {literal}
        .right-column-content{
            padding: 0px!important;
        }
        .block-content-outer{
            padding: 0px!important;
        }
        .rd-dash-sidebar{
            margin:0px;
            color: #3b4354;
            padding: 0 0 20px 0;
        }

        #footer-container{
            display: none;
        }
        {/literal}
    </style>
<script type="text/javascript">
	{$modules[$index].initjs}
</script>
<div class="block" style="display: table; width: 100%; margin-bottom: 0px;">
    <div class="mailbox-tabs">
        <ul>
            <li class="active"><a href="index.php?m=conversation">SMS</a></li>
            <li><a href="index.php?m=conversation&d=incomingemails">Emails</a></li>
        </ul>
    </div>
    <div class="block-content-inner">
        <div class="mailbox">
            <div class="block-content-outer flex" style="align-items: inherit">
                <div class="col-md-3 rd-dash-sidebar">
                    <div class="rd-dash-sidebar-load"><a href="javascript:;" onclick="rd_dash_load_sidebar()">Load more</a></div>
                </div>
                <div class="col-md-9 rd-dash-conversation">
                    <div class="row">
                        <div class="col-md-12 rd-dash-conversation-view">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 rd-dash-conversation-answer-loading" style="display:none;">Loading...</div>
                        <div class="col-md-12 rd-dash-conversation-answer" style="display:none;">
                            <textarea id="dashboard-conversation-text" class="form-control" placeholder="Type message here..."></textarea>
                            <button onclick="dash_send_message()" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-send"></span></button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}


{if $modules[$index].mode eq "incomingemails"}
    <style>
        {literal}
        .right-column-content{
            padding: 0px!important;
        }
        .block-content-outer{
            padding: 0px!important;
        }
        .rd-dash-sidebar{
            margin:0px;
            color: #3b4354;
            padding: 0 0 20px 0;
        }

        #footer-container{
            display: none;
        }
        {/literal}
    </style>
    <script type="text/javascript">
        {$modules[$index].initjs}
    </script>
    <div class="block" style="display: table; width: 100%; margin-bottom: 0px;">
        <div class="block-content-inner">
            <div class="mailbox-tabs">
                <ul>
                    <li><a href="index.php?m=conversation">SMS</a></li>
                    <li class="active"><a href="index.php?m=conversation&d=incomingemails">Emails</a></li>
                </ul>
            </div>
            <div class="mailbox">
                <div class="block-content-outer">
                    <div class="col-md-3 rd-dash-sidebar">
                        {if $sm.inbox eq "email"}
                            <div class="conversations-tabs">
                                <a href="index.php?m=conversation&d=incomingemails" {if $sm.activetab neq "incoming"}class="active"{/if}>All</a>
                                <a href="index.php?m=conversation&d=incomingemails&type=incoming" {if $sm.activetab eq "incoming"}class="active"{/if}>Incoming</a>
                            </div>
                        {/if}

                        <div class="rd-dash-sidebar-load"><a href="javascript:;" onclick="rd_dash_load_sidebar()">Load more</a></div>
                    </div>
                    <div class="col-md-9 rd-dash-conversation">
                        <div class="row">
                            <div class="col-md-12 rd-dash-conversation-view">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}