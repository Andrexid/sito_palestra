<?php
// Richiamo della connessione al database
require_once('../database/connessione.php');

// Recupero variabili tramite POST
$name = $conn->real_escape_string($_POST['nome']);
$surname = $conn->real_escape_string($_POST['cognome']);
$email = $conn->real_escape_string($_POST['email']);
$psw = $conn->real_escape_string($_POST['password']);
$vpsw = $conn->real_escape_string($_POST['password-v']);
$sex = $conn->real_escape_string($_POST['sesso']);

// Doppio controllo della password
if ($psw === $vpsw) {
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
} else {
    echo 'Le password non corrispondono, Riprova';
    exit;
}

// Prima query di controllo 
$existing_user_control = "SELECT * FROM utenti WHERE email = '$email'";

// Query di inserimento del nuovo utente
$insert_new_user = "INSERT INTO utenti (nome, cognome, data_nascita, email, password, sesso, peso, altezza) 
        VALUES ('$name', '$surname', NULL, '$email', '$password_hash', '$sex', NULL, NULL)";

// Primo controllo
if ($result_control = $conn->query($existing_user_control)) {
    // Se non ci sono utenti già registrati 
    if ($result_control->num_rows == 0) {
        // verifica se ci sono numeri all'interno di nome o cognome
        if (preg_match("/[0-9]/", $name)) {
            $errore = 'Non inserire numeri nel nome';
            $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
            header("Location: $url");
            exit;
        } elseif (preg_match("/[0-9]/", $surname)) {
            $errore = 'Non inserire numeri nel cognome';
            $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
            header("Location: $url");
            exit;

            // Controllo se password è abbastanza lunga
        } else if ($length = strlen($psw) < 4) {
            $errore = 'La password deve contenere più di 5 caratteri';
            $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
            header("Location: $url");
        } else {
            if ($conn->query($insert_new_user) === true) {
                echo "Registrazione effettuata con successo";

                // Inizializzazione della sessione
                session_start();

                $_SESSION['id'] = $conn->insert_id;
                $_SESSION['logged'] = true;

                // Spostamneto nell'account
                header('Location: account.php');
            } else {
                $errore = 'Errore durante la registrazione dell\'utente';
                $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
                header("Location: $url");
            }
        }
    } else {
        $errore = 'Utente già registrato';
        $url = "../utilites/errore-utente.php?errore=" . urlencode($errore);
        header("Location: $url");
    }
}

// Richiamo della chiusura della connessione al database
require_once('../database/close-connessione.php');
