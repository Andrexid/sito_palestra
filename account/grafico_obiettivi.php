<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grafico a torta - Obiettivi Fitness</title>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  <style>
    #chart-container {
      position: relative;
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }

    #goals {
      width: 100%;
    }

    #center-info {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      font-family: sans-serif;
      pointer-events: none;
      z-index: 10;
      display: none;
    }

    #center-info h3 {
      font-size: 16px;
      margin: 0;
      color: #fff;
      /* Cambiato da #333 a bianco */
    }

    #center-info p {
      font-size: 20px;
      margin: 0;
      font-weight: bold;
      color: #fff;
      /* Cambiato da #666 a bianco */
    }
  </style>

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

  <div id="chart-container">
    <div id="center-info" style="display: none;">
      <h3 id="goal-name"></h3>
      <p id="goal-percent"></p>
    </div>
    <div id="goals"></div>
  </div>

  <script>
    const labels = <?php echo json_encode($labels); ?>;
    const percentuali = <?php echo json_encode($percentuali); ?>;

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
            strokeWidth: '70%', // Aumentato lo spessore delle linee
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
  </script>
</body>

</html>