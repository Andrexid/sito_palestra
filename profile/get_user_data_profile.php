<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('../database/connessione.php');

if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "Sessione non valida o utente non loggato"]);
    exit;
}

$user_id = $_SESSION['id'];

// Primo blocco: info utente base
$sql = "SELECT nome, cognome, email, data_nascita, sesso, peso, altezza, puntiEXP, nAllenamenti, fotoProfilo FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query utente base"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["error" => "Utente non trovato"]);
    exit;
}
$stmt->close();

// Calcolo XP totale guadagnato dall'utente
$stmt = $conn->prepare("SELECT SUM(xp_earned) as xp_totale FROM workout_plans WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query xp totale"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user["xpTotale"] = $res->fetch_assoc()["xp_totale"] ?? 0;
$stmt->close();

// Numero obiettivi attivi
$stmt = $conn->prepare("SELECT COUNT(*) as obiettivi_attivi FROM user_goals WHERE id_utente = ? AND data_fine IS NULL");
if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query obiettivi attivi"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user["numeroObiettiviAttivi"] = $res->fetch_assoc()["obiettivi_attivi"];
$stmt->close();


// Totale set e ripetizioni completate
$stmt = $conn->prepare("
    SELECT 
        SUM(CAST(SUBSTRING_INDEX(reps, ',', 1) AS UNSIGNED) + 
            CASE WHEN LOCATE(',', reps) > 0 THEN 
                LENGTH(reps) - LENGTH(REPLACE(reps, ',', '')) 
            ELSE 0 END
        ) AS totaleReps,
        SUM(sets) AS totaleSets
    FROM workout_exercises 
    WHERE workout_plan_id IN (
        SELECT id FROM workout_plans WHERE user_id = ?
    )
");
if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query set/reps"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$user["totaleSetCompletati"] = $row["totaleSets"] ?? 0;
$user["totaleRepsCompletate"] = $row["totaleReps"] ?? 0;
$stmt->close();


// Esercizio più frequente
$stmt = $conn->prepare("
    SELECT e.name, COUNT(*) as frequenza 
    FROM workout_exercises we 
    JOIN exercises e ON we.exercise_id = e.id 
    JOIN workout_plans wp ON we.workout_plan_id = wp.id 
    WHERE wp.user_id = ? 
    GROUP BY e.name 
    ORDER BY frequenza DESC 
    LIMIT 1
");
if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query esercizio frequente"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user["esercizioPiuFrequente"] = $res->num_rows > 0 ? $res->fetch_assoc()["name"] : "Nessuno";
$stmt->close();

$conn->close();

echo json_encode($user);
?>
