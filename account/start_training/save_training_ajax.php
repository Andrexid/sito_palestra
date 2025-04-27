<?php
session_start();
header('Content-Type: application/json');
require_once("../../database/connessione.php");

if (!isset($_SESSION['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Accesso negato."]);
    exit();
}

$user_id = $_SESSION['id'];
$day = isset($_POST['day']) ? intval($_POST['day']) : null;
$sets = $_POST['sets'] ?? [];
$reps = $_POST['reps'] ?? [];
$notes = $_POST['notes'] ?? [];

$sql = "SELECT id, xp_earned, workouts_done FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($workout_plan_id, $xp_attuali, $workout_fatti);
$stmt->fetch();
$stmt->close();

if (!$workout_plan_id || !$day) {
    echo json_encode(["success" => false, "message" => "Errore nel recupero dei dati."]);
    exit();
}

$total_xp = 0;

foreach ($sets as $exercise_id => $num_set) {
    $exercise_id = intval($exercise_id);
    $num_set = intval($num_set);

    $reps_array = $reps[$exercise_id] ?? [];
    $weights_array = $_POST['weights'][$exercise_id] ?? [];

    $reps_string = implode(",", array_map('intval', $reps_array));
    $weights_string = implode(",", array_map('floatval', $weights_array));
    $note = isset($notes[$exercise_id]) ? trim($notes[$exercise_id]) : null;

    // Media delle ripetizioni
    $media_reps = count($reps_array) > 0 ? array_sum($reps_array) / count($reps_array) : 0;

    // Calcolo XP per questo esercizio
    $xp_esercizio = floor($num_set * $media_reps * 1.2); // moltiplicatore bilanciato
    $total_xp += $xp_esercizio;

    $sql = "UPDATE workout_exercises 
        SET sets = ?, reps = ?, weight = ?, notes = ?, rest_time = ?
        WHERE workout_plan_id = ? AND exercise_id = ? AND exercise_day = ?";

    $stmt = $conn->prepare($sql);
    $default_rest_time = 60;
    $stmt->bind_param(
        "isssiiii",
        $num_set,
        $reps_string,
        $weights_string,
        $note,
        $default_rest_time,
        $workout_plan_id,
        $exercise_id,
        $day
    );
    $stmt->execute();
    $stmt->close();
}

// XP totali aggiornati nel piano
$nuovo_totale_xp = $xp_attuali + $total_xp;
$nuovo_workout_fatti = $workout_fatti + 1;

$update = $conn->prepare("UPDATE workout_plans SET xp_earned = ?, workouts_done = ? WHERE id = ?");
$update->bind_param("iii", $nuovo_totale_xp, $nuovo_workout_fatti, $workout_plan_id);
$update->execute();
$update->close();


// ➕ INSERISCI LA SESSIONE NELLA TABELLA workout_sessions_month
$insert_session = $conn->prepare("
    INSERT INTO workout_sessions_month (user_id, workout_plan_id, session_date, xp_earned)
    VALUES (?, ?, CURRENT_DATE, ?)
");
$insert_session->bind_param("iii", $user_id, $workout_plan_id, $total_xp);
$insert_session->execute();
$insert_session->close();

// ➕ INSERISCI XP NELLA TABELLA exp_points
$insert_exp = $conn->prepare("
    INSERT INTO exp_points (id_utente, punti_exp, giorno)
    VALUES (?, ?, CURRENT_DATE)
");
$insert_exp->bind_param("ii", $user_id, $total_xp);
$insert_exp->execute();
$insert_exp->close();

$conn->close();

echo json_encode([
    "success" => true,
    "total_xp" => $nuovo_totale_xp,
    "xp" => $total_xp
]);
exit();
?>
