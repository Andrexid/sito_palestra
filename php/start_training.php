<?php
session_start();
require_once("../database/connessione.php");

$user_id = $_SESSION['id'];

// Recupera l'ID dell'ultima scheda creata dall'utente
$sql = "SELECT id FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($workout_plan_id);
$stmt->fetch();
$stmt->close();

$giorni = []; // array associativo con chiave = giorno, valore = array di esercizi

// Recupera esercizi con giorno associato
$sql = "SELECT e.name, we.exercise_day 
        FROM workout_exercises we
        JOIN exercises e ON we.exercise_id = e.id
        WHERE we.workout_plan_id = ?
        ORDER BY we.exercise_day";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workout_plan_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $day = $row['exercise_day'];
    $name = $row['name'];
    $giorni[$day][] = $name;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Allenamento Giornaliero</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/buttons.css">
    <link rel="stylesheet" href="../css/_variables.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
        }
        .day-container {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 2px solid var(--primary-color);
            border-radius: 10px;
        }
        .day-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        ul.exercise-list {
            list-style: none;
            padding-left: 0;
        }
        ul.exercise-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>

    <h1>Allenamento suddiviso per giorno</h1>

    <?php if (empty($giorni)) : ?>
        <p>Nessun esercizio trovato.</p>
    <?php else : ?>
        <?php foreach ($giorni as $giorno => $esercizi): ?>
            <div class="day-container">
                <div class="day-title">Giorno <?php echo $giorno; ?></div>
                <ul class="exercise-list">
                    <?php foreach ($esercizi as $esercizio): ?>
                        <li><?php echo htmlspecialchars($esercizio); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>