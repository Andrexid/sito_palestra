<?php
session_start();
header('Content-Type: application/json');
require_once("../database/connessione.php");

if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Accesso negato."]);
    exit();
}

$user_id = $_SESSION['id'];
$day = isset($_POST['day']) ? intval($_POST['day']) : null;
$sets = $_POST['sets'] ?? [];
$reps = $_POST['reps'] ?? [];
$notes = $_POST['notes'] ?? [];
$completed = $_POST['completed'] ?? [];

// Ottieni l'ultima scheda dell'utente
$sql = "SELECT id FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($workout_plan_id);
$stmt->fetch();
$stmt->close();

if (!$workout_plan_id || !$day) {
    echo json_encode(["success" => false, "message" => "Errore nel recupero dei dati."]);
    exit();
}

// Per ogni esercizio, aggiorna i dati relativi a sets, reps (media), note
foreach ($sets as $exercise_id => $num_set) {
    $exercise_id = intval($exercise_id);
    $num_set = intval($num_set);

    // Calcolo della media delle ripetizioni (se presenti)
    $reps_array = $reps[$exercise_id] ?? [];
    $reps_avg = !empty($reps_array) ? array_sum($reps_array) / count($reps_array) : 0;

    $note = isset($notes[$exercise_id]) ? trim($notes[$exercise_id]) : null;
    $is_completed = isset($completed[$exercise_id]) ? 1 : 0;

    // Aggiorna la riga nella tabella workout_exercises
    $sql = "UPDATE workout_exercises 
            SET sets = ?, reps = ?, notes = ?, rest_time = ?, weight = ?
            WHERE workout_plan_id = ? AND exercise_id = ? AND exercise_day = ?";
    
    $stmt = $conn->prepare($sql);
    $default_rest_time = 60;
    $default_weight = null;
    $stmt->bind_param(
        "iisidiii",
        $num_set,
        $reps_avg,
        $note,
        $default_rest_time,
        $default_weight,
        $workout_plan_id,
        $exercise_id,
        $day
    );
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo json_encode(["success" => true, "message" => "Allenamento salvato con successo."]);
exit();
?>
