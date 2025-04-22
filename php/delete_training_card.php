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
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


if (isset($_GET['id_card'])) {
    $id_card = $_GET['id_card'];

    $delete_training_card = "DELETE FROM workout_plans WHERE id = ? AND user_id = ?";
    $stm = $conn->prepare($delete_training_card);

    if ($stm) {
        $stm->bind_param("ii", $id_card, $_SESSION['id']);
        $stm->execute();
        $result = $stm->get_result();

        header("Location: ./account.php");
    }
}



require_once("../database/close-connessione.php");

?>