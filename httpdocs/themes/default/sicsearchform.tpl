<div class="lead-search-form sic-code-form" >
    <form id="searchForm" action="index.php" method="get">
        <div class="col-md-12">
            <input type="text" name="search_query" id="search_query" class="form-control" placeholder="Enter SIC Code Or Title" value="{$data.search_query}" autocomplete="off"/>
            <input type="hidden" name="m" value="searchsiccode"/>
            <div id="suggesstion-box"></div>
        </div>

        <div class="col-md-2">
            <input type="submit" value="Search" class="searchbutton" id="submitbutton" />
        </div>
    </form>
    <div id="error_message" class="aui-message aui-message-error" style="display: none; width: 100%;"></div>
</div>

<script>{literal}
    $(document).ready(function(){
        $("#search_query").keyup(function(){
            $.ajax({
                type: "POST",
                url: "{/literal}{$data.ajaxSearchURL}{literal}",
                data:'search_query='+$(this).val(),
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


