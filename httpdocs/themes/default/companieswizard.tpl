{assign var=m value=$modules[$index]}
{literal}<style>#block0title{display:none;}</style>{/literal}


<div class="companywizard">

    {if $m.mode eq "businessinfo"}
        {include file="block_begin.tpl"}
        <div class="stepssection">
            <h2>Company Wizard</h2>
            {include file="wizardsteps.tpl"}
        </div>

        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="company_name">Company Name<sup class="adminform-required">*</sup></label>
                        <input class="form-control" type="text" name="company_name" value="{$m.businessinfo.company_name}" id="company_name" autofocus>
                    </div>
{*
                    <div class="col-md-12 col-xs-12">
                        <label for="address">Email</label>
                        <input class="form-control" type="text" name="email" value="{$m.businessinfo.company_email}" id="email"/>
                    </div>
*}
                    <div class="col-md-12 col-xs-12">
                        <label for="address">Address</label>
                        <input class="form-control" type="text" name="address" value="{$m.businessinfo.address}" id="address"/>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <label for="address2">Address2</label>
                        <input class="form-control" type="text" name="address2" value="{$m.businessinfo.address2}" id="address2"/>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <label for="city">City</label>
                        <input class="form-control" type="text" name="city" value="{$m.businessinfo.city}" id="city"/>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <label for="state">State</label>

                        <select class="form-control" id="state" name="state" value="{$m.state}">
                            <option value="">----------</option>
                            {foreach from=$m.states_list.ab key=k item=v}
                                <option value="{$v}"{if $v eq $m.businessinfo.state OR $m.states_list.nm[$k] eq $m.businessinfo.state} selected {/if}>{$m.states_list.nm[$k]}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <label for="zip">ZIP</label>
                        <input class="form-control" type="text" name="zip" value="{$m.businessinfo.zip}" id="zip"/>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <input type="submit" value="Next">
                </div>

            </form>

        </div>
        {include file="block_end.tpl"}
    {/if}


    {if $m.mode eq "businesssettings"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Business Settings</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Send system notifications to cellphone </label>
                        <input class="form-control" type="text" name="cellphone_for_notifications" value="{$m.businesssettings.cellphone_for_notifications}" id="cellphone_for_notifications" placeholder="1 (123) 456-7890"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint2}
                    </div>
                </div>

{*
                <br/>
                <h5>Business Hours</h5>
                {section name=i loop = $m.businesssettings.days}
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <label for="business_hrs_{$smarty.section.i.index}" class="day_label">{$m.businesssettings.days[i].day}</label>
                            <input type="text" name="business_hrs_{$smarty.section.i.index}" id="business_hrs_{$smarty.section.i.index}" value="{$m.businesssettings.days[i].val}" class="form-control"  />
                        </div>
                    </div>
                {/section}
*}
                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "twiliophone"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Twilio Settings</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Twilio Phone Number </label>
                        <input class="form-control" type="text" name="twilio_phone" value="{$m.businesssettings.twilio_phone}" id="twilio_phone" placeholder="1 (123) 456-7890"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint}
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "twilioareacode"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Twilio Settings</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Phone Number Area Code</label>
                        <input class="form-control" type="text" name="twilio_phone_area" value="{$m.businesssettings.twilio_phone_area}" id="twilio_phone_area" />
                    </div>
                    <div class="hint">
                        <div class="title_hint">
                            {$m.titlehint}:
                        </div>
                        <div class="text_hint">
                            {$m.hint}
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "gettwilionumberlist"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Select Phone Number</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Phone Numbers Available</label>
                        {section name=i loop = $m.businesssettings}
                            {if $m.businesssettings[i].twilio_phones neq ""}
                                <div class="row">
                                    <input type="radio" name="twilio_phone_number" value="{$m.businesssettings[i].twilio_phones}" /> {$m.businesssettings[i].twilio_phones}
                                </div>
                                <br/>
                            {/if}
                        {/section}
                    </div>
                </div>
                <input type="hidden" name="twilio_phone_area" value="{$m.twilio_phone_area}" />
                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "twiliosettings"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Business Settings</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Twilio Account Sid </label>
                        <input class="form-control" type="text" name="twilio_AccountSid" value="{$m.accountinfo.twilio_AccountSid}" id="twilio_AccountSid"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Twilio Auth Token</label>
                        <input class="form-control" type="text" name="twilio_AuthToken" value="{$m.accountinfo.twilio_AuthToken}" id="twilio_AuthToken"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>MailJet API Key</label>
                        <input class="form-control" type="text" name="mailjet_api_key" value="{$m.accountinfo.mailjet_api_key}" id="mailjet_api_key"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>MailJet API Secret</label>
                        <input class="form-control" type="text" name="mailjet_api_secret" value="{$m.accountinfo.mailjet_api_secret}" id="mailjet_api_secret"/>
                    </div>
                </div>
                <div class="hint">
                    <div class="title_hint">
                        {$m.titlehint}:
                    </div>
                    <div class="text_hint">
                        {$m.hint}
                    </div>
                </div>

{*
                <br/>
                <h5>Business Hours</h5>
                {section name=i loop = $m.businesssettings.days}
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <label for="business_hrs_{$smarty.section.i.index}" class="day_label">{$m.businesssettings.days[i].day}</label>
                            <input type="text" name="business_hrs_{$smarty.section.i.index}" id="business_hrs_{$smarty.section.i.index}" value="{$m.businesssettings.days[i].val}" class="form-control"  />
                        </div>
                    </div>
                {/section}
*}
                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}


    {if $m.mode eq "usercontact"}
        {include file="block_begin.tpl"}
        <div class="stepssection">
            <h2>User Contact Information</h2>
            {include file="wizardsteps.tpl"}
        </div>

        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="first_name">First Name*</label>
                        <input class="form-control" type="text" name="first_name" value="{$m.first_name}" id="first_name"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="last_name">Last Name*</label>
                        <input class="form-control" type="text" name="last_name" value="{$m.last_name}" id="last_name"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="email">Email*</label>
                        <input class="form-control" type="text" name="email" value="{$m.email}" id="email"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="password">Change your password<sup class="adminform-required">*</sup></label>
                        <input class="form-control" type="text" name="password" value="{$m.password}" id="password"/>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <input type="submit" value="Next" class="pull-right">
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}



    {if $m.mode eq "companylogo"}
        {include file="block_begin.tpl"}
        <div class="stepssection">
            <h2>Company Logo</h2>
            {include file="wizardsteps.tpl"}
        </div>

        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label>Upload your logo {$m.upload_label}</label>
                        <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="{$_settings.max_upload_filesize}">
                        <input type="file" name="userfile" id="userfile">
                        <div class="hint" style="margin-top: 10px;">
                            <div class="title_hint">
                               Hint:
                            </div>
                            <div class="text_hint">
                                Recommended size is 300x66 or smaller
                            </div>
                        </div>
                    </div>
                </div>
                {if $m.logo_image neq ""}
                    <div class="row logorow">
                        <div class="label"><img src="{$m.logo_image}" style="max-width: 200px; max-height: 200px; margin-top:10px;"/></div>
                        <a href="{$m.remove_image_url}">Remove logo</a>
                    </div>
                {/if}
                <div class="row logopreview">
                    <div class="label">
                        {*<img src="" id="logopreview" style="display:none; max-width: 200px; max-height: 200px; margin-top:10px;"/>*}
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        <script>
            {literal}
            function uploadCallbackSuccess(_file){
                var d = new Date();
                var src = _file + '?time='+d.getTime();

                $('.logorow').css('display', 'none');
                $('#logopreview').hide();
                $('#logopreview').fadeIn(0);
                $('.logopreview .label').html("<img src='"+src+"' style='max-width: 200px; max-height: 200px; margin-top:10px;'>");

            }

            function uploadCallbackValidationError(errors){
                alert('FILE UPLOADING ERROR\n'+errors.join('\n'));
            }

            function uploadCallbackError(text){
                alert(text);
            }

            function processUpload(){
                var fileInput = document.getElementById('userfile');
                var file = fileInput.files[0];
                var formData = new FormData();
                formData.append('userfile', file);

                $.ajax({
                    url : '{/literal}{$m.upload_url}{literal}',
                    type : 'POST',
                    data : formData,
                    processData: false,
                    contentType: false,
                    success : function(_response) {

                        try {
                            var _responseJson = JSON.parse(_response);

                            if (_responseJson.errors) {
                                uploadCallbackValidationError(_responseJson.errors);
                            } else if (_responseJson.file) {
                                uploadCallbackSuccess(_responseJson.file)
                            } else {
                                uploadCallbackError('Error occurred while uploading');
                            }
                        } catch{
                            uploadCallbackError('Error occurred while uploading');
                        }
                    },

                    error: function (request, status, error) {
                        uploadCallbackError(request.responseText);
                    }
                });

            }

            $("#userfile").change(function() {
                processUpload();
            });

            {/literal}
        </script>
        {include file="block_end.tpl"}
    {/if}


    {if $m.mode eq "phonenumbers"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Phone Numbers</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                {if $m.twilio_phone_set neq 1}
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <label>Twilio Phone Area Code</label>
                            <input class="form-control" type="text" name="twilio_phone_area" value="{$m.twilio_phone_area}" id="twilio_phone_area" autofocus />
                        </div>
                    </div>
                    <div class="hint">
                        <div class="title_hint">
                            {$m.titlehint}:
                        </div>
                        <div class="text_hint">
                            {$m.hint}
                        </div>
                    </div>
                    <br/>
                {/if}


                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
{*                        <a class="skip" href="{$m.skip_url}">Skip</a>*}
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "domains"}
        {include file="block_begin.tpl"}
        <div class="stepssection secondsection">
            <div class="stepssection">
                <h2>Domains/Subdomains</h2>
                {include file="wizardsteps.tpl"}
            </div>
        </div>

        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="webapp_subdomain">Your dashboard login URL ({$m.webappdomain_label} subdomain)<sup class="adminform-required">*</sup></label>
                        <div style="display: flex">
                            <input class="form-control" type="text" name="webapp_subdomain" value="{$m.webapp_subdomain}" id="webapp_subdomain" autofocus/> .{$m.webappdomain_label}
                        </div>

                        <div class="hint">
                            <div class="title_hint">
                                Hint:
                            </div>
                            <div class="text_hint">
                                This subdomain will be used for company staff portal.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="signup_subdomain">signup.{$m.webappdomain_label} subdomain</label>
                        <div style="display: flex">
                            <input class="form-control" type="text" name="signup_subdomain" value="{$m.signup_subdomain}" id="signup_subdomain" /> .signup.{$m.webappdomain_label}
                        </div>
                        <div class="hint">
                            <div class="title_hint">
                                Hint:
                            </div>
                            <div class="text_hint">
                                This subdomain will be used for referals signup for the company. Leave empty for default
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="campaigns_domain">Landing Page Domain</label>
                        <input class="form-control" type="text" name="campaigns_domain" value="{$m.campaigns_domain}" id="campaigns_domain"/>
                    </div>
                </div>


                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <input type="submit" value="Next" class="pull-right">
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "businesstypes"}
        {include file="block_begin.tpl"}

        <div class="stepssection">
            <h2>Business Category</h2>
            {include file="wizardsteps.tpl"}
        </div>
        <div class="addcustomer">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            <form action="{$m.action_url}" method="post" class="company_wizard" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label for="businesstypes">Business Categories</label>
                        {$special.businesstype_selected}
                        <select class="form-control" name="businesstypes" id="businesstypes">
                            <option value="">----------</option>
                            {section name=i loop = $m.businesstypes}
                                <option value="{$m.businesstypes[i].values}" {if $m.businesstypes[i].values eq $m.businesstype_selected} selected{/if}>{$m.businesstypes[i].title}</option>
                            {/section}
                        </select>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="bottom_buttons">
                    <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                    <div class="pull-right">
                        <a class="skip" href="{$m.skip_url}">Skip</a>
                        <input type="submit" value="Next">
                    </div>
                </div>
            </form>
        </div>
        {include file="block_end.tpl"}
    {/if}



    {if $m.mode eq "summary"}
        {include file="block_begin.tpl"}
        <div class="stepssection">
            <h2>Summary</h2>
            {include file="wizardsteps.tpl"}
        </div>

        <div class="addcustomer company_summary">
            {if $m.error_message neq ""}
                <div class="aui-message aui-message-error">{$m.error_message}</div>
            {/if}
            {if $m.logo_image neq ""}
                <div class="row">
                    <div class="label">Logo: <img src="{$m.logo_image}" style="max-width: 200px; max-height: 200px;"/></div>
                </div>
            {/if}
            <div class="row">
                <div class="label">Company Name: <strong>{$m.company_name}</strong></div>
            </div>
            {if $m.address neq ""}
                <div class="row">
                    <div class="label">Address: <strong>{$m.address}</strong></div>
                </div>
            {/if}
            {if $m.city neq ""}
                <div class="row">
                    <div class="label">City: <strong>{$m.city}</strong></div>
                </div>
            {/if}
            {if $m.state neq ""}
                <div class="row">
                    <div class="label">State: <strong>{$m.state}</strong></div>
                </div>
            {/if}
            {if $m.zip neq ""}
                <div class="row">
                    <div class="label">Zip: <strong>{$m.zip}</strong></div>
                </div>
            {/if}
            {if $m.timezone neq ""}
                <div class="row">
                    <div class="label">Time Zone: <strong>{$m.timezone}</strong></div>
                </div>
            {/if}
            {if $m.businesshrs neq ""}
                <div class="row businesshrssummary">
                    <div class="label">Business Hours:</div>
                    <div class="hrs">{$m.businesshrs}</div>
                </div>
            {/if}
            {if $m.businesstype neq ""}
                <div class="row">
                    <div class="label">Business Type: <strong>{$m.businesstype}</strong></div>
                </div>
            {/if}
            <div class="row">
                {if $m.twilio_phone neq ""}
                    <div class="label">Twilio Phone: <strong>{$m.twilio_phone}</strong></div>
                {/if}
            </div>
            <div class="row">
                {if $m.cellphone_for_notifications neq ""}
                    <div class="label">Send system notifications to cellphone: <strong>{$m.cellphone_for_notifications}</strong></div>
                {/if}
            </div>
            <div class="row">
                {if $m.webapp_subdomain neq ""}
                    <div class="label">{$m.webappdomain_label} subdomain: <strong>{$m.webapp_subdomain}.{$m.webappdomain_label}</strong></div>
                {/if}
            </div>
            <div class="row">
                {if $m.signup_subdomain neq ""}
                    <div class="label">signup.{$m.webappdomain_label} subdomain: <strong>{$m.signup_subdomain}.{$m.webappdomain_label}</strong></div>
                {/if}
            </div>
            <div class="row">
                {if $m.campaigns_domain neq ""}
                    <div class="label">Landing page domain: <strong>{$m.campaigns_domain}</strong></div>
                {/if}
            </div>

            <div class="row">
                <div class="label">Label For Customer: <strong>{$m.customer_label}</strong></div>
            </div>
            <div class="row">
                <div class="label">Label For Customers: <strong>{$m.customers_label}</strong></div>
            </div>

            {if $m.sms_template_initial neq ""}
                <div class="row">
                    <div class="label">SMS Template (Initial) <strong>{$m.sms_template_initial}</strong></div>
                </div>
            {/if}
            {if $m.sms_template_credentials neq ""}
                <div class="row">
                    <div class="label">SMS Template (Credentials): <strong>{$m.sms_template_credentials}</strong></div>
                </div>
            {/if}

            <div class="bottom_buttons">
                <a href="{$m.back_url}" class="pull-left backarrow">Back</a>
                <a href="{$m.action_url}" class="pull-right finishbutton">Finish</a>
            </div>
        </div>

        {include file="block_end.tpl"}
    {/if}

    {if $m.mode eq "icongallery"}
        {include file="block_begin.tpl"}
        <div class="addcustomer company_summary">
            <div class="row">
                <div class="col-md-12 col-xs-12 icongallery">
                    {if $m.noicons eq 1}
                        <p style="text-align: center">The folder is empty</>
                    {/if}
                    {section name=i loop = $m.filelist}
                        {if $m.filelist[i].type eq "image"}
                            <div class="image">
                                <a href="javascript:;" onclick="$('#selecticonhref').css('display', 'none'); $('#icontumblr').html(''); $('#icontumblr').load('{$m.filelist[i].url}'); Modal.close();"><img src="{$m.filelist[i].image}"/></a>
                            </div>
                        {elseif $m.filelist[i].type eq "dir" AND $m.filelist[i].title neq ""}
                            <div class="directory">
                                <a href="javascript:;" onclick="$('.dirwrapper').html(''); $('#directorywrapper_{$smarty.section.i.index}').load('{$m.filelist[i].url}')" class="directory"><i class="fa fa-folder-o"></i> {$m.filelist[i].title}</a>
                            </div>
                        {/if}
                        <div id="directorywrapper_{$smarty.section.i.index}" class="dirwrapper"></div>
                    {/section}
                </div>
            </div>

        </div>

        {include file="block_end.tpl"}
    {/if}



</div>

