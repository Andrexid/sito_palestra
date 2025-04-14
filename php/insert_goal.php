<?php
session_start();
require '../database/connessione.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['id_utente']) &&
        isset($_POST['obiettivo']) &&
        isset($_POST['valore_iniziale']) &&
        isset($_POST['valore_finale']) &&
        isset($_POST['data_finale'])
    ) {
        $id_utenti = $_POST['id_utente'];
        $obiettivi = $_POST['obiettivo'];
        $valori_iniziali = $_POST['valore_iniziale'];
        $valori_finali = $_POST['valore_finale'];
        $data_fine = $_POST['data_finale'];

        $data_inizio = date("Y-m-d");

        $stmt_get_id = $conn->prepare("SELECT id FROM goals WHERE obiettivo = ?");
        $stmt_insert = $conn->prepare("INSERT INTO user_goals (id_utente, id_goal, valore_iniziale, valore_finale, data_inizio, data_fine, valore_attuale) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt_get_id && $stmt_insert) {
            for ($i = 0; $i < count($obiettivi); $i++) {
                $obiettivo = $obiettivi[$i];

                $stmt_get_id->bind_param("s", $obiettivo);
                $stmt_get_id->execute();
                $result = $stmt_get_id->get_result();

                if ($row = $result->fetch_assoc()) {
                    $id_goal = $row['id'];

                    $stmt_insert->bind_param(
                        "iiddssd",
                        $id_utenti[$i],
                        $id_goal,
                        $valori_iniziali[$i],
                        $valori_finali[$i],
                        $data_inizio,
                        $data_fine,
                        $valori_iniziali[$i],
                    );
                    $stmt_insert->execute();
                }
            }

            echo "<h2>Obiettivi salvati correttamente!</h2>";
            header("Location: ./account.php");

        } else {
            echo "Errore nella preparazione delle query.";
        }

        $stmt_get_id->close();
        $stmt_insert->close();
    } else {
        echo "Errore: dati mancanti nel form.";
    }
} else {
    echo "Metodo non valido.";
}

require '../database/close-connessione.php';
