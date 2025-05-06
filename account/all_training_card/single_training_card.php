<?php
session_start();
require_once("../../database/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['training_card_id'])) {
    $training_card_id = $_POST['training_card_id'];
}

if (isset($_GET['id'])) {
    $card_id = $_GET['id'];

    // Query workout plan
    $search_card = "SELECT * FROM workout_plans WHERE id = ?";
    $stm = $conn->prepare($search_card);

    // Query con JOIN per ottenere nome esercizio
    $search_workout_exercises = "
        SELECT we.*, e.name 
        FROM workout_exercises we 
        JOIN exercises e ON we.exercise_id = e.id 
        WHERE we.workout_plan_id = ?
    ";
    $stm2 = $conn->prepare($search_workout_exercises);

    // Query info scheda
    $search_info_card = "SELECT * FROM workout_exercises WHERE id = ? and workout_plan_id = ?";
    $stm_info = $conn->prepare($search_info_card);
} else {
    echo "Errore, id non trovato";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Scheda numero: <?php echo $card_id; ?></title>
    <link rel="stylesheet" href="single_training_card.css?v=1.1">

    <link rel="stylesheet" href="../../commonCSS/commonCSS.css?v=1.2">
    <link rel="stylesheet" href="../../commonCSS/footer.css" />
    <link rel="stylesheet" href="../../commonCSS/reset.css" />
    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
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
                <a href="javascript:void(0);" id="logout-btn">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <?php
    if ($stm && $stm2) {
        $stm->bind_param("i", $card_id);
        $stm->execute();
        $result = $stm->get_result();

        $stm2->bind_param("i", $card_id);
        $stm2->execute();
        $result2 = $stm2->get_result();

        if ($result->num_rows > 0) {
            echo "<h1 style='margin-top: 30px'>Scheda Allenamento</h1>";

            // Bottone per abilitare/disabilitare modifica
            echo "<button class='unlock-button secondary-button' onclick='unLockInputs()'>Sblocca Modifica</button>";

            echo "<form action='./refresh_workout_exercises.php' method='post' class='training-form'>
                <input type='hidden' name='training_card_id' value='$card_id'>
                <table border='1' class='table-single-card'>
                    <thead>
                        <tr>
                            <th>Esercizio</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Peso (kg)</th>
                            <th>Recupero (sec)</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($exercise = $result2->fetch_assoc()) {
                echo "<tr>
                    <td>{$exercise['name']}</td>
                    <td><input type='number' disabled name='sets[{$exercise['id']}]' value='{$exercise['sets']}' class='input-text-dis'></td>
                    <td><input type='number' disabled name='reps[{$exercise['id']}]' value='{$exercise['reps']}' class='input-text-dis'></td>
                    <td><input type='number' disabled name='weight[{$exercise['id']}]' value='{$exercise['weight']}' class='input-text-dis'></td>
                    <td><input type='number' disabled name='rest_time[{$exercise['id']}]' value='{$exercise['rest_time']}' class='input-text-dis'></td>
                    <td><input type='text' disabled name='notes[{$exercise['id']}]' value='{$exercise['notes']}' class='input-text-dis'></td>
                </tr>";
            }

            echo "</tbody>
                </table>
                
                <!-- Bottone per salvare le modifiche -->
                <div class='button-wrapper'>
                    <input type='submit' disabled value='Salva Modifiche' class='principal_button-sm'>
                </div>
            </form>";




            echo "<h2 style='margin-top: 40px;'>📅 Storico Esercizi Registrati</h2>";

            if ($result2->num_rows > 0) {
                // Riavvolgi il risultato per riutilizzarlo
                $stm2->bind_param("i", $card_id);
                $stm2->execute();
                $result2 = $stm2->get_result();

                echo "<table border='1' class='table-single-card'>
        <thead>
            <tr>
                <th>Esercizio</th>
                <th>Set</th>
                <th>Reps</th>
                <th>Peso (kg)</th>
                <th>Recupero (sec)</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>";

                while ($exercise = $result2->fetch_assoc()) {
                    echo "<tr>
                        <td>{$exercise['name']}</td>
                        <td>{$exercise['sets']}</td>
                        <td>{$exercise['reps']}</td>
                        <td>{$exercise['weight']}</td>
                        <td>{$exercise['rest_time']}</td>
                        <td>{$exercise['notes']}</td>
                    </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Nessun esercizio registrato per questa scheda.</p>";
            }
        } else {
            echo "<h1>Errore: Nessuna scheda trovata</h1>";
        }

        $stm->close();
        $stm2->close();
    } else {
        echo "Errore nella preparazione delle query.";
    }
    ?>

    <a href="../account.php" class="btnBackToAccount">Torna indietro</a>


    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h2 class="footer-title">MyGymStats</h2>
                <p class="footer-text">Con MyGymStats puoi monitorare gli allenamenti, seguire i tuoi progressi e migliorarti ogni giorno con strumenti avanzati.</p>
                <p class="footer-text">Allenati in modo intelligente, costante e motivato ogni giorno.</p>
                <p class="footer-text" style="margin-top: 10px; font-style: italic; font-weight: 600">“La costanza batte il talento, quando il talento non è costante.”</p>
            </div>

            <div class="footer-section">
                <h3 class="footer-subtitle">Naviga</h3>
                <ul class="footer-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="./gamification/gamification.html">Gamification</a></li>
                    <li><a href="./chiSiamo/chisiamo.html">Chi siamo</a></li>
                    <li><a href="./faq/faq.html">FAQ</a></li>
                    <li><a href="#" onclick="controllaAccesso('account.php')">Progressi</a></li>
                    <li><a href="./contatti/contatti.html">Contatti</a></li>
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
            &copy; 2025 MyGymStats. Tutti i diritti riservati.
        </div>
    </footer>


    <script>
        const inputs = document.querySelectorAll(".input-text-dis");
        const unlockButton = document.querySelector(".unlock-button");
        const submitButton = document.querySelector("input[type='submit']");

        let isDisabled = true;

        function unLockInputs() {
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });

            isDisabled = !isDisabled;

            // Attiva/disattiva il bottone submit
            submitButton.disabled = !submitButton.disabled;

            // Cambia testo del bottone di sblocco
            unlockButton.textContent = isDisabled ? "Sblocca Modifica" : "Blocca Modifica";
        }
    </script>
    <script src="../../commonJS/commonScript.js"></script>
    <script src="../../commonJS/commonNavbar.js"></script>

</body>

</html>

<?php
require_once("../../database/close-connessione.php");
?>