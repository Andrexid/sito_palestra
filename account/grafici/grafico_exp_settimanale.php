<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafico Exp Settimanale</title>
    <link rel="stylesheet" href="grafico_exp_settimanale.css">
    <script src="../charts/apexcharts.js"></script>
</head>
<body>
<?php
require '../database/connessione.php';

function getExpData($startDate, $endDate, $conn)
{
    $sql = "
        SELECT 
            DAYOFWEEK(giorno) as giorno_settimana, 
            punti_exp 
        FROM 
            exp_points 
        WHERE 
            id_utente = '$_SESSION[id]' AND 
            giorno BETWEEN '$startDate' AND '$endDate'
    ";

    $result = $conn->query($sql);
    $punteggi = array_fill(1, 7, 0);
    while ($row = $result->fetch_assoc()) {
        $dayOfWeek = (int)$row['giorno_settimana'];
        $punteggi[$dayOfWeek] = (int)$row['punti_exp'];
    }
    return $punteggi;
}

$inizioSettimana = date('Y-m-d', strtotime('monday this week'));
$fineSettimana = date('Y-m-d', strtotime('sunday this week'));
$inizioSettimanaScorsa = date('Y-m-d', strtotime('monday last week'));
$fineSettimanaScorsa = date('Y-m-d', strtotime('sunday last week'));

$dati_settimana = array_values(getExpData($inizioSettimana, $fineSettimana, $conn));
$dati_scorsa = array_values(getExpData($inizioSettimanaScorsa, $fineSettimanaScorsa, $conn));

require '../database/close-connessione.php';
?>

<!-- DIV nascosti per passare i dati -->
<div id="chartexp"
     data-dati-settimana='<?= json_encode($dati_settimana) ?>'
     data-dati-scorsa='<?= json_encode($dati_scorsa) ?>'></div>

<script src="grafico_exp_settimanale.js"></script>
</body>
</html>
