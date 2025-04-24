<?php
session_start();
require_once '../database/connessione.php';

$user_id = $_SESSION['id'] ?? null;
$nome_utente = "";

if ($user_id) {
    $sql = "SELECT nome, cognome FROM utenti WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $nome_utente = htmlspecialchars($row['nome'] . ' ' . $row['cognome']);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="account.css?v=1.2">

    <link rel="stylesheet" href="../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../commonCSS/buttons.css" />
    <link rel="stylesheet" href="../commonCSS/reset.css" />
    <link rel="stylesheet" href="../commonCSS/_variables.css">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=settings" />
</head>

<body onload="getUserDataProfile()">
    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri Menu di Navigazione">☰</button>
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('account.php')" data-section="Progressi" class="selezionata">Progressi</a></li>
            <li><a href="../faq/faq.html"  data-section="FAQ">FAQ</a></li>
            <li><a href="../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../contatti/contatti.html" data-section="Contatti">Contatti</a></li>
            <li class = "profile-container" data-section="Profile">
                <a href="#">
                    <img id="profile-pic" src="../img/utente_without_bg.png" alt="Profilo">
                </a>
                <div class="dropdown-menu" id="profile-menu">
                    <a href="#" onclick="controllaAccesso('profile.html')">👤 Profilo</a>
                    <a href="#" onclick="controllaAccesso('settings.html')">⚙️ Impostazioni</a>
                    <a href="#" onclick="logout()">🚪 Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <div class="cover">
        <div id="title">Benvenuto, <span id="nomeUtente"><?= $nome_utente ?></span></div>
    </div>


    <div class="training-bar">
        <div class="training-bar-left">
            <a href="new_training_card/new_training_card.html"><button class="principal_button">Aggiungi Scheda</button></a>
            <a href="all_training_card/all_training_card.php"><button class="principal_button">Visualizza Schede</button></a>
        </div>
        <div class="training-bar-center">
            <h2>Questa Settimana</h2>
            <?php
            $sql = "SELECT workouts_per_week, workouts_done FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
            $stm = $conn->prepare($sql);

            if ($stm) {
                $stm->bind_param("i", $user_id);
                $stm->execute();
                $result = $stm->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $done = (int)$row['workouts_done'];
                    $total = (int)$row['workouts_per_week'];
                    $percent = $total > 0 ? round(($done / $total) * 100) : 0;

                    echo "<p class='progress-text'>$done/$total <span class='percent-text'>($percent%)</span></p>";
                } else {
                    echo "Nessuna scheda trovata per questo utente.";
                }
                $stm->close();
            } else {
                echo "Errore nella preparazione della query: " . $conn->error;
            }
            ?>
        </div>
        <div class="training-bar-right">
            <a href="start_training/start_training.php"><button class="principal_button">Inizia Allenamento</button></a>
        </div>
    </div>

    <div class="container">
        <div class="container-main">
            <div class="box large-box">
                <div class="gamification-container-account">
                    <p class="gamification-text-account">Sei al <strong>Livello 3</strong> 💪</p>
                    <p id="nTrainings">Hai completato <strong>42</strong> allenamenti! 🚀</p>
                    <br><br>
                    <label for="progressGoals">Punti per superare la tua attuale lega:</label><br>
                    <progress id="progressGoals" max="100" value="70">170%</progress>

                    <div id="badge-container" class="badge-container">
                        <div class="container-img">
                            <img src="" id="firstImg">
                            <p id="subOne"></p>
                        </div>
                        <div class="container-img">
                            <img src="" id="secondImg">
                            <p id="subTwo"></p>
                        </div>
                        <div class="container-img">
                            <img src="" id="thirdImg" class="locked">
                            <p id="subThree"></p>
                        </div>
                        <!-- <img src = "../img/badge-0.jpg"> -->
                    </div>
                </div>
            </div>
            <div class="box large-box tall-box"> <br>
                <?php require_once 'grafico_exp_settimanale.php' ?>
            </div>
            <div class="box">
                <h2>Obiettivi</h2>
                <a href="new_goal.php"><button class="principal_button">Inserisci un nuovo obiettivo</button></a>
                <?php require_once 'grafico_obiettivi.php'; ?>
            </div>
            <div class="box">
                <?php require_once 'grafico_allenamenti_mensili.php'; ?>
            </div>
        </div>
    </div>

    <script src="../commonJS/commonNavbar.js"></script>
    <script src="account.js"></script>
</body>

</html>