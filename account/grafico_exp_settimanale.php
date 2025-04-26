<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafico Exp Settimanale</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        body {
            background-color: #1e1e1e;
            color: white;
        }
    </style>
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

        // Inizializza array con 0 punti per ogni giorno della settimana (Lunedì = 1, Domenica = 7)
        $punteggi = array_fill(1, 7, 0);

        while ($row = $result->fetch_assoc()) {
            $dayOfWeek = (int)$row['giorno_settimana'];
            $punteggi[$dayOfWeek] = (int)$row['punti_exp'];
        }

        return $punteggi;
    }

    // Date intervalli
    $oggi = date('Y-m-d');
    $inizioSettimana = date('Y-m-d', strtotime('monday this week'));
    $fineSettimana = date('Y-m-d', strtotime('sunday this week'));

    $inizioSettimanaScorsa = date('Y-m-d', strtotime('monday last week'));
    $fineSettimanaScorsa = date('Y-m-d', strtotime('sunday last week'));

    $dati_settimana = getExpData($inizioSettimana, $fineSettimana, $conn);
    $dati_scorsa = getExpData($inizioSettimanaScorsa, $fineSettimanaScorsa, $conn);

    require '../database/close-connessione.php';
    ?>

    <div id="chartexp"></div>

    <script>
        const giorniSettimana = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];

        const datiSettimana = <?php echo json_encode(array_values($dati_settimana)); ?>;
        const datiScorsa = <?php echo json_encode(array_values($dati_scorsa)); ?>;

        var options = {
            series: [{
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
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#000000'] // Testo nero
                },
                background: {
                    enabled: true,
                    foreColor: 'white', // Testo nero
                    background: '#ffffff', // Sfondo bianco
                    borderRadius: 1,
                    padding: 4,
                    opacity: 1
                }
            },


            tooltip: {
                theme: 'dark', // sfondo bianco, testo nero
                style: {
                    fontSize: '14px',
                    color: '#000000'
                }
            },

            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Punti Exp Settimanali',
                align: 'left',
                style: {
                    color: '#ffffff'
                }
            },
            grid: {
                borderColor: '#444',
                row: {
                    colors: ['#2a2a2a', 'transparent'],
                    opacity: 0.5
                },
            },
            markers: {
                size: 4
            },
            xaxis: {
                categories: ['Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato', 'Domenica'],
                title: {
                    text: 'Giorni della settimana',
                    style: {
                        color: '#ffffff',
                        fontSize: "10pt"
                    }
                },
                labels: {
                    style: {
                        colors: '#ffffff'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Punti Exp',
                    style: {
                        color: '#ffffff',
                        fontSize: "10pt"
                    }
                },
                labels: {
                    style: {
                        colors: '#ffffff'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5,
                labels: {
                    colors: '#ffffff'
                }
            }
        };

        var chart_exp = new ApexCharts(document.querySelector("#chartexp"), options);
        chart_exp.render();
    </script>

</body>

</html>