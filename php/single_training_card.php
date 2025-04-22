<?php
session_start();
require_once("../database/connessione.php");

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
    <link rel="stylesheet" href="../css/commonNavbar.css">
    <link rel="stylesheet" href="../css/single_training_card.css">
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/buttons.css">
    <link rel="stylesheet" href="../css/_variables.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html" class="selezionata">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('progressi.html')">Progressi</a></li>
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
    if ($stm && $stm2) {
        $stm->bind_param("i", $card_id);
        $stm->execute();
        $result = $stm->get_result();

        $stm2->bind_param("i", $card_id);
        $stm2->execute();
        $result2 = $stm2->get_result();

        if ($result->num_rows > 0) {
            echo "<h1>Scheda Allenamento</h1>";
            echo "<form action='./refresh_workout_exercises.php' method='post'>
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
                <input type='submit' disabled value='Aggiorna i dati'>
            </form>";
        } else {
            echo "<h1>Errore: Nessuna scheda trovata</h1>";
        }

        $stm->close();
        $stm2->close();
    } else {
        echo "Errore nella preparazione delle query.";
    }
    ?>

    <button class="unLockButton" onclick="unLockInputs()">Sblocca</button>

    <script>
    const inputs = document.querySelectorAll(".input-text-dis");
    const unLockButton = document.querySelector(".unLockButton");
    const submitButton = document.querySelector("input[type='submit']");

    let isDisabled = true;

    function unLockInputs() {
        inputs.forEach(input => {
            input.disabled = !input.disabled;
        });

        isDisabled = !isDisabled;

        // Attiva/disattiva il bottone submit
        submitButton.disabled = !submitButton.disabled;

        // Cambia testo del bottone
        unLockButton.textContent = isDisabled ? "Abilita" : "Disabilita";
    }
</script>


</body>

</html>

<?php
require_once("../database/close-connessione.php");
?>