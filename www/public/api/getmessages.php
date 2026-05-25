<?php
/**
 * API-ENDPOINT: Hämta meddelanden mellan två användare
 * ====================================================
 * Syftet: Hämta konversationshistorik mellan två användare
 * HTTP-metod: GET
 * Indata: uid (inloggad användare), other_id (andra användaren i konversationen)
 * Utdata: JSON med lista av meddelanden mellan dem
 * SÄKERHETSPROBLEM: Ingen autentisering - kan hämta andras privata meddelanden!
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
// uid: ID på den inloggade användaren
// other_id: ID på den andra användaren i konversationen
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$other = isset($_GET['other_id']) ? intval($_GET['other_id']) : 0;

$result = ['success' => false, 'message' => 'Missing parameters'];

// ========== KONTROLL: Verifiera att båda ID:n är giltiga ==========
if ($uid && $other) {
    // ========== DATABAS-OPERATION: Hämta meddelanden ==========
    // Hämtar alla meddelanden mellan uid och other_id i båda riktningar
    $msgs = $db->getMessagesBetween($uid, $other);

    $result['success'] = true;
    $result['messages'] = $msgs;
} else {
    $result['message'] = 'Invalid parameters';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>