<?php
/**
 * API-ENDPOINT: Hämta vänlista
 * ============================
 * Syftet: Hämta alla vänner för en specifik användare
 * HTTP-metod: GET
 * Indata: uid (användar-ID)
 * Utdata: JSON med lista av användarens vänner
 * SÄKERHETSPROBLEM: Ingen validering av ägarskap - kan hämta andras vänner!
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

// ========== HÄMTA OCH VALIDERA INDATA ==========
// uid: ID på användaren vars vänner vi vill hämta
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$result = ['success' => false, 'message' => 'Missing uid'];

// ========== KONTROLL: Verifiera att uid är giltigt ==========
if ($uid) {
    // ========== DATABAS-OPERATION: Hämta vänner ==========
    $friends = $db->getFriends($uid);

    $result['success'] = true;
    $result['friends'] = $friends;
} else {
    $result['message'] = 'Invalid uid';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>