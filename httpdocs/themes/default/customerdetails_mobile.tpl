<div class="block">
    <div class="block-content-outer">
        {include file="common_adminpanel.tpl" panelblocks=$m.uipanel}
        {if $m.mode eq "conversation"}
            <script type="text/javascript">
                {$modules[$index].initjs}
            </script>
            <style>{literal}

                {/literal}</style>
            <div class="col-md-12 rd-dash-conversation">
                <div class="row">
                    <div class="col-md-12 rd-dash-conversation-view">
                    </div>
                </div>
                {if $m.show_conversation_send_message}
                <div class="row">
                    <div class="col-md-12 rd-dash-conversation-answer-loading" style="display:none;">Loading...</div>
                    <div class="col-md-12 rd-dash-conversation-answer" style="display:none;">
                        <textarea id="dashboard-conversation-text" class="form-control" placeholder="Type message here..."></textarea>
                        <button onclick="dash_send_message()" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </div>
                {/if}
            </div>
        {/if}

        {if $m.mode eq "allmessages"}
            <script type="text/javascript">
                {$modules[$index].initjs}
            </script>
            <div class="col-md-12 rd-dash-conversation">
                <div class="row">
                    <div class="col-md-12 rd-dash-conversation-view">
                    </div>
                </div>
            </div>
        {/if}

        {if $m.mode eq "marketingmessages"}
            <script type="text/javascript">
                {$modules[$index].initjs}
            </script>
            <div class="col-md-12 rd-dash-conversation">
                <div class="row">
                    <div class="col-md-12 rd-dash-conversation-view">
                    </div>
                </div>
            </div>
        {/if}

        {if $m.mode eq "notes"}
            <script type="text/javascript">
                {$modules[$index].initjs}
            </script>
            <style>{literal}

                {/literal}</style>
            <div class="col-md-12 rd-dash-conversation">
                <div class="row">
                    <div class="col-md-12 rd-dash-conversation-view">
                    </div>
                </div>
                {if $m.show_conversation_send_message}
                    <div class="row">
                        <div class="col-md-12 rd-dash-conversation-answer-loading" style="display:none;">Loading...</div>
                        <div class="col-md-12 rd-dash-conversation-answer user-notes-form" style="display:none;">
                            <label class="notes-label">Notes</label>
                            <textarea id="dashboard-conversation-text" class="form-control" rows="7" placeholder="Type your note here..."></textarea>
                            <button onclick="dash_send_note()" class="btn btn-primary btn-sm button-notes">Submit</button>
                        </div>
                    </div>
                {/if}
            </div>
        {/if}

        <div style="clear:both;"></div>
    </div>
</div>