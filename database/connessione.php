<?php
    // Credenziali del database
    $servername = 'localhost';
    $username = 'root';
    $password = ''; // Lascia vuoto se non hai configurato una password
    $dbname = 'palestra';

    // Query di connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // controllo della connessione
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

?>