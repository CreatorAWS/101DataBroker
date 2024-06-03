var config = {
    type: 'line',
    data: {
        labels: ['00', '02', '04', '06', '08', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30'],
        datasets: [{
            label: 'Message Sent',
            backgroundColor: "#ff6666",
            borderColor: "#ff6666",
            fill: false,
            data: [
                0, 2, 6, 4, 5, 7, 4, 2, 6, 4, 5, 7, 4
            ],
        }, {
            label: 'Message Received',
            backgroundColor: "#4dffa6",
            borderColor: "#4dffa6",
            fill: false,
            data: [
                2, 3, 7, 4, 1, 2, 3, 3, 7, 4, 1, 2, 3
            ],
    
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: ''
        },
        scales: {
            xAxes: [{
                display: true,
      scaleLabel: {
        display: true,
        labelString: 'Day'
      },
        
            }],
            yAxes: [{
                display: true,
                //type: 'logarithmic',
      scaleLabel: {
                        display: true,
                        labelString: 'Messages'
                    },
                    ticks: {
                        min: 00,
                        max: 09,

                        // forces step size to be 5 units
                        stepSize: 01
                    }
            }]
        }
    }
};

window.onload = function() {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
};

document.getElementById('randomizeData').addEventListener('click', function() {
    config.data.datasets.forEach(function(dataset) {
        dataset.data = dataset.data.map(function() {
            return randomScalingFactor();
        });

    });

    window.myLine.update();
});

