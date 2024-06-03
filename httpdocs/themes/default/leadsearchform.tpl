{if $data.is_imported neq "1"}
    <div class="lead-search-form" >
        <form id="searchForm" action="{$data.searchURL}">
            <div class="col-md-6">
                <input type="text" name="keywords" class="form-control" placeholder="Enter Keyword" value="{$data.keyword}" />
            </div>
            <div class="col-md-6">
                <input type="text" name="google_place" id="autocomplete" class="form-control" placeholder="Enter Place" value="{$data.google_place}"/>
                <input type="hidden" name="place_geo" id="place_geo" class="form-control" value="{$data.place_geo}" />
                <input type="hidden" name="next_page_token" id="next_page_token" class="form-control" value="{$data.next_page_token}" />
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

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$sm.googlekey}&libraries=places"></script>
    <script type="text/javascript" src="themes/default/leadsearch.js"></script>
{/if}