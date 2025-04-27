<?php
session_start();
require_once("../../database/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Durata
    $duration_start = $_POST['duration_start'] ?? null;
    $duration_end = $_POST['duration_end'] ?? null;

    // Allenamenti a settimana
    $week_workout = $_POST['week-workout'] ?? null;

    // Lista esercizi (array)
    $exercise_list = isset($_POST['exercise_list']) ? $_POST['exercise_list'] : null;

    // Se exercise_list è una stringa JSON, decodificala in un array
    if (is_string($exercise_list)) {
        $exercise_list = json_decode($exercise_list, true);
    }
    if (!is_array($exercise_list)) {
        die("Errore: Lista esercizi non valida. Ecco il valore ricevuto: " . var_export($exercise_list, true));
    }


    // Validazione dei dati
    $errors = [];
    if (!$duration_start) $errors[] = "Data di inizio mancante.";
    if (!$duration_end) $errors[] = "Data di fine mancante.";
    if (!$week_workout) $errors[] = "Allenamenti a settimana mancanti.";

    if (empty($errors)) {
        // Inserimento dei dati nel database per il piano di allenamento
        $insert_new_card = "INSERT INTO workout_plans (user_id, start_date, end_date, workouts_per_week) VALUES (?, ?, ?, ?)";
        $stmt_card = $conn->prepare($insert_new_card);

        if ($stmt_card) {
            // Esegui il primo inserimento per il piano di allenamento
            $stmt_card->bind_param("issi", $_SESSION['id'], $duration_start, $duration_end, $week_workout);
            if ($stmt_card->execute()) {
                // Recupera l'ID dell'ultimo inserimento
                $training_card_id = $conn->insert_id;

                // Associa la varianile di sessione all'ID della scheda
                $_SESSION['isEnded'][$training_card_id] = false;

                // Ora inseriamo gli esercizi
                $insert_exercises = "INSERT INTO exercises (name, muscle_group) VALUES (?, ?)";
                $stmt_exercise = $conn->prepare($insert_exercises);

                if ($stmt_exercise) {
                    // Cicla gli esercizi e inserisci uno per uno
                    if (is_array($exercise_list)) { // Verifica che sia un array
                        foreach ($exercise_list as $exercise) {
                            // Verifica se 'name' e 'muscle_group' sono validi
                            if (isset($exercise['name']) && isset($exercise['muscleGroup']) && !empty($exercise['muscleGroup'])) {
                                // Inserisci solo se 'muscle_group' non è vuoto
                                $stmt_exercise->bind_param("ss", $exercise['name'], $exercise['muscleGroup']);
                                if ($stmt_exercise->execute()) {
                                } else {
                                    echo "Errore durante l'inserimento dell'esercizio: " . $stmt_exercise->error . "<br>";
                                }
                            } else {
                                echo "Errore: Esercizio con 'name' o 'muscle_group' mancante o vuoto.<br>";
                            }
                        }
                    } else {
                        echo "La lista degli esercizi non è correttamente formattata.";
                    }
                    $stmt_exercise->close();
                } else {
                    echo "Errore nella preparazione della query per gli esercizi: " . $conn->error . "<br>";
                }
            } else {
                echo "Errore durante l'inserimento del piano di allenamento: " . $stmt_card->error;
            }

            $stmt_card->close();
        } else {
            echo "Errore nella preparazione della query per il piano di allenamento: " . $conn->error;
        }
    } else {
        echo "Errori di validazione: " . implode(", ", $errors);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="new_training_card.css?v=1.1">
</head>

<body>
    <div id="container-results">
        <h1>Visualizza e conferma</h1>
        <form action="./all_training_card.php" class="form-results">
            <label for="">Data inizio: <?php echo $duration_start; ?></label>
            <label for="">Data fine: <?php echo $duration_end; ?> </label>
            <label for="">Allenamenti a settimana: <?php echo $week_workout; ?> </label>
            <label for="">Esercizi inseriti:
                <?php
                echo '<ul>';
                foreach ($exercise_list as $exercise) {
                    echo "<li>";
                    echo $exercise['name'];
                    echo "</li>";
                }
                echo '</ul>';
                echo $_SESSION['isEnded'][$training_card_id];
                ?>
            </label>
            <input type="submit" value="Conferma">
        </form>
    </div>
</body>

</html>