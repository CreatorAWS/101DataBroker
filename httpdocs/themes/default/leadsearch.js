var autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'));
autocomplete.setFields(['place_id', 'geometry']);
autocomplete.addListener('place_changed', function () {
    const place = autocomplete.getPlace();
    const placeID = place.place_id;
    const geometry = place.geometry;

    var lat = place.geometry.location.lat(),
        lng = place.geometry.location.lng();

    if (typeof placeID !== 'undefined') {
        $('#place_id').val(placeID);
    }

    if (typeof geometry !== 'undefined') {
        $('#place_geo').val(lat + ' ' + lng);
    }
});


$(function() {

    $('input').keydown(function (e) {
        if (e.keyCode == 13) {
            var inputs = $(this).parents("form").eq(0).find(":input");
            if (inputs[inputs.index(this) + 1] != null) {
                inputs[inputs.index(this) + 1].focus();
            }
            e.preventDefault();
            return false;
        }
    });

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