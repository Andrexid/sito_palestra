<?php
session_start();
require_once("../database/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['training_card_id'])) {
    $training_card_id = $_POST['training_card_id'];

    // Recupera tutti i dati dal form
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $weight = $_POST['weight'];
    $rest_time = $_POST['rest_time'];
    $notes = $_POST['notes'];

    // Query di update
    $update_query = "
        UPDATE workout_exercises 
        SET sets = ?, reps = ?, weight = ?, rest_time = ?, notes = ? 
        WHERE id = ? AND workout_plan_id = ?
    ";
    $stmt = $conn->prepare($update_query);

    if ($stmt) {
        foreach ($sets as $exercise_id => $set_value) {
            $rep_value = $reps[$exercise_id] ?? 0;
            $weight_value = $weight[$exercise_id] ?? 0;
            $rest_value = $rest_time[$exercise_id] ?? 0;
            $note_value = $notes[$exercise_id] ?? null;

            // Esegui l'update
            $stmt->bind_param("iiiisii", $set_value, $rep_value, $weight_value, $rest_value, $note_value, $exercise_id, $training_card_id);
            $stmt->execute();
        }

        $stmt->close();
        echo "✅ Scheda aggiornata correttamente.";
    } else {
        echo "❌ Errore nella preparazione della query: " . $conn->error;
    }
} else {
    echo "❌ Dati mancanti.";
}

require_once("../database/close-connessione.php");

// Redirect indietro alla pagina della scheda
header("Location: ./all_training_card.php");
exit;
?>
