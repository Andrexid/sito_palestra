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
    <link rel="stylesheet" href="../css/commonNavbar.css">
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

        .day-card {
            background-color:rgb(41, 41, 41);
            padding: 1.5rem;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .day-card:hover {
            transform: scale(1.03);
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn-primary:hover {
            background-color: #005bb5;
        }

    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html" class="selezionata">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('progressi.html')">Progressi</a></li>
            <li><a href="faq.html">FAQ</a></li>
            <li><a href="contatti.html">Contatti</a></li>
            <li class="profile-container">
                <a href="#">
                    <img id="profile-pic" src="" alt="Profilo">
                </a>
                <div class="dropdown-menu" id="profile-menu">
                    <a href="#" onclick="controllaAccesso('profilo.html')">👤 Profilo</a>
                    <a href="#" onclick="controllaAccesso('settings.html')">⚙️ Impostazioni</a>
                    <a href="#" onclick="logout()">🚪 Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <h1>Allenamento suddiviso per giorno</h1>

    <?php if (empty($giorni)) : ?>
        <p>Nessun esercizio trovato.</p>
    <?php else : ?>
        <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
            <?php foreach ($giorni as $giorno => $esercizi): ?>
                <div class="day-card">
                    <div class="day-title">Giorno <?php echo $giorno; ?></div>
                    <ul class="exercise-list">
                        <?php foreach ($esercizi as $esercizio): ?>
                            <li><?php echo htmlspecialchars($esercizio); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <form action="exercise_start_day.php" method="GET">
                        <input type="hidden" name="day" value="<?php echo $giorno; ?>">
                        <button class="btn-primary" type="submit">Inizia questo giorno</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>