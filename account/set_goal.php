<?php
session_start();
require '../database/connessione.php';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleziona Obiettivi</title>
    <link rel="stylesheet" href="set_goal.css">

</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ((isset($_POST['obiettivi']) && !empty($_POST['obiettivi'])) || (isset($_POST['exercise_goals']) && !empty($_POST['exercise_goals']))) {

            echo "<h1>Obiettivi Selezionati</h1>";
            echo "<p class='sub'>Inserisci i dati nei campi per cominciare a settare i tuoi obiettivi!</p>";
            echo "<form method='POST' action='./insert_goal.php'>";
            echo "<div class='card_container'>";

            if (isset($_POST['exercise_goals']) && !empty($_POST['exercise_goals'])) {
                foreach ($_POST['exercise_goals'] as $id_ex => $ex_goals) {
                    echo "<div class='card'>";
                    echo "<h3>" . htmlspecialchars($ex_goals) . "</h3>";

                    echo "<input type='hidden' name='id_utente[]' value='" . $_SESSION['id'] . "'>";
                    echo "<input type='hidden' name='exercise[]' value='" . htmlspecialchars($ex_goals, ENT_QUOTES, 'UTF-8') . "'>";
                    echo "<input type='hidden' name='id_exercises[]' value='" . (int)$id_ex . "'>";

                    echo "<div class='container-insert'>
                            <p>Inserisci un valore iniziale</p>
                            <div class='insert'>
                                <input type='text' name='es_valore_iniziale[]' required> kg
                            </div>
                        </div>";

                    echo "<div class='container-insert'>
                            <p>Inserisci un valore da raggiungere</p>
                            <div class='insert'>
                                <input type='text' name='es_valore_finale[]' required> kg
                            </div>
                        </div>";

                    echo "<div class='container-insert'>
                            <p>Inserisci la data finale</p>
                            <div class='insert'>
                                <input type='date' name='es_data_finale[]' required>
                            </div>
                        </div>";

                    echo "</div>"; // chiude card
                }
            }

            if (isset($_POST['obiettivi']) && !empty($_POST['obiettivi'])) {
                $obiettivi_selezionati = $_POST['obiettivi'];
                // Obiettivi generici 
                foreach ($obiettivi_selezionati as $obiettivo_nome) {
                    // Prepara la query per cercare l'obiettivo
                    $query = "SELECT * FROM goals WHERE obiettivo = ?";
                    $stm = $conn->prepare($query);
                    $stm->bind_param("s", $obiettivo_nome);

                    if ($stm->execute()) {
                        $result = $stm->get_result();
                        if ($row = $result->fetch_assoc()) {
                            echo "<div class='card'>";
                            echo "<h3>" . htmlspecialchars($row['obiettivo']) . "</h3>";
                            echo "<p>" . htmlspecialchars($row['descrizione']) . "</p>";
                            echo "<span> <p class='grassetto'>Unità di misurazione: </p>" . htmlspecialchars($row['metrica']) . "</span>";

                            // Hidden fields per i dati da inviare
                            echo "<input type='hidden' name='id_goal[]' value='" . $row['id'] . "'>";
                            echo "<input type='hidden' name='id_utente[]' value='" . $_SESSION['id'] . "'>";
                            echo "<input type='hidden' name='obiettivo[]' value='" . htmlspecialchars($row['obiettivo'], ENT_QUOTES, 'UTF-8') . "'>";

                            // Campi input per valore iniziale e finale
                            echo "<div class='container-insert'>
                                    <p>Inserisci un valore iniziale</p>
                                    <div class='insert'>
                                        <input type='text' name='valore_iniziale[]' required> " . htmlspecialchars($row['unita_misura']) . "
                                    </div>
                                </div>";

                            echo "<div class='container-insert'>
                                    <p>Inserisci un valore da raggiungere</p>
                                    <div class='insert'>
                                        <input type='text' name='valore_finale[]' required> " . htmlspecialchars($row['unita_misura']) . "
                                    </div>
                                </div>";

                            echo "<div class='container-insert'>
                                    <p>Inserisci la data finale</p>
                                    <div class='insert'>
                                        <input type='date' name='data_finale[]' required>
                                    </div> 
                                </div>";

                            echo "</div>"; // chiude card
                        }
                    }
                }
            }

            echo "</div>"; // chiude card_container
            echo "<input type='submit' value='Salva Obiettivi' class='principal_button'>";
            echo "</form>";
        } else {
            echo "<h1>Nessun obiettivo selezionato</h1>";
        }
    } else {
        echo "<h1>Errore: metodo non valido</h1>";
    }

    require '../database/close-connessione.php';
    ?>

</body>

</html>