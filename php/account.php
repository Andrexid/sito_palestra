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
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/account.css?v=1.2">
    <link rel="stylesheet" href="../css/commonNavbar.css">
    <link rel="stylesheet" href="../css/buttons.css">
    <link rel="stylesheet" href="../css/_variables.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=settings" />
</head>

<body>
<nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('../php/account.php')" data-section="Progressi"  class="selezionata">Progressi</a></li>
            <li><a href="../html/faq.html" data-section="FAQ">FAQ</a></li>
            <li><a href="../html/chisiamo.html">Chi siamo</a></li>
            <li><a href="../html/contatti.html" data-section="Contatti">Contatti</a></li>
            <li class = "profile-container" data-section="Profile">
                <a href="#">
                    <img id="profile-pic" src="img/utente_without_bg.png" alt="Profilo">
                </a>
                <div class="dropdown-menu" id="profile-menu">
                    <a href="#" onclick="controllaAccesso('profile.html', false)">👤 Profilo</a>
                    <a href="#" onclick="controllaAccesso('settings.html', false)">⚙️ Impostazioni</a>
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
            <a href="../html/new_training_card.html"><button>Aggiungi Scheda</button></a>
            <a href="./all_training_card.php"><button>Visualizza Schede</button></a>
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
            <a href = "start_training.php"><button class="principal_button">Inizia Allenamento</button></a>
        </div>
    </div>

    <div class="container">
        <div class="container-main">
            <div class="box large-box">
                bedge
            </div>
            <!-- <div class="box info" id="notification-box"> -->
            <!-- <div class="notification-content">
                    <h2>Notifiche e scadenze</h2>
                    <div class="notification-buttons">
                        <div class="notification-button-and-settings">
                            <button>Notifica 1</button>
                            <span class="material-symbols-outlined">
                                settings
                            </span>
                        </div>
                        <p>scade il ??/??/???</p>
                        <div class="notification-button-and-settings">
                            <button>Notifica 1</button>
                            <span class="material-symbols-outlined">
                                settings
                            </span>
                        </div>
                        <p>scade il ??/??/???</p>
                        <div class="notification-button-and-settings">
                            <button>Notifica 1</button>
                            <span class="material-symbols-outlined">
                                settings
                            </span>
                        </div>
                        <p>scade il ??/??/???</p>
                    </div>
                </div> -->
            <!-- </div> -->
            <div class="box large-box tall-box"> <br>
                <?php require_once '../grafici/grafco_exp_settimanale.php' ?>
            </div>
            <div class="box">
                <h2>Obiettivi</h2>
                <a href="../php/new_goal.php"><button>Inserisci un nuovo obiettivo</button></a>
                <?php require_once '../grafici/obiettivi.php'; ?>
            </div>
            <div class="box">
                <?php require_once '../grafici/allenamenti_mensili.php'; ?>
            </div>
        </div>
    </div>

    <script src="../src/commonNavbar.js?v=1.1"></script>
    <!-- <script src="../src/callAjaxAccount.js"></script> -->
    <!-- <script>
        document.addEventListener("DOMContentLoaded", () => {
            const hamburger = document.getElementById('hamburger');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            const closeBtn = document.getElementById('close-btn');
            hamburger.addEventListener('click', () => {
                sidebar.style.left = '0';
                overlay.style.display = 'block';
            });

            overlay.addEventListener('click', () => {
                sidebar.style.left = '-350px';
                overlay.style.display = 'none';
            });

            closeBtn.addEventListener('click', () => {
                sidebar.style.left = '-350px';
                overlay.style.display = 'none';
            });
        });
    </script> -->


</body>

</html>