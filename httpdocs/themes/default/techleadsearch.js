$(function() {

    var form = $('#searchForm');
    var html='';
    $(form).submit(function(event)
    {
        event.preventDefault();
        $('.loader').addClass('display');
        $('.loader').css('display', 'flex');
        $('#error_message').html('');
        $('#error_message').css('display', 'none');
        var formData = $(form).serialize();
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
        })
            .done(function(response) {

                console.log(response);

                var data = JSON.parse(decodeURIComponent(response));
                if(data.message.includes("success"))
                    window.location.replace(data.url);
                else
                {
                    $('.loader').css('display', '');
                    $('.loader').removeClass('display');
                    $('#error_message').html(data.error_message);
                    $('#error_message').css('display', 'table');
                }
            });
    });
});