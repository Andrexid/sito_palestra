<?php
session_start();
require_once("../database/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Durata
    $duration_start = $_POST['duration_start'] ?? null;
    $duration_end = $_POST['duration_end'] ?? null;

    // Allenamenti a settimana
    $week_workout = $_POST['week-workout'] ?? null;

    // Lista esercizi (array)
    $exercise_list = isset($_POST['exercise_list']) ? $_POST['exercise_list'] : null;

    // Se exercise_list è una stringa JSON, decodificala in un array
    if ($exercise_list && is_string($exercise_list)) {
        $exercise_list = json_decode($exercise_list, true); // Decodifica in array associativo
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
                echo "Piano di allenamento inserito con successo!<br>";
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
                                    echo "Esercizio inserito con successo!<br>";
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

require_once("../database/close-connessione.php");
?>
