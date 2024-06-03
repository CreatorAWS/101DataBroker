function make_a_call(url, id_customer) {
    $.ajax({
        url: url,
        type: 'get',
        success: function(response){
            $('#messagemodal_content').html('');
            // Add response in Modal body
            $('#messagemodal_content').html(response);

            // Display Modal
            $('#messagemodal').modal('show');

            $('#messagemodal').on('shown.bs.modal', function (e) {
                setTimeout( function(){
                    twilio_js_call(id_customer + "-primary");
                }  , 1000 );
            });
        }
    });

};
