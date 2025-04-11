<?php
header("Content-Type: application/json");
require_once("../database/connessione.php");

session_start();
$user_id = $_SESSION['id']; // O da un controllo

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$workouts_per_week = $_POST['workouts_per_week'];
$exercises = json_decode($_POST['exercise_list'], true); // [{ name: "Squat", muscleGroup: "Gambe" }, ...]
echo $exercises;

// 1. Inserisci nella tabella workout_plans
$sql = "INSERT INTO workout_plans (user_id, start_date, end_date, workouts_per_week) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $user_id, $start_date, $end_date, $workouts_per_week);
$stmt->execute();
$workout_plan_id = $stmt->insert_id;
$stmt->close();

// 2. Per ogni esercizio, trova l'ID dalla tabella exercises e salva nella tabella workout_exercises
foreach ($exercises as $exercise) {
    $exerciseName = $exercise['name'];

    // Trova l’ID dell’esercizio
    $stmt = $conn->prepare("SELECT id FROM exercises WHERE name = ?");
    $stmt->bind_param("s", $exerciseName);
    $stmt->execute();
    $stmt->bind_result($exercise_id);
    $stmt->fetch();
    $stmt->close();

    if ($exercise_id) {
        // Inserisci nella tabella workout_exercises
        $stmt = $conn->prepare("INSERT INTO workout_exercises (workout_plan_id, exercise_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $workout_plan_id, $exercise_id);
        $stmt->execute();
        $stmt->close();
    }
}
$conn->close();
?>
