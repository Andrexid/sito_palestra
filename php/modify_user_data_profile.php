<?php
session_start();
require_once('../database/connessione.php');

header('Content-Type: application/json'); // Assicura che la risposta sia JSON

$user_id = $_SESSION['id'];

// if (!isset($_POST['username'], $_POST['dateBirth'], $_POST['sex'], $_POST['weight'], $_POST['height'])) {
//     echo json_encode(["error" => "Tutti i campi devono essere compilati!"]);
//     exit;
// }else 

if (isset($_POST['username'], $_POST['dateBirth'], $_POST['sex'], $_POST['weight'], $_POST['height'])){
    // Escape dei dati per sicurezza
    $username = $conn->real_escape_string($_POST['username']);
    $dateBirth = $conn->real_escape_string($_POST['dateBirth']);
    $sex = $conn->real_escape_string($_POST['sex']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $height = $conn->real_escape_string($_POST['height']);

    // Divisione nome e cognome
    $parts = explode(" ", $username, 2);
    $name = $parts[0];
    $surname = isset($parts[1]) ? $parts[1] : "";

    // Query di aggiornamento
    $sql = "UPDATE utenti SET nome = ?, cognome = ?, data_nascita = ?, sesso = ?, peso = ?, altezza = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $name, $surname, $dateBirth, $sex, $weight, $height, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Errore durante l'aggiornamento: " . $stmt->error]);
    }
}else if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../img/uploadUtente/'; // Cartella dove salvare le immagini
    $uploadFile = $uploadDir . basename($_FILES['profilePic']['name']);

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadFile)) {
        $sql = "UPDATE utenti SET fotoProfilo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        // Query di aggiornamento
        $sql = "UPDATE utenti SET fotoProfilo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uploadFile, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "newProfilePicPath" => $uploadFile]);
            exit;
        } else {
            echo json_encode(["error" => "Errore durante l'aggiornamento: " . $stmt->error]);
        }
    } else {
        echo "Errore nel caricamento dell'immagine.";
    }
}

$stmt->close();
$conn->close();

?>
