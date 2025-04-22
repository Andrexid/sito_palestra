<?php
header("Content-Type: application/json");
require_once("../database/connessione.php");

// Query per ottenere tutti gli esercizi
$sql = "SELECT name, muscle_group FROM exercises ORDER BY muscle_group, name";
$result = $conn->query($sql);

$exercises = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $muscleGroup = $row["muscle_group"];
        $exerciseName = $row["name"];
        
        // Raggruppa gli esercizi per gruppo muscolare
        if (!isset($exercises[$muscleGroup])) {
            $exercises[$muscleGroup] = [];
        }
        $exercises[$muscleGroup][] = $exerciseName;
    }
}

// Restituisce i dati in JSON
echo json_encode(["success" => true, "data" => $exercises]);

$conn->close();
?>