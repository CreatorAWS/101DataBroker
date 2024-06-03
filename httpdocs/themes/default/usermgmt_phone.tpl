<td width="30%">Twilio Phone</td>
<td width="70%" class="assignclient">
    <div class="flex">
        <input class="form-control" type="text" name="twilio_phone" value="{$data.twilio_phone}" id="twilio_phone" />
        <a href="javascript:;" onclick="generatePhone()" title="Get a number"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
    </div>

    <div id="messagemodal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="messagemodal_content">
                </div>
            </div>
        </div>
    </div>
</td>

<script>
    {literal}
    function generatePhone() {
        $('#messagemodal_content').html('');
        // Add response in Modal body
        $('.messagemodal_content').load('index.php?m=usersmgmt&d=generatecellphone&theonepage=1');
        // Display Modal
        $('#messagemodal').modal('show');
    };
    {/literal}
</script>

