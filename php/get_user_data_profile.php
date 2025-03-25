<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Assicurati che la sessione sia avviata

require_once('../database/connessione.php'); // Connessione al database

if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "Sessione non valida o utente non loggato"]);
    exit;
}

$user_id = $_SESSION['id'];
$sql = "SELECT nome, cognome, email, data_nascita, sesso, peso, altezza FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Errore nella preparazione della query"]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(["error" => "Utente non trovato"]);
}

$stmt->close();
$conn->close();

?>
