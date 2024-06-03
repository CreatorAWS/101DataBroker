<style>{literal}
        .active_templateselect{
                display: block;
                padding-top: 40px;
        }
        tr.form-group.admintablerow-divider{display: none}
        #admintablerow-text.inactive_sendmessage{display: none}
        #admintablerow-subject.inactive_sendmessage{display: none}
        tr.form-group.inactive_sendmessage{display: none}
        .select_template{font-size: 13px; margin-left: 10px; color: #ff0000}
        .vertical-align-top td:first-child{vertical-align: top; padding-top: 14px;}
        {/literal}</style>


<select name="template_ctgs" id="template_ctgs">
        <option value="-1">-- Select Category --</option>
    {section name=i loop=$data.categories}
        <option value="{$smarty.section.i.index}">{$data.categories[i].title}</option>
    {/section}
</select>
<br/>
<div id="container"></div>

<script>{literal}

        $(document).ready(function(){
                var json = {/literal}{$data.templates}{literal};
                $('#template_ctgs').on('change', function() {

                        $('#container').addClass('active_templateselect');
                        $('#admintablerow-subject').addClass('inactive_sendmessage');
                        $('#admintablerow-divider').addClass('inactive_sendmessage');
                        $('#admintablerow-text').addClass('inactive_sendmessage');
                        $('#admintablerow-ctg_select').addClass('vertical-align-top');


                        if(this.value==='-1')
                                {
                                        $('.nice-select').removeClass('open');
                                        $("#container").html('');
                                        $('#container').removeClass('active_templateselect');
                                        $('#admintablerow-divider').removeClass('inactive_sendmessage');
                                        $('#admintablerow-subject').removeClass('inactive_sendmessage');
                                        $('#admintablerow-text').removeClass('inactive_sendmessage');
                                        $('#admintablerow-ctg_select').removeClass('vertical-align-top');
                                }

                        var newselect = json[this.value];

                        var select = $("<select class='template_category template_category_"+ newselect.ctg_id + "' name='email_template' id='template_"+ newselect.ctg_id + "'></select>");
                        var template_ids = newselect.email_ids;
                        var template_titles = newselect.email_titles;
                        var i;
                        select.append("<option value=''>-- Select Template --</option>");
                        for (i = 0; i < template_ids.length; i++)
                                {
                                        select.append("<option value="+ template_ids[i] +">"+ template_titles[i] +"</option>");
                                }
                        $("#container").html(select);
                        $("select").niceSelect();
                        $('form').submit(function () {

                                var name = $('.template_category_'+ newselect.ctg_id).val();

                                // Check if empty of not
                                if (name  === '' && $('.template_category_'+ newselect.ctg_id).length !== 0) {
                                        $("#container").prepend('<span class="select_template">Select Template</span>');
                                        return false;
                                }
                        });

                });


        });
{/literal}</script>

