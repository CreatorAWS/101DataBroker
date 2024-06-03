$(function() {
    var form = $('.addcustomerform');
    // Get the form.

    $(form).submit(function(event)
    {
        event.preventDefault();
        var formMessages = $('#form-messages');
        var formData = new FormData(form[0]);

        $('.adminform_savebutton input').prop('disabled', true);
        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            url: $(form).attr('action'),
            data: formData
        })
        .done(function(response)
            {
                var response = JSON.parse(decodeURIComponent(response));

                if(response['message'].includes("success"))
                    {
                        $(formMessages).removeClass('error');
                        $(formMessages).addClass('success');
                        $(form).trigger("reset");
                        $(form).html('');
                        $(formMessages).text('Select Phone Number');

                        html = '<h4 style="margin-bottom: 20px;">Select Number</h4>';
                        html = html + '<ul class="twilio-phonelist">'
                        for (let i = 0; i < response['phonenumbers'].length; i++) {
                            html = html + '<li><a href="javascript:;" onclick="insernumber(\''+ response['phonenumbers_unf'][i] +'\')">' + response['phonenumbers'][i] + '</a></li>'
                        }
                        html = html + '</ul>'

                        $('.messagemodal_content').append(html);
                        $('input[type="submit"]').prop("disabled", false);
                    }
                else
                    {
                        $(formMessages).removeClass('success');
                        $(formMessages).addClass('error');
                        $(formMessages).text(response['message']);
                        $('.adminform_savebutton input').prop('disabled', false);
                    }
            })
        .fail(function(data)
            {
                $(formMessages).removeClass('success');
                $(formMessages).addClass('error');

                // Set the message text.
                if (data.responseText !== '')
                    {
                        $(formMessages).text(data.responseText);
                    }
                else
                    {
                        $(formMessages).text('Oops! An error occured and your message could not be sent.');
                    }
            });
    });
});

function insernumber(number) {
    $('#twilio_phone').val(number);
    $('#messagemodal').modal('hide');
    $('#twilio_phone_generated').val(1);
}