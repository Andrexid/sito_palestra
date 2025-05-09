<?php
session_start();
require_once("../../database/connessione.php");

// Verifica che sia una richiesta POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Legge i dati JSON inviati
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id_card']) && isset($_SESSION['id'])) {
        $id_card = $data['id_card'];
        $user_id = $_SESSION['id'];

        // Prepara ed esegue la query
        $delete_training_card = "DELETE FROM workout_plans WHERE id = ? AND user_id = ?";
        $stm = $conn->prepare($delete_training_card);

        if ($stm) {
            $stm->bind_param("ii", $id_card, $user_id);
            $success = $stm->execute();

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Scheda eliminata']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione']);
            }

            $stm->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dati mancanti o sessione non valida']);
    }

    exit;
}

echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
exit;
