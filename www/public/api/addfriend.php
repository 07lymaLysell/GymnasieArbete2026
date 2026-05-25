<?php
/**
 * API-ENDPOINT: Lägg till vän
 * ===========================
 * Syftet: Lägg till en annan användare som vän för den inloggade användaren
 * HTTP-metod: POST
 * Indata: uid (användar-ID), friend_id (väns användar-ID)
 * Utdata: JSON med success-status och meddelande
 * SÄKERHETSPROBLEM: Ingen autentisering - kan adda vän för vilken uid som helst!
 */

session_start();

// ========== CORS-HEADERS ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Endast POST-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// Standardsvar
$result = ['success' => false, 'message' => 'Failed to add friend'];

// ========== HÄMTA OCH VALIDERA INDATA ==========
// uid: ID på den användare som lägger till vän
// friend_id: ID på den användare som ska bli vän
$uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
$fid = isset($_POST['friend_id']) ? intval($_POST['friend_id']) : 0;

// ========== TAB-LOGIK: Kontrollera att båda ID:n är giltiga och inte samma ==========
if ($uid && $fid && $uid !== $fid) {
    // ========== DATABAS-OPERATION: Lägg till vän ==========
    if ($db->addFriend($uid, $fid)) {
        $result['success'] = true;
        $result['message'] = 'Friend added';
    } else {
        $result['message'] = 'Database error';
    }
} else {
    $result['message'] = 'Invalid parameters';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>