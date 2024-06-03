$(function () {
  log('Init calling service...');
  $.getJSON('{TOKENURL}')
    .done(function (data) {
      log('Processing...');
      console.log('Token: ' + data.token);

      // Setup Twilio.Device
      Twilio.Device.setup(data.token);

      Twilio.Device.ready(function (device) {
        log('Device Ready! Now you can make a call!');
        document.getElementById('call-controls').style.display = 'block';
      });

      Twilio.Device.error(function (error) {
        log('Call Error: ' + error.message);
        $('#messagemodal').modal('hide');
      });

      Twilio.Device.connect(function (conn) {
        log('Successfully established call!');
        //document.getElementById('button-call').style.display = 'none';
        document.getElementById('button-hangup').style.display = 'inline';
      });

      Twilio.Device.disconnect(function (conn) {
        log('Call ended.');
        //document.getElementById('button-call').style.display = 'inline';
        document.getElementById('button-hangup').style.display = 'none';
        $('#messagemodal').modal('hide');
      });

      Twilio.Device.incoming(function (conn) {
        log('Incoming connection from ' + conn.parameters.From);
        conn.accept();
      });

      //setClientNameUI(data.identity);
    })
    .fail(function () {
      log('Could not init device on the server!');
    });

    /*
  // Bind button to make call
  document.getElementById('button-call').onclick = function () {
    // get the phone number to connect the call to
    var params = {
      To: document.getElementById('phone-number').value
    };

    console.log('Calling ' + params.To + '...');
    Twilio.Device.connect(params);
  };
  */

  // Bind button to hangup call
  document.getElementById('button-hangup').onclick = function () {
    log('Hanging up...');
    Twilio.Device.disconnectAll();
    $('#messagemodal').modal('hide');
  };

});

// Activity log
function log(message) {
  var logDiv = document.getElementById('log');
  logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
  logDiv.scrollTop = logDiv.scrollHeight;
}

// Set the client name in the UI
function setClientNameUI(clientName) {
  var div = document.getElementById('client-name');
  div.innerHTML = 'Your client name: <strong>' + clientName +
    '</strong>';
}

function twilio_js_call($number)
    {
        var params = {
              To: $number
            };
        console.log('Calling ' + params.To + '...');
        Twilio.Device.connect(params);
    }