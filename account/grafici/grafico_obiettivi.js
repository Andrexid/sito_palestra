document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById("chart-wrapper");

    const labels = JSON.parse(wrapper.dataset.labels);
    const percentuali = JSON.parse(wrapper.dataset.percentuali);

    const centerInfo = document.getElementById("center-info");
    const nameElem = document.getElementById("goal-name");
    const percentElem = document.getElementById("goal-percent");

    var options = {
        series: percentuali,
        chart: {
            height: 390,
            type: 'radialBar',
            events: {
                dataPointMouseEnter: function(event, chartContext, config) {
                    const i = config.dataPointIndex;
                    nameElem.textContent = labels[i];
                    percentElem.textContent = percentuali[i].toFixed(1) + "%";
                    centerInfo.style.display = "block";
                },
                dataPointMouseLeave: function() {
                    centerInfo.style.display = "none";
                }
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: 0,
                endAngle: 360,
                hollow: {
                    size: '50%',
                    background: 'transparent'
                },
                track: {
                    background: '#f2f2f2',
                    strokeWidth: '70%',
                    margin: 3
                },
                dataLabels: {
                    show: false
                }
            }
        },
        labels: labels,
        stroke: {
            lineCap: "round"
        },
        colors: ['#ff6384', '#ffcd56', '#4bc0c0', '#36a2eb', '#9966ff', '#ff9f40'],
        legend: {
            show: false
        }
    };

    const chartGoals = new ApexCharts(document.querySelector("#goals"), options);
    chartGoals.render();
});
