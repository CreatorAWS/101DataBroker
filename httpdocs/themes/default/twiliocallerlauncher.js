$(function () {
    var TwilioStarted = "";
    var TwilioSessionToken = "";
    var twilioaction = "";
    var active = "";
    var IamActive = 0;
    var lastUpdatedTime = 0;

    var intervalTw = window.setInterval(function(){

        TwilioStarted = localStorage.getItem("twiliostarted");
        TwilioSessionToken = localStorage.getItem("twilioSessionToken");
        twilioaction = localStorage.getItem("twilioaction");
        active = localStorage.getItem("active");

        if (TwilioSessionToken === null || TwilioSessionToken === "")
            {
                IamActive = 1;
                lastUpdatedTime = Date.now();
                localStorage.setItem('active', IamActive);
                localStorage.setItem('twilioaction', lastUpdatedTime);
                startupClient();
            }

        else if ( TwilioStarted !== "1" )
            {
                IamActive = 1;
                lastUpdatedTime = Date.now();
                localStorage.setItem('active', IamActive);
                localStorage.setItem('twilioaction', lastUpdatedTime);
                startupClient();
            }

        else if ( active === "" || active === null || active === "0")
            {
                IamActive = 1;
                lastUpdatedTime = Date.now();

                startupClient();
                localStorage.setItem('active', IamActive);
                localStorage.setItem('twilioaction', lastUpdatedTime);
            }
        else if ( IamActive === 0 && active === "1" && (Date.now() - twilioaction) > 3000)
            {
                startupClient();

                IamActive = 1;
                lastUpdatedTime = Date.now();

                localStorage.setItem('active', IamActive);
                localStorage.setItem('twilioaction', lastUpdatedTime);

            }
        else if (IamActive === 1)
            {
                lastUpdatedTime = Date.now();

                localStorage.setItem('active', IamActive);
                localStorage.setItem('twilioaction', lastUpdatedTime);
            }
    }, 1000);
});