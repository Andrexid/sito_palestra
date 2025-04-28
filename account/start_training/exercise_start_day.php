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
</head>
<body>

<h1>Allenamento - Giorno <?php echo $day; ?></h1>

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

    document.getElementById("training-form").addEventListener("submit", function (e) {
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
            `;
            document.getElementById("training-form").reset();
        })
        .catch(err => alert("Errore nel salvataggio: " + err));
    });

    function redirectAfterDelay() {
    // (Opzionale) Mostra un messaggio mentre aspetti
    document.getElementById('redirectText').style.display = 'block';
    document.getElementById('redirectText').style.color = 'black';
    document.getElementById('redirectText').innerText = 'Verrai reindirizzato tra pochi secondi...';

    // Aspetta 5 secondi e poi cambia pagina
    setTimeout(function() {
        window.location.href = '../account.php'; // Cambia "nome-pagina.php" con la destinazione
    }, 2000);
}
</script>

</body>
</html>
