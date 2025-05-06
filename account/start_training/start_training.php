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
    <link rel="stylesheet" href="start_training.css">

    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../../commonCSS/buttons.css" />
    <link rel="stylesheet" href="../../commonCSS/reset.css" />
    <link rel="stylesheet" href="../../commonCSS/_variables.css">
    <link rel="stylesheet" href="../../commonCSS/footer.css" />
    <link rel="stylesheet" href="../../commonCSS/commonCSS.css?v=1.2">
</head>

<body>
    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri Menu di Navigazione">☰</button>
        <div class="logo">
            <a href="../../index.html">
                <img src="../../img/logo.png" alt="Logo MyGymStats" class="logo-img">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="../../index.html">Home</a></li>
            <li><a href="../account.php" class="selezionata">Progressi</a></li>
            <li><a href="../../gamification/gamification.html">Badge e punti</a></li>
            <li><a href="../../faq/faq.html">FAQ</a></li>
            <li><a href="../../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../../contatti/contatti.html">Contatti</a></li>
        </ul>
        <div class="profile-container">
            <a href="#">
                <img id="profile-pic" src="../../img/utente_without_bg.png" alt="Immagine Profilo Utente">
            </a>
            <div class="dropdown-menu" id="profile-menu">
                <a href="../../profile/profile.html">👤 Profilo</a>
                <a href="../../settings/settings.html">⚙️ Impostazioni</a>
            </div>
        </div>
    </nav>

    <h1>Allenamento suddiviso per giorno</h1>

    <?php if (empty($giorni)) : ?>
        <p>Nessun esercizio trovato.</p>
    <?php else : ?>
        <div class = "container">
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

    <a href="../account.php" class="btnBackToAccount">Torna indietro</a>

    <footer class="site-footer">
      <div class="footer-container">
        <div class="footer-section">
            <h2 class="footer-title">MyGymStats</h2>
            <p class="footer-text">Con MyGymStats puoi monitorare gli allenamenti, seguire i tuoi progressi e migliorarti ogni giorno con strumenti avanzati.</p>
            <p class="footer-text">Allenati in modo intelligente, costante e motivato ogni giorno.</p>
            <p class="footer-text quote">“La costanza batte il talento, quando il talento non è costante.”</p>
        </div>
    
        <div class="footer-section">
            <h3 class="footer-subtitle">Naviga</h3>
            <ul class="footer-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="../../gamification/gamification.html">Gamification</a></li>
            <li><a href="../../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../../faq/faq.html">FAQ</a></li>
            <li><a href="#" data-access="../../account.php">Progressi</a></li>
            <li><a href="../../contatti/contatti.html">Contatti</a></li>
            </ul>
        </div>
    
        <div class="footer-section">
            <h3 class="footer-subtitle">Contattaci</h3>
            <p class="footer-text">
            Email: <a href="mailto:info@mygymstats.com">info@mygymstats.com</a>
            </p>
        </div>
        </div>
    
        <div class="footer-bottom">
        &copy; <span id="currentYear"></span> MyGymStats. Tutti i diritti riservati.
        </div>
    </footer>
    
    <script src="../../commonJS/commonScript.js"></script>
    <script src="../../commonJS/commonNavbar.js"></script>

</body>

</html>