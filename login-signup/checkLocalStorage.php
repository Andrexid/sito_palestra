<!-- Questo file prende l’id da una chiamata AJAX (es. POST o GET) e lo mette in $_SESSION -->

<?php
session_start();
header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

if (!isset($_POST['user_id'])) {
    $response['message'] = "ID utente non fornito.";
    echo json_encode($response);
    exit;
}

$user_id = intval($_POST['user_id']); // Cast di sicurezza

$_SESSION['id'] = $user_id;
$_SESSION['logged'] = true;

$response["success"] = true;
$response["message"] = "Sessione avviata con successo.";
echo json_encode($response);
?>
