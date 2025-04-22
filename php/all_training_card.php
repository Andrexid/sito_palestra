<?php
session_start();
require_once("../database/connessione.php");

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
    <link rel="stylesheet" href="../css/all_training_card.css">
    <link rel="stylesheet" href="../css/commonNavbar.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('progressi.html')" class="selezionata">Progressi</a></li>
            <li><a href="faq.html">FAQ</a></li>
            <li><a href="contatti.html">Contatti</a></li>
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
                <a href='./delete_training_card.php?id_card=" . $row['id'] . "'><button onclick='eliminazione()' type='button' class='input-button'>Elimina Scheda</button></a>
                <a><button class='secondary-button' type='button'>Modifica Scheda</button></a>
                <a href='./single_training_card.php?id=" . $row['id'] . "'><button class='secondary-button' type='button'>Visualizza scheda</button></a>
    
                </div>";
                $counter += 1;
            }
            echo "</div>";
        } else {
            echo "<h1>Nessuna scheda trovata per questo utente.</h1><br>
            <div style='max-width: 15%; margin: auto'>
                <a href='../html/new_training_card.html'><button class='input-button'>Inserisci una nuova scheda</button></a>
            </div>";
            
        }
        $stm->close();
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
    ?>

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


        function eliminazione() {
            if (confirm("Vuoi davvero effettuare eliminare la Scheda?")) {
                alert("Eliminazione effettuata!");
            } else {
                alert("Operazione annullata, Scheda NON eliminata");
            }
        }

        // Rende la funzione disponibile globalmente
        window.eliminazione = eliminazione;
    </script>

</body>

</html>

<?php
require_once("../database/close-connessione.php");
?>