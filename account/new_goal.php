<?php
session_start();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo obiettivo</title>
    <link rel="stylesheet" href="../commonCSS/commonNavbar.css">
    <link rel="stylesheet" href="new_goal.css">

</head>

<body>
    <?php
    require '../database/connessione.php';

    $select_exercises_goals = "SELECT DISTINCT e.name, e.id
    FROM workout_exercises AS we
    INNER JOIN exercises AS e ON we.exercise_id = e.id
    INNER JOIN workout_plans AS wp ON we.workout_plan_id = wp.id
    WHERE wp.user_id = '$_SESSION[id]'";
    ?>

    <main>
        <h1>Inserisci un nuovo obiettivo</h1>
        <h3>“Scegli ciò che desideri raggiungere, così possiamo personalizzare la tua esperienza.”</h3>
        <form action="./set_goal.php" method="POST">
            <div class="ext-container">
                <div class="container">
                    <h2 class="grassetto">Obiettivi generali</h2>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Aumento della forza muscolare">
                        Aumento della forza muscolare
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Incremento della massa muscolare (ipertrofia)">
                        Incremento della massa muscolare (ipertrofia)
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Perdita di peso o dimagrimento">
                        Perdita di peso o dimagrimento
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Miglioramento della resistenza cardiovascolare">
                        Miglioramento della resistenza cardiovascolare
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Miglioramento della mobilità e flessibilità">
                        Miglioramento della mobilità e flessibilità
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Miglioramento della postura e della stabilità">
                        Miglioramento della postura e della stabilità
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Aumento della potenza o performance atletica">
                        Aumento della potenza o performance atletica
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Benessere generale e salute mentale">
                        Benessere generale e salute mentale
                    </label>

                    <label>
                        <input type="checkbox" name="obiettivi[]" value="Preparazione per competizioni o sport specifici">
                        Preparazione per competizioni o sport specifici
                    </label>
                </div>
                <div>
                    <?php
                    $result = $conn->query($select_exercises_goals);

                    if ($row = $result->fetch_assoc()) {
                        echo "<div class='container'>";
                        echo '<h2 class="grassetto">Obiettivi per esercizio</h2>';
                        while ($row = $result->fetch_assoc()) {
                            echo "<div>
                            <label>
                                <input type='checkbox' name='exercise_goals[" . $row['id'] . "]' value='" . $row['name'] . "'>
                                " . htmlspecialchars($row['name']) . "
                            </label>
                        </div>";
                        }
                        echo "</div>";
                    } else {
                        echo "Errore";
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="principal_button">Invia Obiettivi</button>
        </form>
    </main>

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
</body>

</html>