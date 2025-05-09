<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/connessione.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['password'], $_POST['token'])) {
        die("Password o token mancanti.");
    }

    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = $_POST['token'];

    // 1. Controlla se il token è valido
    $stmt = $conn->prepare("SELECT id FROM utenti WHERE reset_token = ? AND reset_expiry > NOW()");
    if (!$stmt) {
        die("Errore nella prepare: " . $conn->error);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // 2. Recupera l'id dell'utente (se trovato)
    if ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $stmt->close(); // CHIUDI PRIMA DI FARE ALTRE QUERY

        // 3. Aggiorna la password
        $stmt = $conn->prepare("UPDATE utenti SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        if (!$stmt) {
            die("Errore nella prepare (update): " . $conn->error);
        }

        $stmt->bind_param("si", $newPassword, $userId);
        $stmt->execute();

        echo "✅ Password aggiornata con successo! Ora puoi effettuare il login.";
    } else {
        echo "❌ Token non valido o scaduto.";
    }
}
?>
