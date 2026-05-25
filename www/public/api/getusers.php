<?php
/**
 * API-ENDPOINT: Hämta alla användare
 * ===================================
 * Syftet: Hämta en lista över alla registrerade användare (för vän-förslag, etc.)
 * HTTP-metod: GET
 * Indata: Ingen
 * Utdata: JSON med lista av alla användare
 * SÄKERHETSPROBLEM: Ingen autentisering - information disclosure (alla kan se alla users)
 */

session_start();

// ========== CORS-HEADERS ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Endast GET-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// Standardsvar
$result = [
    'success' => false,
    'message' => 'Failed to fetch users'
];

// ========== DATABAS-OPERATION: Hämta alla användare ==========
$users = $db->getUsers();

// ========== RESULTAT: Verifiera om det finns några användare ==========
if (!empty($users)) {
    $result['success'] = true;
    $result['message'] = 'Users fetched successfully';
    $result['users'] = $users; // Returnera lista med alla users
} else {
    $result['message'] = 'No users found';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>