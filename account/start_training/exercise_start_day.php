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
                <a href="#" onclick="logout()">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <h1 style="margin-top: 30px;">Allenamento - Giorno <?php echo $day; ?></h1>

    <?php if (empty($esercizi)) : ?>
        <p style="text-align:center;">Nessun esercizio trovato per oggi.</p>
    <?php else : ?>
        <form id="training-form">
            <input type="hidden" name="day" value="<?php echo $day; ?>">

            <?php foreach ($esercizi as $esercizio): ?>
                <div class="exercise-card">
                    <div class="exercise-title"><?php echo htmlspecialchars($esercizio['name']); ?></div>

                    <label class="set-label">Numero di set:
                        <input type="number" id="set-input-<?php echo $esercizio['id']; ?>" name="sets[<?php echo $esercizio['id']; ?>]" value="3" min="1" onchange="generaInputSet(<?php echo $esercizio['id']; ?>)">
                    </label>

                    <div id="reps-container-<?php echo $esercizio['id']; ?>">
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

            <div id="result" class="result-box" style="display: none;"></div>

            <button type="submit" class="finish-btn" onclick="redirectAfterDelay()">✅ Finisci allenamento</button>
            <p id="redirectText"></p>
        </form>
    <?php endif; ?>

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
        function generaInputSet(id) {
            const setInput = document.getElementById(`set-input-${id}`);
            const setCount = parseInt(setInput.value);
            const container = document.getElementById(`reps-container-${id}`);
            container.innerHTML = "";
            for (let i = 1; i <= setCount; i++) {
                container.innerHTML += `
                <div class="set-container">
                    <label class="set-label">Set ${i}:</label>
                    <input type="number" name="reps[${id}][]" placeholder="Reps" min="1" required>
                    <input type="number" name="weights[${id}][]" placeholder="Peso (kg)" step="0.5" min="0">
                </div>
            `;
            }
        }

        document.getElementById("training-form").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("save_training_ajax.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    const resultBox = document.getElementById("result");
                    resultBox.style.display = "block";
                    resultBox.innerHTML = `
                <strong>🎉 Allenamento salvato con successo!</strong><br><br>
                Hai guadagnato <strong>${data.xp}</strong> XP<br>
                Totale XP: <strong>${data.total_xp}</strong><br>
                Verrai reindirizzato tra pochi secondi <br>
            `;
                    document.getElementById("training-form").reset();
                })
                .catch(err => alert("Errore nel salvataggio: " + err));
        });

        function redirectAfterDelay() {
            // (Opzionale) Mostra un messaggio mentre aspetti
            // Aspetta 5 secondi e poi cambia pagina
            setTimeout(function() {
                window.location.href = '../account.php'; // Cambia "nome-pagina.php" con la destinazione
            }, 5000);
        }
    </script>

    <script src="../../commonJS/commonScript.js"></script>
    <script src="../../commonJS/commonNavbar.js"></script>
</body>

</html>