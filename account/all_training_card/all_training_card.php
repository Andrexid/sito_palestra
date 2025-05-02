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
                <a href='#' onclick='eliminazione(" . $row['id'] . ")'><button type='button' class='input-button'>Elimina Scheda</button></a>
                <a href='./single_training_card.php?id=" . $row['id'] . "'><button class='secondary-button' type='button'>Visualizza scheda</button></a>
    
                </div>";
                $counter += 1;
            }
            echo "</div>";
        } else {
            echo "<h1>Nessuna scheda trovata per questo utente.</h1><br>
            <div style='max-width: 15%; margin: auto'>
                <a href='../new_training_card/new_training_card.html'><button class='input-button'>Inserisci una nuova scheda</button></a>
            </div>";
        }
        $stm->close();
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
    ?>
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

    <script>
        var expirationText = document.querySelectorAll('.expiration');

        // Ottieni la data di oggi
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Rimuove l'orario per un confronto solo sulla data

        expirationText.forEach(element => {
            // Converti expiration in un oggetto Date
            var expirationDate = new Date(element.innerHTML);

            // Controllo che la scheda sia scaduta
            if (expirationDate < today) {
                console.log("Scheda scaduta");
                element.style.color = "red"; // Evidenzia se scaduta
            } else {
                console.log("Scheda ancora valida");
            }
        });


        function eliminazione(cardId) {
            if (confirm("Vuoi davvero eliminare questa scheda di allenamento?")) {
                // Effettua una richiesta fetch per eliminare la scheda
                fetch(`./delete_training_card.php?id_card=${cardId}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            alert("Scheda eliminata con successo!");
                            // Ricarica la pagina per aggiornare la lista
                            // window.location.reload();
                            // window.location.href=window.location.href
                        } else {
                            throw new Error('Errore nella cancellazione'.response);
                        }
                    })
                    .catch(error => {
                        console.error('Errore:', error);
                        alert("Si è verificato un errore durante l'eliminazione");
                    });
            } else {
                alert("Eliminazione annullata");
            }
        }

        // Rende la funzione disponibile globalmente
        window.eliminazione = eliminazione;
    </script>
    <script src="../../commonJS/commonScript.js"></script>
    <script src="../../commonJS/commonNavbar.js"></script>
    <script src="../../commonJS/navbar.js"></script>

    <script>
        
    </script>


</body>

</html>