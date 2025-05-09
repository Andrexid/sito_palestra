<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once('../database/connessione.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string($_POST['nome']);
    $surname = $conn->real_escape_string($_POST['cognome']);
    $email = $conn->real_escape_string($_POST['email']);

    $subject = "Benvenuto su MyGymStats 💪";

    $message = "
    <html>
    <head>
        <title>Benvenuto su MyGymStats!</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            h1 { color: #2E86C1; }
            .content { padding: 20px; background-color: #f4f4f4; border-radius: 8px; }
            ul { margin-top: 10px; }
            .footer { margin-top: 20px; font-size: 0.9em; color: #555; }
        </style>
    </head>
    <body>
        <h1>Ciao $name $surname,</h1>
        <div class='content'>
            <p>Siamo felicissimi che tu abbia scelto di allenarti con noi su <strong>MyGymStats</strong>! 🎉</p>
            <p>Hai appena fatto il primo passo verso una versione più forte e determinata di te stesso. Il nostro obiettivo è accompagnarti ogni giorno, rendendo ogni allenamento più chiaro, motivante e gratificante.</p>
            
            <p>Con <strong>MyGymStats</strong> potrai:</p>
            <ul>
                <li>📅 <strong>Creare piani di allenamento personalizzati</strong> e scegliere gli esercizi giusti per te</li>
                <li>📊 <strong>Visualizzare grafici e statistiche</strong> sui tuoi progressi settimana dopo settimana</li>
                <li>🎮 <strong>Guadagnare punti esperienza</strong> per ogni sessione completata e salire di livello</li>
                <li>🏆 <strong>Impostare obiettivi personali</strong> e restare motivato nel raggiungerli</li>
            </ul>

            <p>Accedi al tuo profilo e inizia a costruire la tua trasformazione oggi stesso.</p>
            <p><a href='https://www.mygymstats.com/login-signup/login.html'>👉 Vai al tuo profilo ora</a></p>
        </div>
        <div class='footer'>
            <p>Non vediamo l'ora di vedere i tuoi progressi!</p>
            <p><strong>Il Team di MyGymStats</strong></p>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: MyGymStats <info@mygymstats.com>\r\n";
    $headers .= "Reply-To: info@mygymstats.com\r\n";

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
