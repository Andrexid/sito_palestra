<?php
session_start();
require_once("../../database/connessione.php");

// Controlla se il bottone è stato premuto per cambiare lo stato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['training_card_id'])) {
    $training_card_id = $_POST['training_card_id'];
}

$user_id = $_SESSION['id'];
$select_training_cards = "SELECT * FROM workout_plans WHERE user_id = ?";
$stm = $conn->prepare($select_training_cards);

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Schede</title>
    <link rel="stylesheet" href="all_training_card.css?v=1.1">

    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../../commonCSS/commonCSS.css?v=1.2">
    <link rel="stylesheet" href="../../commonCSS/footer.css" />

</head>

<body>
    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri Menu di Navigazione">
            ☰
        </button>
        <div class="logo">
            <a href="../../index.html">
                <img
                    src="../../img/logo.png"
                    alt="Logo MyGymStats"
                    class="logo-img" />
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="../../index.html">Home</a></li>
            <li><a href="../account.php" class="selezionata">Progressi</a></li>
            <li>
                <a href="../../gamification/gamification.html">Badge e punti</a>
            </li>
            <li><a href="../../faq/faq.html">FAQ</a></li>
            <li><a href="../../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../../contatti/contatti.html">Contatti</a></li>
        </ul>
        <div class="profile-container">
            <a href="#">
                <img
                    id="profile-pic"
                    src="../../img/utente_without_bg.png"
                    alt="Immagine Profilo Utente" />
            </a>
            <div class="dropdown-menu" id="profile-menu">
                <a href="../../profile/profile.html">👤 Profilo</a>
                <a href="../../settings/settings.html">⚙️ Impostazioni</a>
            </div>
        </div>
    </nav>

    <?php
    if ($stm) {
        $stm->bind_param("i", $user_id);
        $stm->execute();
        $result = $stm->get_result();

        if ($result->num_rows > 0) {
            echo "<h1>Tutte le tue schede</h1>";
            echo "<div class='all-training-cards'>";
            $counter = 1;
            while ($row = $result->fetch_assoc()) {

                $training_card_id = $row['id'];

                echo "<div class='training-card'>
                <h2>Scheda n°: " . $counter . "</h2><br>
                <div class='expiration-cont'>
                    Scadenza: 
                        <div class='expiration'>" . $row['end_date'] . "
                    </div>
                </div> 
                <br>
                <a href='#' id = 'deleteCard' data-id='<?= json_encode($row['id']) ?>'><button type='button' class='input-button'>Elimina Scheda</button></a>
                <a href='./single_training_card.php?id=" . $row['id'] . "'><button class='secondary-button' type='button'>Guarda i Progressi</button></a>
    
                </div>";
                $counter += 1;
            }
            echo "</div>";
        } else {
            echo "<h1>Nessuna scheda trovata per questo utente.</h1><br>
            <div class = 'nuovaScheda'>
                <a href='../new_training_card/new_training_card.html'><button class='input-button'>Inserisci una nuova scheda</button></a>
            </div>";
        }
        $stm->close();
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
    ?>
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

    <script src = "all_training_card.js?v=1.1"></script>
    <script src="../../commonJS/commonScript.js?v=1.1"></script>
    <script src="../../commonJS/commonNavbar.js?v=1.1"></script>

</body>

</html>