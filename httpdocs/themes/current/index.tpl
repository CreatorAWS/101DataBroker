{include file="page_header.tpl"}
{if $userinfo.level neq 0}
    <header>
        <nav class="navbar navbar-default navbar-default" {if $userinfo.level eq 0 }style="position:relative"{/if}>
            <div class="container-fluid flex">
                <div class="navbar-header">
                    {if $special.company_logo neq ""}
                        <a href="{$special.company_website}" class="navbar-brand"><img src="{$special.company_logo}"  border="0" alt="{$_settings.logo_text}"></a>
                    {else}
                        <a href="{if $special.page.scheme neq ""}{$special.page.scheme}{else}http{/if}://{$special.resource_url}" class="navbar-brand"><img src="{$sm.s.current_logo}"  border="0" alt="{$_settings.logo_text}"></a>
                    {/if}
                </div>
                <div class="header-second-row">
                    <!-- Navigation-->
                    <nav>
                        <ul class="nav luna-nav flex-1 w-100">
                            {section name=i loop=$sm.mainmenu}
                                <li {if $sm.mainmenu[i].selected}class=" active"{/if}>
                                    {if $sm.mainmenu[i].url neq ""}
                                        <a href="{$sm.mainmenu[i].url}"{if $sm.mainmenu[i].target neq ""} target="{$sm.mainmenu[i].target}"{/if}>
                                            <div>
                                                {if $sm.mainmenu[i].icon neq ""}
                                                    <span class="main-menu-icon {if $sm.mainmenu[i].icon_class neq ""} fa-fas {$sm.mainmenu[i].icon_class}{/if}">
                                                        {if $sm.mainmenu[i].icon neq ""}
                                                            <img src="{$sm.mainmenu[i].icon}"/>
                                                        {/if}
                                                    </span>
                                                {/if}
                                                <span class="main-menu-text">{$sm.mainmenu[i].title}</span>
                                            </div>
                                            {* <span class="left-menu-right-arrow"><i class="fas fa-angle-right"></i></span> *}
                                        </a>
                                    {else}
                                        <span class="main-menu-label">
                                            {$sm.mainmenu[i].title}
                                        </span>
                                    {/if}
                                    {if $sm.mainmenu[i].has_subitems}
                                        <ul class="secondary-nav active">
                                            {section name=j loop=$sm.mainmenu[i].items}
                                                <li><a href="{$sm.mainmenu[i].items[j].url}">{$sm.mainmenu[i].items[j].title}</a></li>
                                            {/section}
                                        </ul>
                                    {/if}
                                </li>
                            {/section}
                        </ul>
                    </nav>
                    <!-- End navigation-->
                </div>
                <div id="navbar" class="navbar-collapse">
                    <ul class="nav luna-nav w-100">

                        <li class="profil-link">
                            <a href="#" class="top dropdown-toggle adminmenu" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <div>
                                    <span class="main-menu-text">
                                        <svg width="20px" height="20px" viewBox="0 0 15 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g id="icon-user" transform="translate(-3.000000, -3.000000)">
                                                    <rect id="Rectangle" x="0" y="0" width="20" height="20"></rect>
                                                    <g id="user-alt" transform="translate(3.000000, 3.000000)" fill="CurrentColor" fill-rule="nonzero">
                                                        <path d="M7.5,0.9375 C9.31054688,0.9375 10.78125,2.40820312 10.78125,4.21875 C10.78125,6.02929688 9.31054688,7.5 7.5,7.5 C5.68945312,7.5 4.21875,6.02929688 4.21875,4.21875 C4.21875,2.40820312 5.68945312,0.9375 7.5,0.9375 L7.5,0.9375 Z M11.25,10.3125 C12.7998047,10.3125 14.0625,11.5751953 14.0625,13.125 L14.0625,14.0625 L0.9375,14.0625 L0.9375,13.125 C0.9375,11.5751953 2.20019531,10.3125 3.75,10.3125 C6.24023438,10.3125 5.72167969,10.78125 7.5,10.78125 C9.28417969,10.78125 8.75683594,10.3125 11.25,10.3125 L11.25,10.3125 Z M7.5,0 C5.17089844,0 3.28125,1.88964844 3.28125,4.21875 C3.28125,6.54785156 5.17089844,8.4375 7.5,8.4375 C9.82910156,8.4375 11.71875,6.54785156 11.71875,4.21875 C11.71875,1.88964844 9.82910156,0 7.5,0 Z M11.25,9.375 C8.54296875,9.375 9.16992188,9.84375 7.5,9.84375 C5.8359375,9.84375 6.45410156,9.375 3.75,9.375 C1.67871094,9.375 0,11.0537109 0,13.125 L0,14.0625 C0,14.5810547 0.418945312,15 0.9375,15 L14.0625,15 C14.5810547,15 15,14.5810547 15,14.0625 L15,13.125 C15,11.0537109 13.3212891,9.375 11.25,9.375 Z" id="Shape"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                            <ul class="dropdown-menu adminmenu" aria-labelledby="dropdownMenu1">
                                {if $userinfo.level eq 3}
                                    <label>{$userinfo.login}</label>
                                    <div class="dropdown-divider"></div>
                                {/if}
                                {section name=i loop=$sm.accountmenuactions}
                                    <li>
                                        <a href="{$sm.accountmenuactions[i].url}" {if $sm.accountmenuactions[i].selected} class="active"{/if}>
                                            {$sm.accountmenuactions[i].icon}
                                            <span class="main-text">{$sm.accountmenuactions[i].title}</span>
                                        </a>
                                    </li>
                                {/section}
                                <li>
                                    <a href="index.php?m=account&d=logout">
                                        <span aria-hidden="true" class="icon icon-arrow-curve-right"></span>
                                        <span class="main-text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
{/if}
<!-- Wrapper-->
<div class="wrapper flex align-items-start">
    <section class="content" {if $userinfo.level eq 0}style="margin-left: 0px; margin-top:0px;"{/if}>
        <div class="container-fluid">
            {include file="path.tpl"}


            {$special.document.panel[0].beforepanel}
            {assign var=loop_center_panel value=1}
            {assign var=show_center_panel value=1}
            {section name=mod_index loop=$modules step=1 start=1}
                {if $_settings.main_block_position lt $loop_center_panel and $show_center_panel eq 1}
                    {assign var=show_center_panel value=0}
                    {assign var=index value=0}
                    {assign var=mod_name value=$modules[0].module}
                    {$special.document.block[0].beforeblock}
                    {include file="$mod_name.tpl"}
                    {$special.document.block[0].afterblock}
                {/if}
                {if $modules[mod_index].panel eq "center"}
                    {assign var=index value=$smarty.section.mod_index.index}
                    {assign var=mod_name value=$modules[mod_index].module}
                    {$special.document.block[mod_index].beforeblock}
                    {include file="$mod_name.tpl"}
                    {$special.document.block[mod_index].afterblock}
                    {assign var=loop_center_panel value=$loop_center_panel+1}
                {/if}
            {/section}
            {if $show_center_panel eq 1}
                {assign var=show_center_panel value=0}
                {assign var=index value=0}
                {assign var=mod_name value=$modules[0].module}
                {$special.document.block[0].beforeblock}
                {include file="$mod_name.tpl"}
                {$special.document.block[0].afterblock}
            {/if}
            {$special.document.panel[0].afterpanel}


            {$special.document.panel[1].beforepanel}
            {section name=mod_index loop=$modules step=1}
                {if $modules[mod_index].panel eq "1"}
                    {assign var=index value=$smarty.section.mod_index.index}
                    {assign var=mod_name value=$modules[mod_index].module}
                    {$special.document.block[mod_index].beforeblock}
                    {include file="$mod_name.tpl"}
                    {$special.document.block[mod_index].afterblock}
                {/if}
            {/section}
            {$special.document.panel[1].afterpanel}


            {$special.document.panel[2].beforepanel}
            {section name=mod_index loop=$modules step=1}
                {if $modules[mod_index].panel eq "2"}
                    {assign var=index value=$smarty.section.mod_index.index}
                    {assign var=mod_name value=$modules[mod_index].module}
                    {$special.document.block[mod_index].beforeblock}
                    {include file="$mod_name.tpl"}
                    {$special.document.block[mod_index].afterblock}
                {/if}
            {/section}
            {$special.document.panel[2].afterpanel}

        </div>
    </section>
    <!-- End main content-->

</div>
<!-- End wrapper-->

<div id="callsmodal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="messagemodal_content">
                <div class="incoming_call">
                    <div id="caller_info_name"></div>
                    <div id="caller_info"></div>
                    <div class="buttons">
                        <div class="answer">
                            <a href="javascript:;" id="answer_call" onclick="answer_the_call()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </a>
                        </div>
                        <div class="decline">
                            <a href="javascript:;" id="button-decline" onclick="decline_the_call()"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></a>
                        </div>
                    </div>
                    <div id="notes_section"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vendor scripts -->

<script src="themes/{$special.theme}/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="themes/{$special.theme}/vendor/toastr/toastr.min.js"></script>
<script src="themes/{$special.theme}/vendor/sparkline/index.js"></script>
<script src="themes/{$special.theme}/vendor/flot/jquery.flot.min.js"></script>
<script src="themes/{$special.theme}/vendor/flot/jquery.flot.resize.min.js"></script>
<script src="themes/{$special.theme}/vendor/flot/jquery.flot.spline.js"></script>
<script type="text/javascript" src="themes/{$special.theme}/assets/js/required/jquery-ui-1.11.0.custom/jquery-ui.min.js"></script>

<script src="themes/{$special.theme}/vendor/niceselect/jquery.nice-select.js"></script>

<script>{literal}
    $(document).ready(function() {
        $('select').niceSelect();
    });
    {/literal}
</script>


<!-- App scripts -->
<script src="themes/{$special.theme}/scripts/luna.js"></script>
<script src="themes/{$special.theme}/scripts/selectize.js"></script>
<script src="themes/default/dragula.js"></script>
<script src="themes/default/leaddraganddrop.js"></script>

<script>


    $(document).ready(function() {ldelim}
        $('#tag_filter').niceSelect('destroy');
        $('#js-tags-selector').niceSelect('destroy');
        $('#js-tags-selector').selectize({ldelim}
            plugins: ['remove_button'],
            create: true
        {rdelim});
    {rdelim});

    $( ".insert_postcard_front" )
        .change(function () {ldelim}
            var str = "";
            $( ".insert_postcard_front option:selected" ).each(function() {ldelim}
                str += '[img src="' + $( this ).val() + '" /]';
            {rdelim});
            $( "#postcard_front" ).append( str );
        {rdelim});

    $( "#insertassets_post_back" )
        .change(function () {ldelim}
            var str = "";
            $( "#insertassets_post_back option:selected" ).each(function() {ldelim}
                str += '[img src="' + $( this ).val() + '" /]';
            {rdelim});
            $( "#postcard_back" ).append( str );
        {rdelim});

    $( "#insertassets_letter" )
        .change(function () {ldelim}
            var str = "";
            $( "#insertassets_letter option:selected" ).each(function() {ldelim}
                str += '[img src="' + $( this ).val() + '" /]';
            {rdelim});
            $( "#message" ).append( str );
        {rdelim});


</script>


<script>
    {literal}

    $(document).ready(function() {
        $("#search-popup-button").click(function(){
            $("#search-form-popup").addClass('visible');
            $("#search-form-popup").show();
            $('#search-form-popup .form-control').focus();
        });

        $("#search-form-popup .navbar-form").blur(function () {
            $("#search-form-popup").hide();
        });

        $('#insertassets').niceSelect('destroy');
    });

    $( "#insertassets" )
        .change(function () {
            var str = "";
            var activeEditor = tinyMCE.activeEditor.getContent();
            $( "#insertassets option:selected" ).each(function() {
                str += '<img src="' + $( this ).val() + '" />';
            });
            if(tinyMCE.activeEditor!==null){
                tinyMCE.activeEditor.setContent(str + activeEditor);
            } else {
                $('#message').val(str);
            }
        });

    {/literal}
</script>


{include file="page_footer.tpl"}
