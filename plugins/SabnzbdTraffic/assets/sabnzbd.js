const data = {
  datasets: [{
    label: 'Speed',
    backgroundColor: 'rgb(255, 99, 132)',
    borderColor: 'rgb(255, 99, 132)',
    data: []
  }]
};

const config = {
    type: 'line',
    data,
    options: {
        animation: false,
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'second'
                }
            },
            y: {
                type: 'linear',
                min: 0,
                max: 0
            }
        },
        plugins: {
            legend: {
                display: false
              }
        }     
    }
};
function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}    
docReady(function(){
    var sabnzbdtraffic = new Chart(
        document.getElementById('sabnzbdChart'),
        config
      );
    function updateChart() {
        fetch('/plugins/sabnzbtraffic/data')
        .then(response => response.json())
        .then(data => {
            sabnzbdtraffic.data.datasets[0].data.push({
                    x: new Date(Date.now()),
                    y: data.currentSpeed
            });
            if(sabnzbdtraffic.data.datasets[0].data.length > 20) {
                sabnzbdtraffic.data.datasets[0].data.shift();
            }
            sabnzbdtraffic.update();
            sabnzbdtraffic.options.scales.y.max = data.fullSpeed;
        });
    }
    setInterval(updateChart,1000);
    updateChart()
    updateChart()

})
