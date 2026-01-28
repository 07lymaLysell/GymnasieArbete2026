<?php
session_start();

// CORS-headers - tillåt requests från SvelteKit
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();
$result = [
    'success' => false,
    'message' => 'Kunde inte lägga till användare'
];

// 1. Kontrollera att alla obligatoriska fält finns med
if (
    !isset($_POST['firstname'], $_POST['surname'], $_POST['username'], $_POST['password'], $_POST['email']) ||
    empty(trim($_POST['firstname'])) ||
    empty(trim($_POST['surname'])) ||
    empty(trim($_POST['username'])) ||
    empty(trim($_POST['password'])) ||
    empty(trim($_POST['email']))
) {
    $result['message'] = 'Vänligen fyll i alla fält';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Rensa och förbered indata
$firstname = trim($_POST['firstname']);
$surname = trim($_POST['surname']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// 3. Kontrollera om användarnamnet redan finns
if ($db->userExists($username)) {
    $result['message'] = 'Användarnamnet är redan taget';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Kontrollera lösenordets längd
if (strlen($password) < 6) {
    $result['message'] = 'Lösenordet måste vara minst 6 tecken långt';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Försök skapa användaren
$inserted = $db->addUser($firstname, $surname, $username, $email, $password);

// 6. Sätt resultatet beroende på om det lyckades
if ($inserted) {
    $result['success'] = true;
    $result['message'] = 'Användare skapad framgångsrikt!';
} else {
    $result['message'] = 'Kunde inte skapa användare (databasfel ???)';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
