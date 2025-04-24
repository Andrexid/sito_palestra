<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once('../database/connessione.php');

// Verifica che i dati siano stati inviati correttamente
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ottieni i dati dal form
    $name = $conn->real_escape_string($_POST['nome']);
    $surname = $conn->real_escape_string($_POST['cognome']);
    $email = $conn->real_escape_string($_POST['email']);

    // Oggetto dell'email
    $subject = "Benvenuto nella nostra community fitness!";

    // Contenuto HTML dell'email
    $message = "
    <html>
    <head>
        <title>Benvenuto nella nostra community fitness!</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            h1 { color: #007BFF; }
            .content { padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; }
            .footer { margin-top: 20px; font-size: 0.9em; color: #555; }
        </style>
    </head>
    <body>
        <h1>Ciao $name,</h1>
        <div class='content'>
            <p>Benvenuto nella nostra piattaforma fitness! 🎉</p>
            <p>Qui potrai monitorare i tuoi progressi, impostare obiettivi e migliorare la tua forma fisica con strumenti avanzati.</p>
            <p>Ecco alcune funzionalità che troverai sul nostro sito:</p>
            <ul>
                <li><strong>Tracciamento degli allenamenti:</strong> Registra le tue sessioni e osserva i miglioramenti.</li>
                <li><strong>Statistiche dettagliate:</strong> Visualizza grafici e analisi sui tuoi progressi.</li>
                <li><strong>Obiettivi personalizzati:</strong> Imposta target e raggiungili con il nostro supporto.</li>
            </ul>
            <p>Accedi subito al tuo profilo e inizia il tuo percorso di trasformazione! 💪</p>
            <p><a href='https://www.andre-dev.com/login.html'>Accedi ora</a></p>
        </div>
        <div class='footer'>
            <p>A presto,</p>
            <p><strong>Il Team di Fitness Tracker</strong></p>
        </div>
    </body>
    </html>
    ";

    // Header dell'email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Fitness Tracker <andreasabetta456@gmail.com>" . "\r\n";
    $headers .= "Reply-To: andreasabetta456@gmail.com" . "\r\n";

    // Invio dell'email all'utente
    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(["success" => true, "message" => "Email inviata con successo!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Errore durante l'invio dell'email."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Richiesta non valida."]);
}

$conn->close();
?>
