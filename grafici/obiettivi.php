<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grafico a torta - Obiettivi Fitness</title>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="stylesheet" href="../css/grafici.css">
</head>

<body>

  <?php
  require '../database/connessione.php';

  $sql = "
    SELECT 
        p.valore_iniziale, 
        p.valore_finale, 
        p.valore_attuale, 
        g.obiettivo 
    FROM 
        user_goals p
    JOIN 
        goals g ON p.id_goal = g.id
    WHERE 
        p.id_utente = 24
";

  $result = $conn->query($sql);

  $labels = [];
  $percentuali = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $iniziale = (float)$row["valore_iniziale"];
      $finale = (float)$row["valore_finale"];
      $attuale = (float)$row["valore_attuale"];

      $percentuale = 0;
      if ($finale != $iniziale) {
        $percentuale = (($attuale - $iniziale) / ($finale - $iniziale)) * 100;
        $percentuale = max(0, min($percentuale, 100));
      }

      $labels[] = $row["obiettivo"];
      $percentuali[] = round($percentuale, 2);
    }
  }
  require '../database/close-connessione.php';
  ?>

  <div id="goals"></div>

  <script>
    var options = {
      series: <?php echo json_encode($percentuali); ?>,
      chart: {
        height: 390,
        type: 'radialBar',
      },
      plotOptions: {
        radialBar: {
          offsetY: 0,
          startAngle: 0,
          endAngle: 270,
          hollow: {
            margin: 5,
            size: '30%',
            background: 'transparent',
          },
          dataLabels: {
            name: {
              show: false
            },
            value: {
              show: false
            }
          },
          barLabels: {
            enabled: true,
            useSeriesColors: true,
            offsetX: -8,
            fontSize: '16px',
            formatter: function(seriesName, opts) {
              return seriesName + ": " + opts.w.globals.series[opts.seriesIndex].toFixed(1) + "%";
            },
          },
        }
      },
      colors: ['#1ab7ea', '#0084ff', '#39539E', '#0077B5'],
      labels: <?php echo json_encode($labels); ?>,
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            show: false,
            fontSize: '5px'
          }
        }
      }]
    };

    var chart = new ApexCharts(document.querySelector("#goals"), options);
    chart.render();
  </script>


</body>

</html>