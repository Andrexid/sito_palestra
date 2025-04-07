<?php
session_start();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/account.css">
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
            <li><a href="../index.html" class="selezionata">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('progressi.html')">Progressi</a></li>
            <li><a href="../html/faq.html">FAQ</a></li>
            <li><a href="../html/chisiamo.html">Chi siamo</a></li>
            <li><a href="../html/contatti.html">Contatti</a></li>
            <li class="profile-container">
                <a href="#">
                    <img id="profile-pic" src="" alt="Profilo">
                </a>
                <div class="dropdown-menu" id="profile-menu">
                    <a href="#" onclick="controllaAccesso('profilo.html')">👤 Profilo</a>
                    <a href="#" onclick="controllaAccesso('impostazioni.html')">⚙️ Impostazioni</a>
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
            <div class="weekly-training">
                <input type="checkbox">
                <p>Allenamento 1</p>
            </div>
            <div class="weekly-training">
                <input type="checkbox">
                <p>Allenamento 1</p>
            </div>
            <div class="weekly-training">
                <input type="checkbox">
                <p>Allenamento 1</p>
            </div>
        </div>
        <div class="training-bar-right">
            <button class="principal_button">Inizia Allenamento</button>
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
            </div>
            <div class="box">
                <?php require '../grafici/allenamenti_mensili.html'; ?>
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