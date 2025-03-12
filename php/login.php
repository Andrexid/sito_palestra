<?php

// Richiamo della connessione al database
require_once('../database/connessione.php');

// Recupero dei dati dal from
$email = $conn->real_escape_string($_POST['email']);
$password = trim(($_POST['password']));

// Controllo che i dati siano inseriti tramite POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Controllo se l'utente esiste
    $email_control = "SELECT * FROM utenti WHERE email = '$email'";

    if ($result = $conn->query($email_control)) {
        // Se è presente una riga si va avanti
        if ($result->num_rows == 1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            // Verifica cella paassword
            if (password_verify($password, $row['psw'])) {

                // Inizializzazione della sessione
                session_start();

                $_SESSION['id'] = $row['id_utente'];
                $_SESSION['logged'] = true;

                header("Location: account.php");
            } else {
                $errore = 'Password non corretta';
                $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
                header("Location: $url");
            }
        } else {
            $errore = 'Non esistono account con quello username';
            $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
            header("Location: $url");
        }
    } else {
        $errore = 'Errore in fase di accesso';
        $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
        header("Location: $url");
    }
}

// Richiamo della chiusura della connessione al database
require_once('../database/close-connessione.php');
