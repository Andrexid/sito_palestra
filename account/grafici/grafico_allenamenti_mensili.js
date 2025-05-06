document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById("workoutChart");
    const datiJson = chartElement.dataset.allenamenti;
    const allenamentiMensili = JSON.parse(datiJson);

    var options = {
        series: [{
            name: "Allenamenti",
            data: allenamentiMensili
        }],
        chart: {
            type: 'bar',
            height: 400
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic"],
            labels: {
                style: {
                    colors: "#fff"
                }
            }
        },
        yaxis: {
            title: {
                text: "Numero di Allenamenti",
                style: {
                    color: "#fff"
                }
            },
            labels: {
                style: {
                    colors: "#fff"
                }
            }
        },
        fill: {
            opacity: 0.9
        },
        colors: ["#00E396"],
        tooltip: {
            theme: "dark"
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#workoutChart"), options);
    chart.render();
});
