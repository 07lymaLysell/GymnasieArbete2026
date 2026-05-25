<?php
/**
 * API-ENDPOINT: Hämta konversationslista
 * ======================================
 * Syftet: Hämta lista över alla aktiva konversationer för en användare
 * HTTP-metod: GET
 * Indata: uid (användar-ID)
 * Utdata: JSON med lista av konversationer (senaste för varje vän)
 * SÄKERHETSPROBLEM: Ingen autentisering - kan hämta andras konversationslista!
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
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$result = ['success' => false, 'message' => 'Missing uid'];

// ========== KONTROLL: Verifiera att uid är giltigt ==========
if ($uid) {
    // ========== DATABAS-OPERATION: Hämta alla konversationer för denna användare ==========
    $convs = $db->getConversations($uid);

    // ========== DEDUPLIKERING: Behåll endast senaste konversation per vän ==========
    // Om det finns flera meddelanden från samma person, visa bara det senaste
    $seen = [];
    $filtered = [];
    foreach ($convs as $c) {
        $other = $c['other_id']; // ID på den andra personen i konversationen
        if (!isset($seen[$other])) {
            $seen[$other] = true;
            $filtered[] = $c;
        }
    }

    $result['success'] = true;
    $result['conversations'] = $filtered;
} else {
    $result['message'] = 'Invalid uid';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>