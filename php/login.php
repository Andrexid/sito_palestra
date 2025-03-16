<?php
require_once('../database/connessione.php');

header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Metodo di richiesta non valido.");
    }

    // Recupero dati dal form
    $email = $conn->real_escape_string($_POST['email']);
    $password = trim($_POST['password']);

    // Controllo se l'utente esiste
    $email_control = "SELECT * FROM utenti WHERE email = '$email'";
    $result = $conn->query($email_control);

    if (!$result) {
        throw new Exception("Errore nella query di controllo: " . $conn->error);
    }

    if ($result->num_rows === 1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['id'] = $row['id'];
            $_SESSION['logged'] = true;

            $response["success"] = true;
            $response["message"] = "Login effettuato con successo.";
        } else {
            throw new Exception("Password non corretta.");
        }
    } else {
        throw new Exception("Non esistono account con questa email.");
    }
} catch (Exception $e) {
    $response["message"] = $e->getMessage();
    $response["error_file"] = $e->getFile();
    $response["error_line"] = $e->getLine();
    $response["error_code"] = $e->getCode();
}

// Invio la risposta in JSON
echo json_encode($response);
$conn->close();
?>
