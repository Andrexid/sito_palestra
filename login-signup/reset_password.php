<?php
require_once '../database/connessione.php';

$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = $_POST['token'];

    // Verifica token valido e non scaduto
    $stmt = $conn->prepare("SELECT id FROM utenti WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($userId);
    if ($stmt->fetch()) {
        // Aggiorna password
        $update = $conn->prepare("UPDATE utenti SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $update->bind_param("si", $newPassword, $userId);
        $update->execute();
        echo "Password aggiornata con successo!";
    } else {
        echo "Token non valido o scaduto.";
    }
} else {
    // Mostra form di reset
    echo '
    <form method="POST">
      <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
      <label>Nuova password:</label>
      <input type="password" name="password" required>
      <input type="submit" value="Aggiorna password">
    </form>';
}
?>
