<?php
session_start(); // Assicurati che la sessione sia avviata per ottenere l'ID utente

// Richiamo della connessione al database
require_once('../database/connessione.php');

$user_id = $_SESSION['id']; // Prendi l'ID utente dalla sessione

// Query per ottenere i dati dell'utente
$sql = "SELECT nome, cognome, email FROM utenti WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user); // Restituisce i dati in formato JSON
} else {
    echo json_encode(["error" => "Utente non trovato"]);
}

$stmt->close();
$conn->close();
?>
