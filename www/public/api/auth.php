<?php
/**
 * API-ENDPOINT: Logga in användare
 * ================================
 * Syftet: Motta användarnamn och lösenord, validera mot databasen, returnera användardata
 * HTTP-metod: POST
 * Indata: username, password
 * Utdata: JSON med success-status, användardata och meddelande
 * Säkerhet: Använder prepared statements och password_verify()
 */

session_start(); // Starta PHP-session

// ========== CORS-HEADERS: Tillåt cross-origin requests ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true'); // Tillåt credentials (cookies/sessions)

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Acceptera bara POST-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // HTTP 405: Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// Standardsvar (blir överskrivit om login lyckas)
$result = [
    'success' => false,
    'message' => 'Login failed'
];

// ========== VALIDERING: Kontrollera att username och password finns och inte är tomma ==========
if (
    !isset($_POST['username']) || !isset($_POST['password']) ||
    empty(trim($_POST['username'])) || empty(trim($_POST['password']))
) {
    $result['message'] = 'Username and password required';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== HÄMTA OCH RENSA INDATA ==========
$username = trim($_POST['username']);
$password = $_POST['password'];

// ========== AUTENTISERING: Anropa auth-metoden ==========
// Denna metod kollar lösenord med password_verify() (minskar risken för brute-force)
$user = $db->auth($username, $password);

// ========== RESULTAT: Verifiera om autentisering lyckades ==========
if (!empty($user)) {
    // Autentisering lyckades - sätt sessionsdata
    $result['success'] = true;
    $result['message'] = 'Login successful';
    $result['user'] = $user; // Returnera användardata (id, username, email, etc.)
} else {
    // Autentisering misslyckades - returnera generellt felmeddelande (hindra user enumeration)
    $result['message'] = 'Invalid username or password';
}

// Skicka JSON-svar tillbaka till frontend
echo json_encode($result, JSON_UNESCAPED_UNICODE);