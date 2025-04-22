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
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>

<body>
<nav class="navbar">
    <div class="logo">
        <img src="../img/logo.png" alt="Logo Palestra">
    </div>
    <ul class="nav-links">
        <li><a href="../index.html">Home</a></li>
        <li><a href="#" onclick="controllaAccesso('../php/account.php')" data-section="Progressi" class="selezionata">Progressi</a></li>
        <li><a href="../html/faq.html">FAQ</a></li>
        <li><a href="../html/chisiamo.html">Chi siamo</a></li>
        <li><a href="../html/contatti.html">Contatti</a></li>
        <li class="profile-container">
            <a href="#"><img id="profile-pic" src="img/utente_without_bg.png" alt="Profilo"></a>
            <div class="dropdown-menu" id="profile-menu">
                <a href="#" onclick="controllaAccesso('profile.html', false)">👤 Profilo</a>
                <a href="#" onclick="controllaAccesso('settings.html', false)">⚙️ Impostazioni</a>
                <a href="#" onclick="logout()">🚪 Logout</a>
            </div>
        </li>
    </ul>
</nav>

<div class="cover" data-aos="fade-down">
    <div id="title">Benvenuto, <span id="nomeUtente"><?= $nome_utente ?></span></div>
</div>

<div class="training-bar" data-aos="fade-up">
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
            }
        ?>
    </div>
    <div class="training-bar-right">
        <a href="start_training.php"><button class="principal_button">Inizia Allenamento</button></a>
    </div>
</div>

<div class="container">
    <div class="container-main">
        <div class="box large-box" data-aos="zoom-in">
            <h2>Badge</h2>
            <p>Visualizza i tuoi risultati più importanti</p>
        </div>

        <div class="box large-box tall-box" data-aos="fade-up">
            <h2>Esperienza settimanale</h2>
            <?php require_once '../grafici/grafco_exp_settimanale.php'; ?>
        </div>

        <div class="box" data-aos="fade-right">
            <h2>Obiettivi</h2>
            <a href="../php/new_goal.php"><button>Inserisci un nuovo obiettivo</button></a>
            <?php require_once '../grafici/obiettivi.php'; ?>
        </div>

        <div class="box" data-aos="fade-left">
            <h2>Allenamenti Mensili</h2>
            <?php require_once '../grafici/allenamenti_mensili.php'; ?>
        </div>
    </div>
</div>

<script src="../src/commonNavbar.js?v=1.1"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
    });
</script>

</body>
</html>
