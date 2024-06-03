var intervalId = '';
var started = "";
var data = "";
var sessionStarted = "";
var IamActive = 0;
var active = "";
var timeInterval = 40000;
var shouldStopTime = Date.now() + 1*60*60*1000;
var IAmActiveTime = Date.now();

$(function () {
    startupClient();
    started = localStorage.getItem("started");
    data = localStorage.getItem("action");
    sessionStarted = sessionStorage.getItem("started");
    active = localStorage.getItem("active");
    IamActive = 0;

    if ( started === "" || started === null )
    {
        IamActive = 1;
        sessionStorage.setItem('started', 1);
        localStorage.setItem('started', 1);
        localStorage.setItem('active', IamActive);
        Start();
    }
    else if ( sessionStarted !== "1")
    {
        sessionStorage.setItem('started', 1);
        localStorage.setItem('started', 1);
        IamActive = 1;
        localStorage.setItem('active', IamActive);
        Start();
    }
    else if ( IamActive === 0 && active === "1")
    {
        IamActive = 1;
        sessionStorage.setItem('started', 1);
        localStorage.setItem('started', 1);
        Start();
        localStorage.setItem('active', IamActive);
    }
    else if (IamActive === 1)
    {
        Start();
        localStorage.setItem('active', IamActive);
    }

    intervalId = window.setInterval(function(){
        started = localStorage.getItem("started");
        data = localStorage.getItem("action");
        sessionStarted = sessionStorage.getItem("started");
        active = localStorage.getItem("active");
        if ( started === "" || started === null )
        {
            IamActive = 1;
            IAmActiveTime = Date.now();
            sessionStorage.setItem('started', 1);
            localStorage.setItem('started', 1);
            localStorage.setItem('active', IamActive);
            Start();
        }
        else if ( sessionStarted !== "1")
        {
            sessionStorage.setItem('started', 1);
            localStorage.setItem('started', 1);
            IamActive = 1;
            IAmActiveTime = Date.now();
            localStorage.setItem('active', IamActive);
            Start();
        }
        else if ( IamActive === 0 && active === "1" && (Date.now() - data) > timeInterval && shouldStopTime > IAmActiveTime)
        {
            IamActive = 1;
            IAmActiveTime = Date.now();
            sessionStorage.setItem('started', 1);
            localStorage.setItem('started', 1);
            Start();
            localStorage.setItem('active', IamActive);
        }
        else if (IamActive === 1 && shouldStopTime > IAmActiveTime)
        {
            IAmActiveTime = Date.now();
            Start();
            localStorage.setItem('active', IamActive);

        }
        else
        {
            clearInterval(intervalId);
        }

    }, timeInterval);
});

function stopScript(){
    clearInterval(intervalId);
}

function Start(){
    localStorage.setItem('action', Date.now());
    $.ajax({
        url: "index.php?m=usersmgmt&d=setonlinetime&theonepage=1",
        type: 'GET',
        error: function () {
            stopScript();
        },
        async: false
    });
}

