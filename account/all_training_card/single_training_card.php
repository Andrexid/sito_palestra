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
    <?php
    if ($stm && $stm2) {
        $stm->bind_param("i", $card_id);
        $stm->execute();
        $result = $stm->get_result();

        $stm2->bind_param("i", $card_id);
        $stm2->execute();
        $result2 = $stm2->get_result();

        if ($result->num_rows > 0) {
            echo "<h1>📅 Storico Esercizi Registrati</h1>";

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


    <script src = "single_training_card.js?v=1.1"></script>
    <script src="../../commonJS/commonScript.js?v=1.1"></script>
    <script src="../../commonJS/commonNavbar.js?v=1.1"></script>
</body>

</html>

<?php
require_once("../../database/close-connessione.php");
?>