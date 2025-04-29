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
    <link rel="stylesheet" href="single_training_card.css">
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
            echo "<h1>Scheda Allenamento</h1>";

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
        } else {
            echo "<h1>Errore: Nessuna scheda trovata</h1>";
        }

        $stm->close();
        $stm2->close();
    } else {
        echo "Errore nella preparazione delle query.";
    }
    ?>

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

</body>

</html>

<?php
require_once("../../database/close-connessione.php");
?>
