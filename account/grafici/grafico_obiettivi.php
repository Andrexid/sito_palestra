<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grafico a torta - Obiettivi Fitness</title>
  <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
  <script src="../charts/apexcharts.js"></script>

  <link rel="stylesheet" href="grafico_obiettivi.css">
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

  <div id="chart-wrapper" 
      class="chart-wrapper" 
      data-labels='<?= json_encode($labels) ?>' 
      data-percentuali='<?= json_encode($percentuali) ?>'>
    <div class="overlay">
      <p>🚧 Ancora in lavorazione 🚧</p>
    </div>
    <div id="chart-container">
      <div id="center-info">
        <h3 id="goal-name"></h3>
        <p id="goal-percent"></p>
      </div>
      <div id="goals"></div>
    </div>
  </div>

  <script src = "grafico_obiettivi.js"></script>
</body>

</html>