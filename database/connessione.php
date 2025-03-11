<?php
    $servername = 'localhost';
    $username = 'root';
    $password = ''; // Lascia vuoto se non hai configurato una password
    $dbname = 'palestra';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    } else {
        echo "<h1>Prova</h1>";
    }

?>