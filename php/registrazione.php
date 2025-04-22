<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once('../database/connessione.php');

$response = ["success" => false, "message" => ""];

try {
    // Recupero variabili tramite POST
    $name = $conn->real_escape_string($_POST['nome']);
    $surname = $conn->real_escape_string($_POST['cognome']);
    $email = $conn->real_escape_string($_POST['email']);
    $psw = $conn->real_escape_string($_POST['password']);
    $sex = $conn->real_escape_string($_POST['sesso']);

    // Controllo se l'email è già registrata
    $existing_user_control = "SELECT id FROM utenti WHERE email = '$email'";
    $result_control = $conn->query($existing_user_control);

    if (!$result_control) {
        throw new Exception("Errore nella query di controllo: " . $conn->error);
    }

    if ($result_control->num_rows > 0) {
        throw new Exception("L'email è già registrata.");
    }

    // Hash della password
    $password_hash = password_hash($psw, PASSWORD_DEFAULT);

    // Query di inserimento
    $insert_new_user = "INSERT INTO utenti (nome, cognome, data_nascita, email, password, sesso, peso, altezza) 
                        VALUES ('$name', '$surname', NULL, '$email', '$password_hash', '$sex', NULL, NULL)";

    if (!$conn->query($insert_new_user)) {
        throw new Exception("Errore durante la registrazione: " . $conn->error);
    }

    // Inizializzazione sessione
    session_start();
    $_SESSION['id'] = $conn->insert_id;
    $_SESSION['logged'] = true;

    $response["success"] = true;
    $response["message"] = "Registrazione effettuata con successo!";
} catch (Exception $e) {
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>
