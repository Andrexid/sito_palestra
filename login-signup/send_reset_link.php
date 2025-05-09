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
        $expiry = date("Y-m-d H:i:s", time() + 3600); // Valido per 1 ora

        $update = $conn->prepare("UPDATE utenti SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        $resetLink = "https://www.mygymstats.com/login-signup/reset_password/reset_password_form.html?token=$token";
        $subject = "Reimposta la tua password MyGymStats";
        $message = "<html><body>
        <h2>Reimposta la tua password</h2>
        <p>Hai richiesto di reimpostare la tua password. Clicca sul link qui sotto per continuare:</p>
        <a href='$resetLink'>$resetLink</a>
        <p>Il link scadrà tra 60 minuti.</p>
        <br>
        <p>Se non hai richiesto questo reset, ignora questa email.</p>
        </body></html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: MyGymStats Support <no-reply@mygymstats.com>\r\n";


        // Invia l'email
        mail($email, $subject, $message, $headers);

        echo json_encode(["success" => true, "message" => "Email inviata! Controlla la tua casella di posta."]);
    } else {
        echo json_encode(["success" => false, "message" => "Email non trovata nei nostri archivi."]);
    }
}
?>
