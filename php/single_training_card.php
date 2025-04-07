<?php
session_start();
require_once("../database/connessione.php");

// Controlla se il bottone è stato premuto per cambiare lo stato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['training_card_id'])) {
    $training_card_id = $_POST['training_card_id'];
}

if (isset($_GET['id'])) {
    $card_id = $_GET['id'];

    // Query per selezionare tutte le info della scheda
    $search_card = "SELECT * FROM workout_plans WHERE id = ?";
    $stm = $conn->prepare($search_card);
} else {
    echo "Errore, id non trovato";
}


?>


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheda numero: <?php echo $card_id; ?></title>
    <link rel="stylesheet" href="../css/commonNavbar.css">
    <nav class="navbar">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Palestra">
        </div>
        <ul class="nav-links">
            <li><a href="../index.html" class="selezionata">Home</a></li>
            <li><a href="#" onclick="controllaAccesso('progressi.html')">Progressi</a></li>
            <li><a href="faq.html">FAQ</a></li>
            <li><a href="contatti.html">Contatti</a></li>
            <li class="profile-container">
                <a href="#">
                    <img id="profile-pic" src="" alt="Profilo">
                </a>
                <div class="dropdown-menu" id="profile-menu">
                    <a href="#" onclick="controllaAccesso('profilo.html')">👤 Profilo</a>
                    <a href="#" onclick="controllaAccesso('impostazioni.html')">⚙️ Impostazioni</a>
                    <a href="#" onclick="logout()">🚪 Logout</a>
                </div>
            </li>
        </ul>
    </nav>
</head>

<body>


    <?php
    if ($stm) {
        $stm->bind_param("i", $card_id);
        $stm->execute();
        $result = $stm->get_result();

        if ($result->num_rows > 0) {
            echo "<h1>Scheda</h1>";
            echo "<table border='1'>
                    <thead>
                        <tr>
                            <td>
                                Esercizi
                            </td>
                            <td>
                                Reps
                            </td>
                            <td>
                                Peso
                            </td>
                        </tr>
                    </thead>
            ";
            while ($row = $result->fetch_assoc()) {
                for ($i = 1; $i <= $row['workouts_per_week']; $i++) {
                    echo "<tr>
                            <td>Ciao</td>
                        </tr>";
                };
            }
            echo "</table>";
        } else {
            echo "<h1>Errore, Nessuna scheda trovata</h1>";
        }
        $stm->close();
    } else {
        echo "Errore nella preparazione della query: " . $conn->error;
    }
    ?>

</body>

</html>



<?php
require_once("../database/close-connessione.php");
?>