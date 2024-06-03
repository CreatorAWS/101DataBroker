{if $data.is_imported neq "1"}
    <div class="lead-search-form" >
        <form id="searchForm" action="{$data.searchURL}" method="post" autocomplete="off">
            <div class="col-md-10">
                <input type="text" name="tech" class="form-control" placeholder="Enter Technology" id="search-box" value="{$data.tech}" autocomplete="off" />
                <div id="suggesstion-box"></div>
            </div>

            <div class="col-md-2">
                <input type="submit" value="Search" class="searchbutton" id="submitbutton" />
            </div>
        </form>
        <div id="error_message" class="aui-message aui-message-error" style="display: none; width: 100%;"></div>
        <div class="loader">
            <div class="loader__element"></div>
        </div>
        <div id="loadContent">
            <div id="loadContent"></div>
        </div>
    </div>

    <script type="text/javascript" src="themes/default/techleadsearch.js"></script>
{/if}


<script>{literal}
    $(document).ready(function(){
        $("#search-box").keyup(function(){
            $.ajax({
                type: "POST",
                url: "{/literal}{$data.ajaxSearchURL}{literal}",
                data:'title='+$(this).val(),
                beforeSend: function(){

                },
                success: function(data){
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(data);
                }
            });
        });
    });

    {/literal}</script>