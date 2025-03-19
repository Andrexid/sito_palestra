<?php
session_start();
require_once('../database/connessione.php');

header('Content-Type: application/json'); // Assicura che la risposta sia JSON

$user_id = $_SESSION['id'];

if (!isset($_POST['username'], $_POST['dateBirth'], $_POST['sex'])) {
    echo json_encode(["error" => "Tutti i campi devono essere compilati!"]);
    exit;
}

// Escape dei dati per sicurezza
$username = $conn->real_escape_string($_POST['username']);
$dateBirth = $conn->real_escape_string($_POST['dateBirth']);
$sex = $conn->real_escape_string($_POST['sex']);

// Divisione nome e cognome
$parts = explode(" ", $username, 2);
$name = $parts[0];
$surname = isset($parts[1]) ? $parts[1] : "";

// Query di aggiornamento
$sql = "UPDATE utenti SET nome = ?, cognome = ?, data_nascita = ?, sesso = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $surname, $dateBirth, $sex, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Errore durante l'aggiornamento: " . $stmt->error]);
}

$stmt->close();
$conn->close();

?>
