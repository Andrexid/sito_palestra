<?php
session_start();
require_once("../database/connessione.php");

if (!isset($_SESSION['id']) || !isset($_GET['day'])) {
    header("Location: start_training.php");
    exit();
}

$user_id = $_SESSION['id'];
$day = intval($_GET['day']);

// Recupera l'ID dell'ultima scheda creata dall'utente
$sql = "SELECT id FROM workout_plans WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($workout_plan_id);
$stmt->fetch();
$stmt->close();

// Recupera gli esercizi del giorno scelto
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
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/buttons.css">
    <link rel="stylesheet" href="../css/_variables.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
        }
        .exercise-card {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .exercise-title {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .set-container {
            margin-bottom: 1rem;
        }
        input[type="number"], textarea {
            width: 80px;
            padding: 0.4rem;
            margin-right: 10px;
            color: black;
        }
        textarea {
            width: 100%;
            margin-top: 0.5rem;
        }
        .finish-btn {
            margin-top: 2rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: black;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .result-box {
            margin-top: 2rem;
            padding: 1rem;
            border: 2px dashed var(--primary-color);
            border-radius: 12px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<h1>Allenamento Giorno <?php echo $day; ?></h1>

<?php if (empty($esercizi)) : ?>
    <p>Nessun esercizio trovato per oggi.</p>
<?php else : ?>
    <form id="training-form">
        <input type="hidden" name="day" value="<?php echo $day; ?>">

        <?php foreach ($esercizi as $esercizio): ?>
            <div class="exercise-card">
                <div class="exercise-title"><?php echo htmlspecialchars($esercizio['name']); ?></div>

                <label>Numero di set: 
                    <input type="number" id="set-input-<?php echo $esercizio['id']; ?>" name="sets[<?php echo $esercizio['id']; ?>]" value="3" min="1" onchange="generaInputSet(<?php echo $esercizio['id']; ?>)">
                </label>

                <div id="reps-container-<?php echo $esercizio['id']; ?>">
                    <div class="set-container">
                        Set 1: 
                        <input type="number" name="reps[<?php echo $esercizio['id']; ?>][]" placeholder="Reps" min="1" required> 
                        <input type="number" name="weights[<?php echo $esercizio['id']; ?>][]" placeholder="Peso (kg)" step="0.5" min="0">
                    </div>
                    <div class="set-container">
                        Set 2: 
                        <input type="number" name="reps[<?php echo $esercizio['id']; ?>][]" placeholder="Reps" min="1" required> 
                        <input type="number" name="weights[<?php echo $esercizio['id']; ?>][]" placeholder="Peso (kg)" step="0.5" min="0">
                    </div>
                    <div class="set-container">
                        Set 3: 
                        <input type="number" name="reps[<?php echo $esercizio['id']; ?>][]" placeholder="Reps" min="1" required> 
                        <input type="number" name="weights[<?php echo $esercizio['id']; ?>][]" placeholder="Peso (kg)" step="0.5" min="0">
                    </div>
                </div>

                <br>
                <label>Note:<br>
                    <textarea name="notes[<?php echo $esercizio['id']; ?>]" rows="2" placeholder="Scrivi qualcosa se vuoi..."></textarea>
                </label>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="finish-btn">Finisci allenamento</button>
    </form>

    <div id="result" class="result-box" style="display: none;"></div>
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
                    Set ${i}: 
                    <input type="number" name="reps[${id}][]" placeholder="Reps" min="1" required> 
                    <input type="number" name="weights[${id}][]" placeholder="Peso (kg)" step="0.5" min="0">
                </div>
            `;
        }
    }

    document.getElementById("training-form").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        console.log(formData);

        fetch("save_training_ajax.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const resultBox = document.getElementById("result");
            resultBox.style.display = "block";
            resultBox.innerHTML = `
                <strong>Allenamento salvato con successo!</strong><br>
                Hai guadagnato <strong>${data.xp}</strong> XP<br>
                Totale XP: <strong>${data.total_xp}</strong><br>
                Badge ottenuto: <strong>${data.badge}</strong>
            `;
        })
        .catch(err => alert("Errore nel salvataggio: " + err));
    });
</script>

</body>
</html>
