device = '';
var activeConnection = null;

async function startupClient() {
    log("Requesting Access Token...");
    try {
        const data = await $.getJSON("index.php?m=twiliojsontoken");
        log("Got a token.");
        log(data.token);
        token = data.token;
        localStorage.setItem('twilioSessionToken', token);
        localStorage.setItem('token_error', '0');
        StartDevice(data);
    } catch (err) {
        console.log(err);
        localStorage.setItem('token_error', '1');
        log("An error occurred. See your browser console for more information.");
    }
}

async function getClientNotes(id) {
    $.ajax({
        url: "index.php?m=contactcustomer&d=notes&theonepage=1&id=" + id,
        type: 'get',
        success: function(response){
            $('#notes_section').html(response);
        }
    });
}

async function getClientInfo(phone) {
    client = await $.getJSON("index.php?m=customers&d=getbriefinfo&phone=" + phone);

    if (client.name !== '' && client.name !== null && client.name !== undefined)
        {
            $('#caller_info_name').html('<a href="' + client.url + '" target="_blank">' + client.name + '</a>');
        }
    if (client.id !== '' && client.id !== null && client.id !== undefined)
        getClientNotes(client.id);
}

function answer_the_call() {
    $('.answer').addClass('hidden');
}

function StartDevice (data) {
    var client = '';

    localStorage.setItem('twiliostarted', 1);

    // Setup Twilio.Device
    device = new Twilio.Device(token);
    device.register();


    device.on('registered', function () {
        log('Device Ready! Now you can make a call!');
        $('#call-controls').css('display', 'block');
        $('#call-info').css('display', 'none');
    });


    device.on('error', e => {
        log('Call Error: ' + e);
        localStorage.setItem('token_error', '1');
    });

    device.on('disconnect', function () {
        log('Call ended.');
        $('#button-call').removeClass('hidden');
        $('#button-hangup').css('display', 'none');
        $('#callsmodal').modal('hide');
    });

    device.on('connect', function (conn) {
        activeConnection = conn;
        log('Successfully established call!');
        $('#button-call').addClass('hidden');
        $('#button-hangup').css('display', 'inline');

        console.log(data.token);
    });

    device.on('incoming', function(conn) {
        activeConnection = conn;
        log('Incoming connection from ' + conn.parameters.From);
        if (conn.parameters.From !== '' || conn.parameters.From !== null)
            {
                $('#caller_info').html(conn.parameters.From);
                getClientInfo(conn.parameters.From);
            }
        $('#callsmodal').modal('show');
        $( "#answer_call" ).click(function() {
            conn.accept();
        });

        $( "#button-decline" ).click(function() {
            conn.disconnect();
        });
    });

    $( "#button-hangup" ).click(function() {
        log('Hanging up...');
        $('#button-hangup').css('display', 'none');
        device.disconnectAll();
        $('#messagemodal').modal('hide');
    });

    $( "#button-decline" ).click(function() {
        log('Hanging up...');
        $('#button-hangup').css('display', 'none');
        device.disconnectAll();
        $('#messagemodal').modal('hide');
    });

}

function log(message) {
    console.log(message);
}


function twilio_js_call($number) {
    var params = {
        To: $number
    };
    console.log('Calling ' + params.To + '...');
    device.connect({params});
}

function test () {
    $('#caller_info').html('14352003216');
    getClientInfo('14352003216');
    $('#callsmodal').modal('show');
}

function decline_the_call () {
    if (activeConnection) {
        activeConnection.disconnect();
    }
    $('#callsmodal').modal('hide');
    setTimeout( function(){
        location.reload();
    }, 1000 );
}