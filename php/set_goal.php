<?php
session_start();
require '../database/connessione.php';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleziona Obiettivi</title>
    <link rel="stylesheet" href="../css/set_goal.css">
</head>

<body>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['obiettivi']) && !empty($_POST['obiettivi'])) {
            $obiettivi_selezionati = $_POST['obiettivi'];

            echo "<h1>Obiettivi Selezionati</h1>";
            echo "<form method='POST' action='./insert_goal.php'>";
            echo "<div class='card_container'>";

            foreach ($obiettivi_selezionati as $obiettivo_nome) {
                // Prepara la query per cercare l'obiettivo
                $query = "SELECT * FROM goals WHERE obiettivo = ?";
                $stm = $conn->prepare($query);
                $stm->bind_param("s", $obiettivo_nome);

                if ($stm->execute()) {
                    $result = $stm->get_result();
                    if ($row = $result->fetch_assoc()) {
                        echo "<div class='card'>";
                        echo "<h3>" . htmlspecialchars($row['obiettivo']) . "</h3>";
                        echo "<p>" . htmlspecialchars($row['descrizione']) . "</p>";
                        echo "<span>" . htmlspecialchars($row['metrica']) . "</span><br>";

                        // Hidden fields per i dati da inviare
                        echo "<input type='hidden' name='id_goal[]' value='" . $row['id'] . "'>";
                        echo "<input type='hidden' name='id_utente[]' value='" . $_SESSION['id'] . "'>";
                        echo "<input type='hidden' name='obiettivo[]' value='" . htmlspecialchars($row['obiettivo'], ENT_QUOTES, 'UTF-8') . "'>";

                        // Campi input per valore iniziale e finale
                        echo "<p>Inserisci un valore iniziale</p>";
                        echo "<div class='insert'>
                            <input type='text' name='valore_iniziale[]' required> " . htmlspecialchars($row['unita_misura']) . "
                          </div>";

                        echo "<p>Inserisci un valore da raggiungere</p>";
                        echo "<div class='insert'>
                            <input type='text' name='valore_finale[]' required> " . htmlspecialchars($row['unita_misura']) . "
                          </div>";

                        echo "<p>Inserisci la data finale</p>";
                        echo "<div class='insert'>
                            <input type='date' name='data_finale' required>;
                          </div>";

                        echo "</div>"; // chiude card
                    }
                }
            }

            echo "</div>"; // chiude card_container
            echo "<input type='submit' value='Salva Obiettivi'>";
            echo "</form>";
        } else {
            echo "<h1>Nessun obiettivo selezionato</h1>";
        }
    } else {
        echo "<h1>Errore: metodo non valido</h1>";
    }

    require '../database/close-connessione.php';
    ?>

</body>

</html>