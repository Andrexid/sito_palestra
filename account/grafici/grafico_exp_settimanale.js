document.addEventListener("DOMContentLoaded", function () {
    const giorniSettimana = ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'];

    const chartDiv = document.getElementById('chartexp');
    const datiSettimana = JSON.parse(chartDiv.getAttribute('data-dati-settimana'));
    const datiScorsa = JSON.parse(chartDiv.getAttribute('data-dati-scorsa'));

    const options = {
        series: [
            {
                name: "Questa settimana",
                data: datiSettimana,
                color: '#00ff00'
            },
            {
                name: "Settimana scorsa",
                data: datiScorsa,
                color: '#888'
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            background: '#1e1e1e',
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.5
            },
            zoom: { enabled: false },
            toolbar: { show: false }
        },
        dataLabels: {
            enabled: true,
            style: { colors: ['#000000'] },
            background: {
                enabled: true,
                foreColor: 'white',
                background: '#ffffff',
                borderRadius: 1,
                padding: 4,
                opacity: 1
            }
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '14px', color: '#000000' }
        },
        stroke: { curve: 'smooth' },
        title: {
            text: 'Punti Exp Settimanali',
            align: 'left',
            style: { color: '#ffffff' }
        },
        grid: {
            borderColor: '#444',
            row: { colors: ['#2a2a2a', 'transparent'], opacity: 0.5 }
        },
        markers: { size: 4 },
        xaxis: {
            categories: giorniSettimana,
            title: {
                text: 'Giorni della settimana',
                style: { color: '#ffffff', fontSize: "10pt" }
            },
            labels: { style: { colors: '#ffffff' } }
        },
        yaxis: {
            title: {
                text: 'Punti Exp',
                style: { color: '#ffffff', fontSize: "10pt" }
            },
            labels: { style: { colors: '#ffffff' } }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            floating: true,
            offsetY: -25,
            offsetX: -5,
            labels: { colors: '#ffffff' }
        }
    };

    const chart_exp = new ApexCharts(chartDiv, options);
    chart_exp.render();
});
