<?php
session_start();
require_once '../database/connessione.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login-signup/login.html");
    exit();
}

$user_id = $_SESSION['id'];

$nome_utente = "";

if ($user_id) {
    $sql = "SELECT nome, cognome FROM utenti WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row) {
                $nome_utente = htmlspecialchars($row['nome'] . ' ' . $row['cognome']);
            }
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
    <link rel="stylesheet" href="account.css?v=1.6">

    <link rel="stylesheet" href="../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../commonCSS/reset.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=settings" />
    <script src="../login-signup/sessionCheck.js"></script>
</head>

<body onload="getUserDataProfile()">
    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri Menu di Navigazione">
            ☰
        </button>

        <div class="logo">
            <a href="../index.html">
                <img src="../img/logo.png" alt="Logo Palestra" class="logo-img" />
            </a>
        </div>

        <!-- QUESTO È IL MENU MOBILE E DESKTOP -->
        <ul class="nav-links">
            <li><a href="../index.html">Home</a></li>
            <li>
                <a href="#" onclick="controllaAccesso('../account/account.php')" data-section="Progressi" class="selezionata">Progressi</a>
            </li>
            <li><a href="../faq/faq.html" data-section="FAQ">FAQ</a></li>
            <li><a href="../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../contatti/contatti.html" data-section="Contatti">Contatti</a></li>
        </ul>

        <!-- QUESTO È IL PROFILO, FUORI DAL MENU -->
        <div class="profile-container" data-section="Profile">
            <a href="#">
                <img id="profile-pic" src="../img/utente_without_bg.png" alt="Profilo" />
            </a>
            <div class="dropdown-menu" id="profile-menu">
                <a href="#" onclick="controllaAccesso('profile.html')">👤 Profilo</a>
                <a href="#" onclick="controllaAccesso('settings.html')">⚙️ Impostazioni</a>
                <a href="#" onclick="logout()">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <div class="cover">
        <div id="title">Benvenuto <span id="nomeUtente"><?= $nome_utente ?></span></div>
    </div>


    <div class="double-box">
        <div class="box box-30">
            <div class="training-bar">
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
                            echo "<p class='no-data'>Nessuna scheda trovata per questo utente.</p>";
                        }
                        $stm->close();
                    } else {
                        echo "<p class='error-message'>Errore nella preparazione della query: " . $conn->error . "</p>";
                    }
                    ?>
                </div><br><br>

                <div class="training-bar-actions">
                    <div class="training-bar-left">
                        <a href="new_training_card/new_training_card.html"><button class="principal_button">➕ Aggiungi Scheda</button></a>
                        <a href="all_training_card/all_training_card.php"><button class="principal_button">📑 Visualizza Schede</button></a>
                    </div>
                    <div class="training-bar-right">
                        <a href="start_training/start_training.php"><button class="principal_button highlight">🔥 Inizia Allenamento!</button></a>
                    </div>
                </div>
            </div>
        </div>


        <div class="box box-70">
            <div class="profile-container-header">
                <!-- NUOVA SEZIONE GAMIFICATION -->
                <div class="gamification-container">
                    <h3>🏆 Obiettivi & Livelli</h3>
                    <p id="gamification-text" class="gamification-text">Sei al <strong>Livello 3</strong> 💪</p>
                    <p id="nTrainings">Hai completato <strong>42</strong> allenamenti! 🚀</p>
                    <br><br>
                    <label for="progressGoals">Sei sempre più vicino al prossimo traguardo! 💪 Continua ad allenarti e supera i tuoi limiti!</label><br>
                    <progress id="progressGoals" max="100" value="70"></progress>
                    <p id="ci">0%</p>

                    <div id="badge-container" class="badge-container">
                        <div>
                            <h3>🏅 Il tuo Badge Attuale:</h3>
                            <img src="../img/badge-1.jpg" id="secondImg" alt="Il tuo badge attuale" class="bedge-img">
                            <p id="secondP"></p>
                        </div>
                        <div>
                            <h3>🚀 Prossimo Obiettivo:</h3>
                            <img src="../img/badge-2.jpg" id="thirdImg" class="locked bedge-img" alt="Badge successivo">
                            <p id="thirdP"></p>
                        </div>
                    </div>
                </div>
                <!-- FINE SEZIONE GAMIFICATION -->
            </div>
        </div>
    </div>

    <div class="container-box">
        <div class="box">
            <?php require_once 'grafico_exp_settimanale.php' ?>
        </div>
    </div>
    
    <div class="double-box">
        <div class="box-100">
            <h2>Obiettivi</h2>
            <a href="new_goal.php"><button class="principal_button">Inserisci un nuovo obiettivo</button></a>
            <?php require_once 'grafico_obiettivi.php'; ?>
        </div>
        <div class="box-100">
            <?php require_once 'grafico_allenamenti_mensili.php'; ?>
        </div>
    </div>

    <button id="toggle-theme" class="theme-button">🌙 Modalità Scura</button>

    <script>
        function applyTheme() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                document.getElementById('toggle-theme').textContent = '☀️ Modalità Chiara';
            } else {
                document.body.classList.remove('dark-mode');
                document.getElementById('toggle-theme').textContent = '🌙 Modalità Scura';
            }
        }

        function toggleTheme() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                localStorage.setItem('theme', 'light');
            } else {
                localStorage.setItem('theme', 'dark');
            }
            applyTheme();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Quando la pagina carica
            if (!localStorage.getItem('theme')) {
                // Se non c'è un tema salvato, imposta 'light' di default
                localStorage.setItem('theme', 'light');
            }
            applyTheme();

            // Event listener sul pulsante
            document.getElementById('toggle-theme').addEventListener('click', toggleTheme);
        });
    </script>

    <script src="../commonJS/navbar.js"></script>
    <script src="../commonJS/commonNavbar.js"></script>
    <script src="account.js"></script>
</body>

</html>