<?php
session_start();
require_once("../../database/connessione.php");

if (!isset($_SESSION['id']) || !isset($_GET['day'])) {
    header("Location: start_training.php");
    exit();
}

$user_id = $_SESSION['id'];
$day = intval($_GET['day']);

// Recupera l'ID dell'ultima scheda creata
$sql = "SELECT id FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($workout_plan_id);
$stmt->fetch();
$stmt->close();

// Recupera gli esercizi del giorno
$sql = "SELECT e.id, e.name 
        FROM workout_exercises we
        JOIN exercises e ON we.exercise_id = e.id
        WHERE we.workout_plan_id = ? AND we.exercise_day = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $workout_plan_id, $day);
$stmt->execute();
$result = $stmt->get_result();

$esercizi = [];
while ($row = $result->fetch_assoc()) {
    $esercizi[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Allenamento Giorno <?php echo $day; ?></title>
    <link rel="stylesheet" href="exercise_start_day.css">
    <link rel="stylesheet" href="../../commonCSS/footer.css" />
    <link rel="stylesheet" href="../../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="../../commonCSS/reset.css">

</head>

<body>
    <h1>Allenamento - Giorno <?php echo $day; ?></h1>

    <?php if (empty($esercizi)) : ?>
        <p>Nessun esercizio trovato per oggi.</p>
    <?php else : ?>
        <form id="training-form">
            <input type="hidden" name="day" value="<?php echo $day; ?>">

            <?php foreach ($esercizi as $esercizio): ?>
                <div class="exercise-card">
                    <div class="exercise-title"><?php echo htmlspecialchars($esercizio['name']); ?></div>

                    <label class="set-label">Numero di set:
                        <input
                            type="number"
                            class="set-input"
                            data-id="<?php echo $esercizio['id']; ?>"
                            id="set-input-<?php echo $esercizio['id']; ?>"
                            name="sets[<?php echo $esercizio['id']; ?>]"
                            value="3"
                            min="1"
                        >
                    </label>

                    <div id="reps-container-<?php echo $esercizio['id']; ?>" class="reps-container">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <div class="set-container">
                                <label class="set-label">Set <?php echo $i; ?>:</label>
                                <input type="number" name="reps[<?php echo $esercizio['id']; ?>][]" placeholder="Reps" min="1" required>
                                <input type="number" name="weights[<?php echo $esercizio['id']; ?>][]" placeholder="Peso (kg)" step="0.5" min="0">
                            </div>
                        <?php endfor; ?>
                    </div>

                    <label for="note-<?php echo $esercizio['id']; ?>">Note:
                        <textarea id="note-<?php echo $esercizio['id']; ?>" name="notes[<?php echo $esercizio['id']; ?>]" rows="2" placeholder="Scrivi qualcosa se vuoi..."></textarea>
                    </label>
                </div>
            <?php endforeach; ?>

            <div id="result" class="result-box"></div>

            <button type="submit" id="finishWorkoutBtn" class="finish-btn">
                ✅ Finisci allenamento
            </button>

            <p id="redirectText"></p>
        </form>
    <?php endif; ?>

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

    <script src = "exercise_start_day.js?v=1.2"></script>
    <script src="../../commonJS/commonScript.js?v=1.1"></script>
    <script src="../../commonJS/commonNavbar.js?v=1.1"></script>
</body>

</html>