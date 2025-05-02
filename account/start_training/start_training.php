<?php
session_start();
require_once("../../database/connessione.php");

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
    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../../commonCSS/buttons.css" />
    <link rel="stylesheet" href="../../commonCSS/reset.css" />
    <link rel="stylesheet" href="../../commonCSS/_variables.css">
    <link rel="stylesheet" href="../../commonCSS/footer.css" />


    <style>
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
            background-color: rgb(41, 41, 41);
            padding: 1.5rem;
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        @media (max-width: 768px) {
            .day-card {
                width: 100%;
                padding: 1.2rem;
                border-radius: 12px;
                box-sizing: border-box;
            }

            .day-title {
                font-size: 1.3rem;
                text-align: center;
            }

            .day-container {
                padding: 0.8rem;
                margin-bottom: 1.5rem;
            }

            ul.exercise-list li {
                font-size: 1rem;
                padding: 0.6rem 0;
            }

            .btn-primary {
                width: 100%;
                font-size: 1.1rem;
                padding: 0.8rem;
                margin-top: 1.2rem;
                border-radius: 10px;
            }

            /* Evita scroll orizzontale */
            html,
            body {
                max-width: 100vw;
                overflow-x: hidden;
            }
        }
    </style>

    <link rel="stylesheet" href="../../commonCSS/commonCSS.css?v=1.2">
    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
</head>

<body>

    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri Menu di Navigazione">
            ☰
        </button>

        <div class="logo">
            <a href="../../index.html">
                <img src="../../img/logo.png" alt="Logo Palestra" class="logo-img" />
            </a>
        </div>

        <!-- QUESTO È IL MENU MOBILE E DESKTOP -->
        <ul class="nav-links">
            <li><a href="../../index.html">Home</a></li>
            <li>
                <a href="#" onclick="controllaAccesso('../account.php')" data-section="Progressi" class="selezionata">Progressi</a>
            </li>
            <li><a href="../../faq/faq.html" data-section="FAQ">FAQ</a></li>
            <li><a href="../../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../../contatti/contatti.html" data-section="Contatti">Contatti</a></li>
        </ul>

        <!-- QUESTO È IL PROFILO, FUORI DAL MENU -->
        <div class="profile-container" data-section="Profile">
            <a href="#">
                <img id="profile-pic" src="../../img/utente_without_bg.png" alt="Profilo" />
            </a>
            <div class="dropdown-menu" id="profile-menu">
                <a href="#" onclick="controllaAccesso('profile.html')">👤 Profilo</a>
                <a href="#" onclick="controllaAccesso('settings.html')">⚙️ Impostazioni</a>
                <a href="#" onclick="logout()">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <h1 style="margin-top: 30px;">Allenamento suddiviso per giorno</h1>

    <?php if (empty($giorni)) : ?>
        <p>Nessun esercizio trovato.</p>
    <?php else : ?>
        <div style="margin-top: 30px; display: flex; justify-content: center; flex-wrap: wrap; gap: 2rem;">
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

    <button onClick="window.location.href = '../account.php'" class="btnBackToAccount">Torna indietro</button>

    <footer class="site-footer">
        <div class="footer-container">
            <!-- Logo / Descrizione -->
            <div class="footer-section">
                <h2 class="footer-title">GymPower</h2>
                <p class="footer-text">
                    Il tuo viaggio verso una forma fisica straordinaria inizia oggi. Con GymPower hai tutto sotto controllo: progressi, allenamenti e nuove sfide da superare!
                </p>
                <p class="footer-text">
                    Traccia i tuoi progressi. Supera i tuoi limiti. Ogni giorno.
                </p>
                <!-- Frase motivazionale extra -->
                <p class="footer-text" style="margin-top: 10px; font-style: italic; font-weight: 600;">
                    “La costanza batte il talento, quando il talento non è costante.”
                </p>
            </div>

            <!-- Link utili -->
            <div class="footer-section">
                <h3 class="footer-subtitle">Scopri</h3>
                <ul class="footer-links">
                    <li><a href="../index.html">Home</a></li>
                    <li><a href="../gamification/gamification.html">Gamification</a></li>
                    <li><a href="../chiSiamo/chisiamo.html">Chi siamo</a></li>
                    <li><a href="../faq/faq.html">FAQ</a></li>
                    <li><a href="#" onclick="controllaAccesso('account.php')">Progressi</a></li>
                    <li><a href="../contatti/contatti.html">Contatti</a></li>
                </ul>
            </div>

            <!-- Contatti / Social -->
            <div class="footer-section">
                <h3 class="footer-subtitle">Contattaci</h3>
                <p class="footer-text">Email: <a href="mailto:info@gympower.com">info@gympower.com</a></p>
            </div>
        </div>

        <!-- Footer bottom -->
        <div class="footer-bottom">
            &copy; 2025 GymPower. Tutti i diritti riservati.
        </div>
    </footer>

    <script src="../../commonJS/commonNavbar.js"></script>
    <script src="../../commonJS/navbar.js"></script>
    <script src="../../commonJS/commonScript.js"></script>

</body>

</html>