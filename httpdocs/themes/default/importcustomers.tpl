{assign var=m value=$modules[$index]}

{if $modules[$index].mode eq "list"}
    {include file="block_begin.tpl"}

    <div class="addcustomer last_step">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <h3>Contact List</h3>
        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <label>Title</label>
            <input type="text" class="form-control" name="list_title"  value="{$m.list_title}"/>
            <div class="schedule_buttons">
                <input type="submit" value="Upload" class="pull-right">
            </div>
        </form>
    </div>
    {include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "import"}
    {include file="block_begin.tpl"}

    <div class="addcustomer last_step">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <h3>Upload Your List up to 5000 Maximum </h3>
        <form action="{$m.action_url}" method="post" class="add_contact" enctype="multipart/form-data">
            <label>CSV-file</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
            <input type="file" name="userfile" id="userfile">
            <div class="schedule_buttons">
                <input type="submit" value="Upload" class="pull-right">
            </div>
        </form>
    </div>
    {include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "mapping"}
    {include file="block_begin.tpl"}
    <div class="addcustomer last_step">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <h3>Map Columns</h3>

        <form action="{$m.action_url}" method="post" class="import_contact businessregister" enctype="multipart/form-data">
            <div class="row" style="border-bottom: 1px solid #ccc; padding:0px 0 15px; margin-bottom: 20px;">
                <div class="col-md-6">
                    Data Fields
                </div>
                <div class="col-md-6">
                    Your Uploaded File Columns
                </div>
            </div>
            {section name = j loop = $m.customer_fields}
                <div class="row">
                    <div class="col-md-6">
                        {$m.customer_fields[j].value}
                        {if $m.customer_fields[j].required}<span style="color:red">*</span>{/if}
                    </div>
                    <div class="col-md-6">
                        <select id="import_fields" name="{$m.customer_fields[j].id}">
                            <option value="">-------------</option>
                            {section name = i loop = $m.fields}
                                <option value="{$smarty.section.i.index+1}" {if $m.customer_fields[j].selected eq $smarty.section.i.index+1}SELECTED{/if}>{$m.fields[i]}</option>
                            {/section}
                        </select>
                    </div>
                </div>
                <br/>
            {/section}



            <h4>Select Status For Contacts</h4>
            <div class="row">
                <div class="col-md-6">
                    Status
                </div>
                <div class="col-md-6">
                    <select id="import_fields" name="statusarray">
                        {section name = i loop = $m.statusarray}
                            <option value="{$m.statusarray[i]}" {if $m.statusarray[i] eq $m.statusarray_selected}SELECTED{/if}>{$m.statusarray[i]}</option>
                        {/section}
                    </select>
                </div>
            </div>

            <h4>Create Your Custom Fields</h4>
            {section name = k loop = $m.custom_fields}
                Custom Field #{$m.custom_fields[k].id}
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="custom_field_lable_{$m.custom_fields[k].id}"  value="{$m.custom_fields[k].label}"/>
                        {if $m.customer_fields[j].required}<span style="color:red">*</span>{/if}
                    </div>
                    <div class="col-md-6">
                        <select id="import_fields" name="custom_field_{$smarty.section.k.index}">
                            <option value="">-------------</option>
                            {section name = i loop = $m.fields}
                                <option value="{$smarty.section.i.index+1}" {if $m.custom_fields[k].selected eq $smarty.section.i.index+1}SELECTED{/if}>{$m.fields[i]}</option>
                            {/section}
                        </select>
                    </div>
                </div>
            {/section}

            <h4>Add Tags</h4>
            <div class="row">
                <div class="col-md-6">
                    Tag 1
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="tag_0"  value="{$m.tag_0}"/>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6">
                    Tag 2
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="tag_1"  value="{$m.tag_1}"/>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6">
                    Tag 3
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="tag_2"  value="{$m.tag_2}"/>
                </div>
            </div>

            <div class="schedule_buttons">
                <a href="{$m.back_url}" class="btn">Upload Another File</a>
                <input type="submit" value="Next" class="pull-right">
            </div>
        </form>
    </div>
    {include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "compliancemessage"}
    {include file="block_begin.tpl"}
    <div class="addcustomer last_step">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <h3>Send Compliance Message</h3>

        <form action="{$m.action_url}" method="post" class="import_contact businessregister" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-12" style="margin: 30px 0 50px;">
                    <input type="checkbox" id="compliancemessage" name="compliancemessage" value="1" {if $m.compliancemessage eq 1} checked{/if} class="pull-left"/>
                    <label style="display: table;width: auto;margin: 0 10px;line-height: 19px;" for="compliancemessage" class="pull-left">Send Compliance Message</label>
                </div>
            </div>

            <div class="schedule_buttons">
                <a href="{$m.back_url}" class="btn">Upload Another File</a>
                <input type="submit" value="Import" class="pull-right">
            </div>
        </form>
    </div>
    {include file="block_end.tpl"}
{/if}

{if $modules[$index].mode eq "upload"}
    {include file="block_begin.tpl"}

    <div class="addcustomer width100 last_step">
        {if $m.error_message neq ""}
            <div class="aui-message aui-message-error">{$m.error_message}</div>
        {/if}
        <h3>Select Customers</h3>
        <form action="{$m.action_url}" method="post" class="import_contact businessregister" enctype="multipart/form-data">
            <div class="schedule_buttons">
                <input type="submit" value="Submit">
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <tr>
                        <td><a href="javascript:;" id="checkAll">Select All</td>
                        {section name = i loop = $m.customer_csv.title}
                            <td>{$m.customer_csv.title[i]}</td>
                        {/section}
                    </tr>
                    {section name = i loop = $m.customer_csv.rows}
                        <tr>
                            <td><input type="checkbox" class="customers_row" value="{$smarty.section.i.index}"/></td>
                            {section name = j loop = $m.customer_csv.rows[i].title}
                                <td>{$m.customer_csv.rows[i].title[j]}</td>
                            {/section}
                        </tr>
                    {/section}
                </table>
            </div>
            <div class="schedule_buttons">
                <input type="submit" value="Submit">
            </div>
            <input type="hidden" name="checkedval" id="checkedval" value="" />
        </form>
    </div>
    <script>{literal}

        var checked = 0;
        $("#checkAll").click( function() {
            if (checked === 0)
            {
                $("INPUT[type='checkbox']").prop('checked', true);
                checked = 1
            }
            else
            {
                $("INPUT[type='checkbox']").attr('checked', false);
                checked = 0
            }

        });

        $('form').on('submit', function(event) {
            event.preventDefault();

            var str = $('.customers_row:checked').map(function() {
                return this.value;
            }).get().join();
            $('#checkedval').val(str);
            this.submit(); //now submit the form
        });

        {/literal}</script>
    {include file="block_end.tpl"}
{/if}