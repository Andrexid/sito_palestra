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

<h3>Allenamenti mensili</h3>
<div id="workoutChart" style="max-width: 700px; margin: auto;"></div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        series: [{
            name: "Allenamenti",
            data: <?= json_encode($dati_js) ?>
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
</script>
