<?php
header("Content-Type: application/json");
require_once("../../database/connessione.php");

session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Utente non autenticato."]);
    exit();
}

$user_id = $_SESSION['id'];

// Validazione dati in ingresso
if (!isset($_POST['start_date'], $_POST['end_date'], $_POST['workouts_per_week'], $_POST['exercise_list'])) {
    echo json_encode(["success" => false, "message" => "Dati mancanti."]);
    exit();
}

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$workouts_per_week = (int)$_POST['workouts_per_week'];
$exercises = json_decode($_POST['exercise_list'], true); // [{ name: "Squat", day: 1 }, ...]

if (!is_array($exercises)) {
    echo json_encode(["success" => false, "message" => "Formato esercizi non valido."]);
    exit();
}

// 1. Inserisci nella tabella workout_plans
$sql = "INSERT INTO workout_plans (user_id, start_date, end_date, workouts_per_week) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $user_id, $start_date, $end_date, $workouts_per_week);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Errore durante l'inserimento del piano."]);
    exit();
}

$workout_plan_id = $stmt->insert_id;
$stmt->close();

// 2. Per ogni esercizio, trova l'ID e salva nella tabella workout_exercises con il giorno
foreach ($exercises as $exercise) {
    $exerciseName = $exercise['name'];
    $exerciseDay = (int)$exercise['day'];

    // Trova l’ID dell’esercizio
    $stmt = $conn->prepare("SELECT id FROM exercises WHERE name = ?");
    $stmt->bind_param("s", $exerciseName);
    $stmt->execute();
    $stmt->bind_result($exercise_id);
    $stmt->fetch();
    $stmt->close();

    if ($exercise_id) {
        // Inserisci nella tabella workout_exercises con il giorno
        $stmt = $conn->prepare("INSERT INTO workout_exercises (workout_plan_id, exercise_id, exercise_day) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $workout_plan_id, $exercise_id, $exerciseDay);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

echo json_encode(["success" => true, "message" => "Piano di allenamento salvato con successo."]);
?>
