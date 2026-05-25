<?php
/**
 * API-ENDPOINT: Registrera ny användare
 * =====================================
 * Syftet: Motta registreringsdata från frontend och skapa ett nytt användarkonto
 * HTTP-metod: POST
 * Indata: firstname, surname, username, password, email
 * Utdata: JSON med success-status och meddelande
 */

session_start(); // Starta PHP-session för CSRF-hantering

// ========== CORS-HEADERS: Tillåt cross-origin requests från SvelteKit ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json'); // Sätt svarsformat till JSON

// Hantera preflight-request (webbläsare skickar OPTIONS före POST)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// Standardsvar (blir överskrivit om registreringen lyckas)
$result = [
    'success' => false,
    'message' => 'Kunde inte lägga till användare'
];

// ========== VALIDERING 1: Kontrollera att alla obligatoriska fält finns och inte är tomma ==========
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

// ========== RENSA INDATA: Trimma blanksteg från användarens input ==========
$firstname = trim($_POST['firstname']);
$surname = trim($_POST['surname']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// ========== VALIDERING 2: Kontrollera om användarnamnet redan är registrerat ==========
if ($db->userExists($username)) {
    $result['message'] = 'Användarnamnet är redan taget';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== VALIDERING 3: Kontrollera lösenordets längd (minimum 6 tecken) ==========
if (strlen($password) < 6) {
    $result['message'] = 'Lösenordet måste vara minst 6 tecken långt';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== DATABAS-OPERATION: Försök skapa användaren ==========
// DbEgyTalk::addUser() hashar lösenordet och sparar i databasen
$inserted = $db->addUser($firstname, $surname, $username, $email, $password);

// ========== RESULTAT: Sätt lämpligt svar beroende på om det lyckades ==========
if ($inserted) {
    $result['success'] = true;
    $result['message'] = 'Användare skapad korrekt!';
} else {
    $result['message'] = 'Kunde inte skapa användare (databasfel ?!?)';
}

// Skicka JSON-svar tillbaka till frontend
echo json_encode($result, JSON_UNESCAPED_UNICODE);
