<?php
require_once '../database/connessione.php';

header('Content-Type: application/json'); // Risposta JSON

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT id FROM utenti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600);

        $update = $conn->prepare("UPDATE utenti SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        $resetLink = "https://www.andre-dev.com/reset_password.php?token=$token";

        $subject = "Reset della tua password";
        $message = "<html><body><h2>Reset Password</h2>
        <p>Clicca qui per reimpostare la password:</p>
        <a href='$resetLink'>$resetLink</a></body></html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Andrea Sabetta <andre@andre-dev.com>\r\n";

        mail($email, $subject, $message, $headers);

        echo json_encode(["success" => true, "message" => "Email inviata! Controlla la tua casella."]);
    } else {
        echo json_encode(["success" => false, "message" => "Email non trovata."]);
    }
}
?>
