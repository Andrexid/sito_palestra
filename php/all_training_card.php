<?php
session_start();
require_once("../database/connessione.php");

// Controlla se il bottone è stato premuto per cambiare lo stato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_status']) && isset($_POST['training_card_id'])) {
    $training_card_id = $_POST['training_card_id'];

    // Cambia lo stato della variabile di sessione
    if (isset($_SESSION['isEnded'][$training_card_id])) {
        $_SESSION['isEnded'][$training_card_id] = !$_SESSION['isEnded'][$training_card_id];

        // Ricarica la pagina per mostrare i cambiamenti aggiornati
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

$user_id = $_SESSION['id'];
$select_training_cards = "SELECT * FROM workout_plans WHERE user_id = ?";
$stm = $conn->prepare($select_training_cards);

if ($stm) {
    $stm->bind_param("i", $user_id);
    $stm->execute();
    $result = $stm->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='all-training-cards'>";
        while ($row = $result->fetch_assoc()) {
            $training_card_id = $row['id'];

            if (isset($_SESSION['isEnded'][$training_card_id])) {
                $status = $_SESSION['isEnded'][$training_card_id] ? "Terminato" : "Da completare";
                echo "<div class='training-card'>

                ID: " . $training_card_id . " - Stato: " . $status . "<br>
                
                </div>";

                if (!$status == "Terminato") {
                }
            } else {
                echo "<div class='container-all-cards'>
                ID: " . $training_card_id . " - Nessuna variabile di sessione trovata.<br>
                </div>";
            }
        }
        echo "</div>";
    } else {
        echo "Nessuna scheda trovata per questo utente.";
    }
    $stm->close();
} else {
    echo "Errore nella preparazione della query: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Schede</title>
    <link rel="stylesheet" href="../css/all_training_card.css">
</head>
<body>

</body>
</html>

<?php
require_once("../database/close-connessione.php");
?>
