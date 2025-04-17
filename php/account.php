<?php
session_start();
$user_id = $_SESSION['id'];
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
    <?php require '../database/connessione.php'; ?>
    <?php echo $_SESSION['id'] ?>

    <nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="#" onclick="#" class="selezionata">Progressi</a></li>
            <li><a href="../html/faq.html">FAQ</a></li>
            <li><a href="../html/chisiamo.html">Chi siamo</a></li>
            <li><a href="../html/contatti.html">Contatti</a></li>

            <li class="profile-container">
                <a href="#">
                    <img id="profile-pic" src="" alt="Profilo">
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
        <div id="title">Benvenuto, <span id="nomeUtente"></span></div>
    </div>


    <div class="training-bar">
        <div class="training-bar-left">
            <a href="../html/new_training_card.html"><button class="principal_button">Aggiungi Scheda</button></a>
            <a href="./all_training_card.php"><button class="principal_button">Visualizza Schede</button></a>
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
            <div class="double-box">
                <div class="box">
                    bedge
                </div>
                <div class="box info" id="notification-box">
                    <div class="notification-content">
                        <h2>Notifiche e scadenze</h2>
                        <div class="notification-buttons">
                            <div class="notification-button-and-settings">
                                <button class="principal_button">Notifica 1</button>
                                <span class="material-symbols-outlined">
                                    settings
                                </span>
                            </div>
                            <p>scade il ??/??/???</p>
                            <div class="notification-button-and-settings">
                                <button class="principal_button">Notifica 1</button>
                                <span class="material-symbols-outlined">
                                    settings
                                </span>
                            </div>
                            <p>scade il ??/??/???</p>
                            <div class="notification-button-and-settings">
                                <button class="principal_button">Notifica 1</button>
                                <span class="material-symbols-outlined">
                                    settings
                                </span>
                            </div>
                            <p>scade il ??/??/???</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="box">
                <h2>Obiettivi</h2>
                <?php require '../grafici/obiettivi.html'; ?>
                <br>
            </div><br>
            <div class="box">
                <?php require 'grafici/allenamenti_mensili.php'; ?>
            </div>

            <div class="box large-box tall-box"> <br>
                <?php require '../grafici/andamento_settimanale.html' ?>
            </div>
        </div>
    </div>


    <script src="../src/callAjaxAccount.js"></script>
    <script>
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
    </script>

</body>

</html>