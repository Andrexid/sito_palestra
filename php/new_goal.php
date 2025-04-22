<?php
session_start();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo obiettivo</title>
    <link rel="stylesheet" href="../css/commonNavbar.css">
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
    require '../database/connessione.php';

    $select_exercises_goals = "SELECT DISTINCT e.name, e.id
    FROM workout_exercises AS we
    INNER JOIN exercises AS e ON we.exercise_id = e.id
    INNER JOIN workout_plans AS wp ON we.workout_plan_id = wp.id
    WHERE wp.user_id = '$_SESSION[id]'";

    ?>



    <main>
        <h1>Inserisci un nuovo obiettivo</h1>
        <form action="../php/set_goal.php" method="POST">
            <div>
                <legend>Seleziona uno o più obiettivi</legend>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Aumento della forza muscolare">
                    Aumento della forza muscolare
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Incremento della massa muscolare (ipertrofia)">
                    Incremento della massa muscolare (ipertrofia)
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Perdita di peso o dimagrimento">
                    Perdita di peso o dimagrimento
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Miglioramento della resistenza cardiovascolare">
                    Miglioramento della resistenza cardiovascolare
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Miglioramento della mobilità e flessibilità">
                    Miglioramento della mobilità e flessibilità
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Miglioramento della postura e della stabilità">
                    Miglioramento della postura e della stabilità
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Aumento della potenza o performance atletica">
                    Aumento della potenza o performance atletica
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Benessere generale e salute mentale">
                    Benessere generale e salute mentale
                </label><br>

                <label>
                    <input type="checkbox" name="obiettivi[]" value="Preparazione per competizioni o sport specifici">
                    Preparazione per competizioni o sport specifici
                </label><br>

            </div>


            <div>
                <?php
                $result = $conn->query($select_exercises_goals);

                if ($row = $result->fetch_assoc()) {
                    echo "<div>";
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

            <button type="submit">Invia Obiettivi</button>
        </form>
    </main>


</body>

</html>