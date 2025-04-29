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
</head>

<body>
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
    <button onClick= "window.location.href = '../account.php'" class = "btnBackToAccount">Torna indietro</button>

    <button id="toggle-theme" class="theme-button">🌙 Modalità Scura</button>

    <script>
        function applyTheme() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
              document.body.classList.add('dark-mode');
              document.getElementById('toggle-theme').textContent = '☀️ Modalità Chiara';
            } else {
              document.body.classList.remove('dark-mode');
              document.getElementById('toggle-theme').textContent = '🌙 Modalità Scura';
            }
          }
        
          function toggleTheme() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
              localStorage.setItem('theme', 'light');
            } else {
              localStorage.setItem('theme', 'dark');
            }
            applyTheme();
          }
        
          document.addEventListener('DOMContentLoaded', function() {
            // Quando la pagina carica
            if (!localStorage.getItem('theme')) {
              // Se non c'è un tema salvato, imposta 'light' di default
              localStorage.setItem('theme', 'light');
            }
            applyTheme();
        
            // Event listener sul pulsante
            document.getElementById('toggle-theme').addEventListener('click', toggleTheme);
          });
    </script>
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
                        throw new Error('Errore nella cancellazione' . response);
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

</body>

</html>