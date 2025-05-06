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
                $nome = $row['nome'] ?? '';
                $cognome = $row['cognome'] ?? '';
                $nome_utente = htmlspecialchars(trim($nome . ' ' . $cognome));
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
    <title>MyGymStats – Il Tuo Profilo Fitness: Progressi, Obiettivi e Allenamenti</title>
    <meta name="description" content="Accedi al tuo profilo MyGymStats per monitorare i tuoi progressi, gestire gli allenamenti e raggiungere nuovi obiettivi fitness.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="MyGymStats Team">

    <meta property="og:title" content="MyGymStats – Il Tuo Profilo Fitness">
    <meta property="og:description" content="Monitora i tuoi progressi, gestisci gli allenamenti e raggiungi nuovi obiettivi con MyGymStats.">
    <meta property="og:url" content="https://www.mygymstats.com/account/account.php">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="it_IT">
    <link rel="canonical" href="https://www.mygymstats.com/account/account.php">

    <link rel="stylesheet" href="account.css?v=1.93">
    <link rel="stylesheet" href="../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../commonCSS/reset.css">
    <link rel="stylesheet" href="../commonCSS/buttons.css">
    <link rel="stylesheet" href="../commonCSS/footer.css">
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap"> -->
</head>
<body>
    <nav class="navbar" aria-label="Menu di navigazione principale">
        <button class="hamburger-menu" aria-label="Apri menu di navigazione">☰</button>
    
        <div class="logo">
        <a href="../index.html">
            <img src="../img/logo.png" alt="Logo MyGymStats" class="logo-img" />
        </a>
        </div>
    
        <ul class="nav-links">
        <li><a href="../index.html">Home</a></li>
        <li><a href="javascript:void(0);" data-access="account.php" class="selezionata">Progressi</a></li>
        <li><a href="../gamification/gamification.html">Badge e punti</a></li>
        <li><a href="../faq/faq.html" data-section="FAQ">FAQ</a></li>
        <li><a href="../chiSiamo/chisiamo.html">Chi siamo</a></li>
        <li><a href="../contatti/contatti.html" data-section="Contatti">Contatti</a></li>
        </ul>
    
        <div class="profile-container" data-section="Profilo">
        <a href="#"><img id="profile-pic" src="../img/utente_without_bg.png" alt="Foto profilo utente" /></a>
        <div class="dropdown-menu" id="profile-menu">
            <a href="javascript:void(0);" data-access="profile.html">👤 Profilo</a>
            <a href="javascript:void(0);" data-access="settings.html">⚙️ Impostazioni</a>
            <a href="javascript:void(0);" id="logout-btn">🚪 Logout</a>
        </div>
        </div>
    </nav> 

    <main>
        <section class="cover">
            <h1 id="title">Benvenuto, <span id="nomeUtente"><?php echo $nome_utente; ?></span></h1>
        </section>

        <section class="ai-coming-soon">
            <h2>🤖 Intelligenza Artificiale in Arrivo</h2>
            <p>Presto potrai allenarti con un assistente intelligente al tuo fianco! L'AI analizzerà i tuoi dati, ti darà consigli su misura e ti aiuterà a superare i tuoi limiti 🚀</p>
            <p class="coming-soon-text">✨ Stiamo lavorando sodo per offrirti il massimo. Resta connesso, il meglio deve ancora arrivare!</p>
            <img src="../img/ai_illustration.jpg" alt="Illustrazione Intelligenza Artificiale in sviluppo" class="ai-img">
        </section>

        <section class="double-box">
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
                    </div>
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
                    <div class="gamification-container">
                        <h3>🏆 Obiettivi & Livelli</h3>
                        <p id="gamification-text" class="gamification-text">Sei al <strong>Livello 3</strong> 💪</p>
                        <p id="nTrainings">Hai completato <strong>42</strong> allenamenti! 🚀</p>
                        <label for="progressGoals">Sei sempre più vicino al prossimo traguardo! 💪 Continua ad allenarti e supera i tuoi limiti!</label>
                        <progress id="progressGoals" max="100" value="70"></progress>
                        <p id="ci">70%</p>
                        <div id="badge-container" class="badge-container">
                            <div>
                                <h3>🏅 Il tuo Badge Attuale:</h3>
                                <img src="../img/badge-1.jpg" id="secondImg" alt="Badge Attuale Livello 3" class="bedge-img">
                                <p id="secondP">Livello 3 – Atleta Determinato</p>
                            </div>
                            <div>
                                <h3>🚀 Prossimo Obiettivo:</h3>
                                <img src="../img/badge-2.jpg" id="thirdImg" class="locked bedge-img" alt="Badge Livello 4 Bloccato">
                                <p id="thirdP">Livello 4 – Campione in Ascesa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container-box">
            <div class="box">
                <?php require_once 'grafici/grafico_exp_settimanale.php'; ?>
            </div>
        </section>

        <section class="double-box">
            <div class="box-100">
                <h2>Obiettivi</h2>
                <button id = "insertGoal" class="principal_button">Inserisci un nuovo obiettivo</button>
                <?php require_once 'grafici/grafico_obiettivi.php'; ?>
            </div>
            <div class="box-100">
                <?php require_once 'grafici/grafico_allenamenti_mensili.php'; ?>
            </div>
        </section>
    </main>

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
            <li><a href="../gamification/gamification.html">Gamification</a></li>
            <li><a href="../chiSiamo/chisiamo.html">Chi siamo</a></li>
            <li><a href="../faq/faq.html">FAQ</a></li>
            <li><a href="#" data-access="../account.php">Progressi</a></li>
            <li><a href="../contatti/contatti.html">Contatti</a></li>
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

    <script src="../commonJS/commonNavbar.js?v=1.2"></script>
    <script src="account.js?v=1.2"></script>
    <script src="../commonJS/commonScript.js?v=1.3"></script>
</body>

</html>