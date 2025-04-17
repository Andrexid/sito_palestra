<?php
session_start();
require '../database/connessione.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data_inizio = date("Y-m-d");

    // Caso: Obiettivi generici
    if (
        isset($_POST['id_utente'], $_POST['obiettivo'], $_POST['valore_iniziale'], $_POST['valore_finale'], $_POST['data_finale'])
    ) {
        $id_utenti = $_POST['id_utente'];
        $obiettivi = $_POST['obiettivo'];
        $valori_iniziali = $_POST['valore_iniziale'];
        $valori_finali = $_POST['valore_finale'];
        $data_fine = $_POST['data_finale'];

        $stmt_get_id = $conn->prepare("SELECT id FROM goals WHERE obiettivo = ?");
        $stmt_insert = $conn->prepare("INSERT INTO user_goals (id_utente, id_goal, valore_iniziale, valore_finale, data_inizio, data_fine, valore_attuale) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt_get_id && $stmt_insert) {
            for ($i = 0; $i < count($obiettivi); $i++) {
                $stmt_get_id->bind_param("s", $obiettivi[$i]);
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
                        $valori_iniziali[$i]
                    );
                    $stmt_insert->execute();
                }
            }
            $stmt_get_id->close();
            $stmt_insert->close();
        } else {
            echo "Errore nella preparazione delle query per gli obiettivi generici.";
        }
    }

    // Caso: Obiettivi esercizio
    if (
        isset($_POST['id_utente'], $_POST['exercise'], $_POST['id_exercises'], $_POST['es_valore_iniziale'], $_POST['es_valore_finale'], $_POST['es_data_finale'])
    ) {
        $id_utenti = $_POST['id_utente'];
        $exercise_names = $_POST['exercise'];
        $id_exercises = $_POST['id_exercises'];
        $valori_iniziali = $_POST['es_valore_iniziale'];
        $valori_finali = $_POST['es_valore_finale'];
        $data_finale = array_map('trim', $_POST['es_data_finale']);

        // Serve anche l'ID del workout_plan per ciascun esercizio
        // Supponiamo che esista un solo piano attivo per utente
        $stmt_get_plan = $conn->prepare("SELECT id FROM workout_plans WHERE user_id = ? LIMIT 1");
        $stmt_get_plan->bind_param("i", $_SESSION['id']);
        $stmt_get_plan->execute();
        $result_plan = $stmt_get_plan->get_result();
        $workout_plan_id = $result_plan->fetch_assoc()['id'] ?? null;
        $stmt_get_plan->close();

        if ($workout_plan_id === null) {
            die("Nessun piano di allenamento trovato per l'utente.");
        }

        for ($i = 0; $i < count($id_exercises); $i++) {
            $final_date = $data_finale[$i] ?? null;

            if (!$final_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $final_date)) {
                echo "Formato data non valido per indice $i : $final_date";
                continue;
            }
        }

        $stmt_update_ex = $conn->prepare("
            UPDATE workout_exercises
            SET start_goal = ?, end_goal = ?, date_start_goal = ?, date_end_goal = ?, is_goal_set = TRUE
            WHERE exercise_id = ? AND workout_plan_id = ?
        ");

        if ($stmt_update_ex) {
            for ($i = 0; $i < count($id_exercises); $i++) {
                $stmt_update_ex->bind_param(
                    "ddssii",
                    $valori_iniziali[$i],
                    $valori_finali[$i],
                    $data_inizio,
                    $final_date,
                    $id_exercises[$i],
                    $workout_plan_id
                );
                $stmt_update_ex->execute();
            }
            $stmt_update_ex->close();
        } else {
            echo "Errore nella preparazione della query per gli esercizi.";
        }
    }

    header("Location: ./account.php");
    exit();
} else {
    echo "Metodo non valido.";
}

require '../database/close-connessione.php';
