{assign var=m value=$modules[$index]}
{literal}<style>#block0title{display:none;}</style>{/literal}


{if $m.mode eq "campaigntitle"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>

    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <form action="{$m.action_url}" method="post" class="campaign_wizard" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <label for="optin_title">Title<sup class="adminform-required">*</sup></label>
                    <input class="form-control" type="text" name="campaign_title" value="{$m.campaign_title}" id="campaign_title" autofocus>
                    <div class="pull-left">
                        <input type="submit" value="Next" class="firststepbutton">
                    </div>
                </div>
            </div>
        </form>

    </div>
    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "contacts"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>

    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <h2>{$m.page_title}</h2>

        <div class="schedule_buttons">
            <a href="{$m.addcontacts_url}" class="pull-left startnow">Upload Your List</a>
            <a href="{$m.addtags_url}" class="pull-left startnow schedule">{$m.choose_customers_label}</a>
            <a href="{$m.selectontactlist_url}" class="pull-left startnow schedule">Select Contacts List</a>
        </div>
        <div class="clearfix"></div>
        <div class="schedule_buttons bottom_buttons">
            <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
            <a href="{$m.next_url}" class="startnow pull-right">Next</a>
        </div>

    </div>
    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "selectype"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <label>Select Sequence Type</label>
                    <select name="selectype">
                            <option value="">---------</option>
                            <option value="single_user" {if $m.selected_type eq 'single_user'}SELECTED{/if}>Single User</option>
                            <option value="multiple_users" {if $m.selected_type eq 'multiple_users'}SELECTED{/if}>Multiple Users</option>
                    </select>
                </div>
            </div>
            <div class="schedule_buttons bottom_buttons">
                <input type="submit" value="Next" class="pull-right">
            </div>
        </form>

    </div>

    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "add_tags"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <h2>{$m.page_title}</h2>

        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <label>Select Tags</label>
                    <select id="js-tags-selector" name="tags_selected[]" multiple>
                        {section name=i loop=$m.tags}
                            <option value="{$m.tags[i].value}" {if $m.tags[i].checked eq 1}SELECTED{/if}>{$m.tags[i].title}</option>
                        {/section}
                    </select>
                </div>
            </div>
            <div class="schedule_buttons bottom_buttons">
                <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                <input type="submit" value="Next" class="pull-right">
            </div>
        </form>

    </div>

    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "import"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <label>CSV-file</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
            <input type="file" name="userfile" id="userfile">

            <div class="row">
                <div class="col-md-12 inlinelabel">
                    <input type="checkbox" name="updatecontacts" id="updatecontacts" value="1" {if $m.updatecontacts eq "1"}checked{/if} />
                    <label class="pull-left inlinelabel" for="updatecontacts">Update the existing matching contacts</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label>Add Tags</label>
                    <select id="js-tags-selector" name="new_tags[]" multiple>
                        {section name=i loop=$m.tags}
                            <option value="{$m.tags[i].value}" {if $m.tags[i].checked eq 1}SELECTED{/if}>{$m.tags[i].title}</option>
                        {/section}
                    </select>
                </div>
            </div>

            <div class="schedule_buttons bottom_buttons" style="padding-top: 0">
                <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                <input type="submit" value="Next" class="pull-right">
            </div>
        </form>
        <div class="aui-message aui-message-info finish-step">
            <span>Your CSV file must contain the columns in next order: First Name, Last Name, Email, Phone(optional), Tag1, Tag2, Tag3...</span>
            <a href="index.php?m=campaignwizard&d=downloadcsv" class="pull-right csv-button">Download Example</a>
        </div>
    </div>

    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "sequence"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer emails_choose">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="email_row">
                        <div class="numbers"><span>1</span></div>
                        <div class="email_container">
                            <label>Email 1 (instant)</label>



                            <select name="template_ctgs" id="template_ctgs">
                                <option value="-1">-- Select Category --</option>
                                {section name=i loop=$m.emailtemplates.categories}
                                    <option value="{$smarty.section.i.index}" {if $m.emailtemplates.categories_selected eq $m.emailtemplates.categories[i].id} selected {/if}>{$m.emailtemplates.categories[i].title}</option>
                                {/section}
                            </select>

                            <input type="hidden" id="email1_template_ctgs_vars" value="{$m.emailtemplates.templates}">
                            <div class="select_container" id="email1_templates">
                                {if $m.emailtemplates.categories_selected neq ""}
                                    <select name="email1_template"  class="form-control">
                                        {section name=i loop=$m.emailtemplates.messagetemplates}
                                            <option{if $m.emailtemplates.messagetemplates[i].selected} selected{/if} value="{$m.emailtemplates.messagetemplates[i].id}">{$m.emailtemplates.messagetemplates[i].title}</option>
                                        {/section}
                                    </select>
                                {/if}
                            </div>

                        </div>
                    </div>

                    {section name=j loop=$m.sequenceitems}
                        <div class="email_row" id="message_row_{$m.sequenceitems[j].id}">
                            <div class="numbers"><span>{$smarty.section.j.index+2}</span></div>
                            <div class="email_container">
                                <div class="removesequence"><a href="javascript:;" onclick="{$m.sequenceitems[j].remove_sequence_step}">Remove</a></div>
                                <label class="capitalize">{$m.sequenceitems[j].mode}</label>

                                {if $m.sequenceitems[j].mode eq "email" OR $m.sequenceitems[j].mode eq "sms"}
                                    <select name="template_ctgs_{$m.sequenceitems[j].id}" class="template_ctgs">
                                        <option value="-1">-- Select Category --</option>
                                        {section name=i loop=$m.sequenceitems[j].categories}
                                            <option value="{$smarty.section.i.index}" {if $m.sequenceitems[j].categories_selected eq $m.sequenceitems[j].categories[i].id} selected {/if}>{$m.sequenceitems[j].categories[i].title}</option>
                                        {/section}
                                    </select>
                                    <input type="hidden" id="template_ctgs_{$m.sequenceitems[j].id}_vars" value="{$m.sequenceitems[j].templates}">
                                    <div class="select_container" id="template_ctgs_{$m.sequenceitems[j].id}">
                                        {if $m.sequenceitems[j].categories_selected neq ""}
                                            <select id="message_template_{$m.sequenceitems[j].id}" name="message_template_{$m.sequenceitems[j].id}"  class="form-control">
                                                {section name=i loop=$m.sequenceitems[j].messagetemplates}
                                                    <option{if $m.sequenceitems[j].messagetemplates[i].selected} selected{/if} value="{$m.sequenceitems[j].messagetemplates[i].id}">{$m.sequenceitems[j].messagetemplates[i].title}</option>
                                                {/section}
                                            </select>
                                        {/if}
                                    </div>
                                {else}
                                    <select id="message_template_{$m.sequenceitems[j].id}" name="message_template_{$m.sequenceitems[j].id}"  class="form-control">
                                        {section name=i loop=$m.sequenceitems[j].messagetemplates}
                                            <option{if $m.sequenceitems[j].messagetemplates[i].selected} selected{/if} value="{$m.sequenceitems[j].messagetemplates[i].id}">{$m.sequenceitems[j].messagetemplates[i].title}</option>
                                        {/section}
                                    </select>
                                {/if}
                                <div style="display: table; width:100%;"></div>

                                <div class="dayselect message-wrapper">
                                    <label class="send_message_label">Send message</label>
                                    <div class="hrs-mns-wrapper">
                                        <select id="message_time_{$m.sequenceitems[j].id}" name="message_time_{$m.sequenceitems[j].id}" class="minutes_select form-control">
                                            {section name=k loop=$m.sequenceitems[j].days}
                                                <option{if $smarty.section.k.index eq $m.sequenceitems[j].day_selected} selected{/if} value="{$m.sequenceitems[j].days[k].id}">{$m.sequenceitems[j].days[k].id}</option>
                                            {/section}
                                        </select>
                                        <label>day(s)</label>
                                        <select id="message_time_hrs_{$m.sequenceitems[j].id}" name="message_time_hrs_{$m.sequenceitems[j].id}" class="hrs-mns form-control">
                                            {section name=k loop=$m.sequenceitems[j].hr }
                                                <option{if $m.sequenceitems[j].hr[k] eq $m.sequenceitems[j].hrs_selected} selected{/if} value="{$m.sequenceitems[j].hr[k]}">{$m.sequenceitems[j].hr[k]}</option>
                                            {/section}
                                        </select>
                                        <label>hr(s)</label>
                                        <select id="message_time_min_{$m.sequenceitems[j].id}" name="message_time_min_{$m.sequenceitems[j].id}" class="hrs-mns form-control">
                                            {section name=k loop=$m.sequenceitems[j].min.val }
                                                <option{if $m.sequenceitems[j].min.val[k] eq $m.sequenceitems[j].min_selected} selected{/if} value="{$m.sequenceitems[j].min.val[k]}">{$m.sequenceitems[j].min.titles[k]}</option>
                                            {/section}
                                        </select>
                                        <label>min(s)</label>
                                    </div>
                                    <label class="send_message_label2"> after previous message</label>
                                </div>

                            </div>
                        </div>

                    {/section}
                    <div class="add_sequence_item sequence-loader">
                        <div style="{if $m.sequencelist_count lt $m.campaign_duration-1}display: table; margin: auto{else}display:none{/if}">
                            <a href="javascript:;" onclick="{$m.load_more_code_email}" class="startnow" style="float:left; margin-right:10px;">Add Email Message</a>
                            <a href="javascript:;" onclick="{$m.load_more_code_sms}" class="startnow"  style="float:left; margin-right:10px;">Add SMS Message</a>
                            {if $m.load_more_code_voice neq ""}
                                <a href="javascript:;" onclick="{$m.load_more_code_voice}" class="startnow">Add Voice Message</a>
                            {/if}
                        </div>
                    </div>

                    <div class="schedule_buttons bottom_buttons" style="padding-top: 0">
                        <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                        <input type="submit" value="Next" class="pull-right">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>{literal}

        $(document).ready(function(){


            $('#template_ctgs').on('change', function() {

                var json = JSON.parse(decodeURIComponent($('#email1_template_ctgs_vars').val()));

                $('#email1_templates' ).addClass('active_templateselect');

                if(this.value==='-1')
                {
                    $('.nice-select').removeClass('open');
                    $("#email1_templates").html('');
                    $('#email1_templates').removeClass('active_templateselect');
                }

                var newselect = json[this.value];

                var select = $("<select class='template_category template_category_"+ newselect.ctg_id + "' name='email1_template' id='email1_template'></select>");
                var template_ids = newselect.message_ids;
                var template_titles = newselect.message_titles;
                var i;
                select.append("<option value=''>-- Select Template --</option>");
                for (i = 0; i < template_ids.length; i++)
                {
                    select.append("<option value="+ template_ids[i] +">"+ template_titles[i] +"</option>");
                }
                $("#email1_templates").html(select);
                $("select").niceSelect();
            });


            $('.template_ctgs').each(function() {

                $(this).on('change', function() {
                    var selector = $(this).attr('name');
                    var json = JSON.parse(decodeURIComponent($('#' + selector + '_vars').val()));

                    $('#' + selector ).addClass('active_templateselect');

                    if(this.value==='-1')
                        {
                            $('.nice-select').removeClass('open');
                            $('#' + selector).html('');
                            $('#' + selector).removeClass('active_templateselect');
                        }

                    var newselect = json[this.value];
                    var select = $("<select class='form-control template_category_"+ newselect.ctg_id + "' name='message_template_"+ selector.replace('template_ctgs_', '') +"' id='template_"+ newselect.ctg_id + "'></select>");
                    var template_ids = newselect.message_ids;
                    var template_titles = newselect.message_titles;
                    var i;
                    select.append("<option value=''>-- Select Template --</option>");
                    for (i = 0; i < template_ids.length; i++)
                        {
                            select.append("<option value="+ template_ids[i] +">"+ template_titles[i] +"</option>");
                        }
                    $('#' + selector).html(select);
                    $("select").niceSelect();
                });
            });
        });
        {/literal}</script>

    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "selectsequence"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer emails_choose">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12 col-xs-12">

                    <label class="capitalize">Select Sequence</label>
                    <select id="campaign_id" name="campaign_id">
                        <option value="0" {if $m.campaigns.selected eq 0} selected{/if}>------ Select Sequence -------</option>
                        {section name = i loop = $m.campaigns.ids}
                            <option{if $m.campaigns.selected eq $m.campaigns.ids[i]} selected{/if} value="{$m.campaigns.ids[i]}">{$m.campaigns.titles[i]}</option>
                        {/section}
                    </select>
                </div>
            </div>
            <div class="schedule_buttons bottom_buttons" style="padding-top: 0">
                <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                <input type="submit" value="Next" class="pull-right">
            </div>
        </form>
    </div>

    {include file="block_end.tpl"}
{/if}

{if $m.mode eq "addsequence"}
    <div class="email_row" id="message_row_{$m.message_template_id}">
        <div class="numbers"><span>{$m.sequence_count}</span></div>
        <div class="email_container">
            <div class="removesequence"><a href="javascript:;" onclick="{$m.remove_sequence_step}">Remove</a></div>
            <label class="capitalize">{$m.sequence_mode}</label>
            {if $m.sequence_mode eq "email" OR $m.sequence_mode eq "sms"}
                <select name="template_ctgs_{$m.message_template_id}" class="template_ctgs">
                    <option value="-1">-- Select Category --</option>
                    {section name=i loop=$m.sequenceitems.categories}
                        <option value="{$smarty.section.i.index}" {if $m.sequenceitems.categories_selected eq $m.sequenceitems.categories[i].id} selected {/if}>{$m.sequenceitems.categories[i].title}</option>
                    {/section}
                </select>
                <input type="hidden" id="template_ctgs_{$m.message_template_id}_vars" value="{$m.sequenceitems.templates}">
                <div class="select_container" id="template_ctgs_{$m.message_template_id}"></div>
            {else}
                <select id="message_template_{$m.message_template_id}" name="message_template_{$m.message_template_id}"  class="form-control">
                    {section name=i loop=$m.message_template}
                        <option{if $m.message_template[i].selected} selected{/if} value="{$m.message_template[i].id}">{$m.message_template[i].title}</option>
                    {/section}
                </select>
            {/if}

            <div style="display: table; width:100%;"></div>
            <div class="dayselect">
                <label>Send message</label>
                <select id="message_time_{$m.message_template_id}" name="message_time_{$m.message_template_id}">
                    {section name=i loop=$m.sequenceitems_days start="1"}
                        <option{if $m.sequenceitems_days[i].selected} selected{/if} value="{$m.sequenceitems_days[i]}">{$m.sequenceitems_days[i]}</option>
                    {/section}
                </select>
                <label>day(s) after instant message</label>
            </div>
        </div>
    </div>
    <div class="add_sequence_item sequence-loader">
        <div style="{if $m.sequence_count lt $m.campaign_duration}display: table; margin: auto{else}display:none{/if}">
            <a href="javascript:;" onclick="{$m.load_more_code_email}" class="startnow" style="float:left; margin-right:10px;">Add Email Message</a>
            <a href="javascript:;" onclick="{$m.load_more_code_sms}" class="startnow" style="float:left; margin-right:10px;">Add SMS Message</a>
            {if $m.load_more_code_voice neq ""}
                <a href="javascript:;" onclick="{$m.load_more_code_voice}" class="startnow" style="float:left; margin-right:10px;">Add Voice Message</a>
            {/if}
        </div>
    </div>
    <script>{literal}
        $(document).ready(function() {
            $('select').niceSelect();

            $('.template_ctgs').each(function() {

                $(this).on('change', function() {
                    var selector = $(this).attr('name');
                    var json = JSON.parse(decodeURIComponent($('#' + selector + '_vars').val()));

                    $('#' + selector ).addClass('active_templateselect');

                    if(this.value==='-1')
                    {
                        $('.nice-select').removeClass('open');
                        $('#' + selector).html('');
                        $('#' + selector).removeClass('active_templateselect');
                    }

                    var newselect = json[this.value];
                    var select = $("<select class='form-control template_category_"+ newselect.ctg_id + "' name='message_template_"+ selector.replace('template_ctgs_', '') +"' id='template_"+ newselect.ctg_id + "'></select>");
                    var template_ids = newselect.message_ids;
                    var template_titles = newselect.message_titles;
                    var i;
                    select.append("<option value=''>-- Select Template --</option>");
                    for (i = 0; i < template_ids.length; i++)
                    {
                        select.append("<option value="+ template_ids[i] +">"+ template_titles[i] +"</option>");
                    }
                    $('#' + selector).html(select);
                    $("select").niceSelect();
                });
            });
        });

        {/literal}
    </script>
{/if}


{if $m.mode eq "schedule"}
    {include file="block_begin.tpl"}
    <div class="stepssection">
        <h2>{$m.page_title}</h2>
        {include file="pageswizard.tpl"}
    </div>


    <div class="addcustomer">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}

        <div class="schedule_buttons">
            <a href="{$m.next_url}" class="pull-left startnow">Start Now</a>
            <a href="{$m.schedule_url}" class="pull-left startnow schedule">Schedule</a>
        </div>

    </div>

    {include file="block_end.tpl"}
{/if}