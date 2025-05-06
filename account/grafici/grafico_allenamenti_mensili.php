<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id'])) {
    echo "Accesso non autorizzato.";
    exit();
}

require '../database/connessione.php';

$user_id = $_SESSION['id'];
$anno_corrente = date("Y");

$sql = "SELECT 
            MONTH(session_date) AS mese,
            COUNT(*) AS numero_allenamenti
        FROM workout_sessions_month
        WHERE user_id = ? AND YEAR(session_date) = ?
        GROUP BY mese";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Errore nella prepare: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $anno_corrente);
$stmt->execute();
$result = $stmt->get_result();

$allenamenti_mensili = array_fill(1, 12, 0);
while ($row = $result->fetch_assoc()) {
    $allenamenti_mensili[(int)$row['mese']] = (int)$row['numero_allenamenti'];
}

$dati_js = array_values($allenamenti_mensili);
?>
<head>
    <link rel="stylesheet" href="grafico_allenamenti_mensili.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
    <script src="../charts/apexcharts.js"></script>
</head>
<body>
    <h3>Allenamenti mensili</h3>
    <div id="workoutChart" data-allenamenti='<?= json_encode($dati_js) ?>' ></div>

    <script src = "grafico_allenamenti_mensili.js"></script>
</body>