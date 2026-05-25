<?php
/**
 * API-ENDPOINT: Sök efter användare
 * ==================================
 * Syftet: Sök efter andra användare baserat på sökord
 * HTTP-metod: GET
 * Indata: q (sökterm/query)
 * Utdata: JSON med lista av matchade användare
 * SÄKERHETSPROBLEM: Ingen autentisering, kan söka alla users
 */

session_start();

// ========== HEADERS: JSON-format och CORS ==========
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

// ========== HÄMTA SÖKtERMEN ==========
// Trim för att ta bort whitespace från början och slutet
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$result = ['success' => false, 'message' => 'Missing query'];

// ========== KONTROLL: Verifiera att söktermen inte är tom ==========
if ($search !== '') {
    // ========== DATABAS-OPERATION: Sök efter användare ==========
    // Metoden kallar findUsers() som gör en LIKE-sökning i databasen
    $users = $db->findUsers($search);

    $result['success'] = true;
    $result['users'] = $users;
} else {
    $result['message'] = 'Invalid search word';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>