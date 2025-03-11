<?php

require_once('../database/connessione.php');


$nome = $conn->real_escape_string($_POST['nome']);
$cognome = $conn->real_escape_string($_POST['cognome']);
$email = $conn->real_escape_string($_POST['email']);
$psw = $conn->real_escape_string($_POST['password']);
$vpsw = $conn->real_escape_string($_POST['password-v']);
$sesso = $conn->real_escape_string($_POST['sesso']);


if($psw === $vpsw){
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
} else {
    echo 'Le password non corrispondono, Riprova';
    exit;
}


$sql_control = "SELECT * FROM utenti WHERE email = '$email'";
$sql = "INSERT INTO utenti (nome, cognome, data_nascita, email, password, sesso, peso, altezza) 
        VALUES ('$nome', '$cognome', NULL, '$email', '$password_hash', '$sesso', NULL, NULL)";

// verifica se ci sono numeri all'interno di nome o cognome
if ($result_control = $conn->query($sql_control)) {
    if ($result_control->num_rows == 0) {
        if (preg_match("/[0-9]/", $nome)) {
            echo 'Non inserire numeri nel nome';
            exit;

        } elseif (preg_match("/[0-9]/", $cognome)) {
            echo 'Non inserire numeri nel cognome';
            exit;
            // controllo se password è abbastanza lunga
        } else if ($length = strlen($psw) < 4) {
            echo 'La password deve contenere più di 5 caratteri';
        } else {
            if ($conn->query($sql) === true) {
                echo "Registrazione effettuata con successo";

                session_start();

                $_SESSION['id'] = $conn->insert_id;
                $_SESSION['loggato'] = true;

                header('Location: account.php');
            } else {
                echo 'Errore durante la registrazione dell\'utente';
            }
        }
    } else {
        echo 'Utente già registrato';
    }
}

$conn->close();
